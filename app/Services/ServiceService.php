<?php

namespace App\Services;

use App\Http\Requests\ServiceFormRequest;
use App\Models\Service;
use App\Models\ServiceImage;
use App\Models\ServiceOffer;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ServiceService
{
    /**
     * Liste de tous les services (admin uniquement)
     */
    public function index()
    {
        return Service::with([
            'freelance.user',
            'category',
            'images',
            'offers'
        ])
            ->orderBy('created_at', 'desc')
            ->paginate(12);
    }

    /**
     * Liste des services publiés (marketplace - visible par tous)
     */
    public function getPublishedServices($perPage = 12)
    {
        return Service::published()
            ->with([
                'freelance.user',
                'category',
                'images',
                'offers'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Liste des services en attente de validation (admin uniquement)
     */
    public function getPendingServices()
    {
        return Service::enAttente()
            ->with([
                'freelance.user',
                'category',
                'images',
                'offers'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    /**
     * Récupérer les services du freelance connecté
     */
    public function myServices($perPage = 12)
    {
        $user = auth('api')->user();

        if ($user->user_type !== 'freelance') {
            throw new \Exception('Seuls les freelances peuvent accéder à leurs services.');
        }

        return Service::where('freelance_id', $user->freelance->id)
            ->with([
                'freelance.user',
                'category',
                'images',
                'offers'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Créer un service (statut automatique : en_attente)
     */
    public function store(ServiceFormRequest $request)
    {
        $user = auth('api')->user();

        DB::beginTransaction();
        try {
            // Créer le service sans les offres et images
            $data = $request->except(['images', 'offers', 'status']);
            $data['freelance_id'] = $user->freelance->id;

            // ✅ Forcer le statut à 'en_attente' lors de la création
            $data['status'] = 'en_attente';

            $service = Service::create($data);

            // Créer les offres
            foreach ($request->input('offers') as $offerData) {
                $service->offers()->create($offerData);
            }

            // Gérer les images si présentes
            if ($request->hasFile('images')) {
                $this->storeImages($service, $request->file('images'));
            }

            DB::commit();

            return $service->load([
                'freelance.user',
                'category',
                'images',
                'offers'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function show(string $id)
    {
        return Service::with([
            'freelance.user',
            'category',
            'images',
            'offers'
        ])->findOrFail($id);
    }

    /**
     * Modifier un service (freelance propriétaire uniquement ET seulement si en_attente)
     */
    public function update(array $data, string $id)
    {
        $service = Service::findOrFail($id);
        $user = auth('api')->user();

        // Vérifier que le service appartient au freelance connecté
        if ($user->user_type === 'freelance' && $service->freelance_id !== $user->freelance->id) {
            throw new \Exception('Vous ne pouvez pas modifier ce service.');
        }

        // ✅ Le freelance ne peut modifier que les services en attente ou rejetés
        if ($user->user_type === 'freelance' && !in_array($service->status, ['en_attente', 'rejected'])) {
            throw new \Exception('Vous ne pouvez modifier que les services en attente ou rejetés.');
        }

        DB::beginTransaction();
        try {
            // Exclure 'offers', 'images' et 'status' des données à mettre à jour
            $updateData = collect($data)->except(['offers', 'images', 'status'])->toArray();

            // ✅ Si le service était rejeté, le repasser en attente
            if ($service->status === 'rejected') {
                $updateData['status'] = 'en_attente';
            }

            $service->update($updateData);

            // Mettre à jour les offres si présentes
            if (isset($data['offers'])) {
                // Supprimer les anciennes offres
                $service->offers()->delete();

                // Créer les nouvelles offres
                foreach ($data['offers'] as $offerData) {
                    $service->offers()->create($offerData);
                }
            }

            DB::commit();

            return $service->load([
                'freelance.user',
                'category',
                'images',
                'offers'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Supprimer un service (freelance propriétaire uniquement ET seulement si en_attente ou rejected)
     */
    public function destroy(string $id)
    {
        $service = Service::findOrFail($id);
        $user = auth('api')->user();

        // Vérifier que le service appartient au freelance connecté
        if ($user->user_type === 'freelance' && $service->freelance_id !== $user->freelance->id) {
            throw new \Exception('Vous ne pouvez pas supprimer ce service.');
        }

        // ✅ Le freelance ne peut supprimer que les services en attente ou rejetés
        if ($user->user_type === 'freelance' && !in_array($service->status, ['en_attente', 'rejected'])) {
            throw new \Exception('Vous ne pouvez supprimer que les services en attente ou rejetés.');
        }

        DB::beginTransaction();
        try {
            // Supprimer les images du storage
            foreach ($service->images as $image) {
                Storage::disk('public')->delete($image->image_path);
                $image->delete();
            }

            // Supprimer les offres (cascade)
            $service->offers()->delete();

            // Supprimer le service
            $service->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * ✅ NOUVEAU : Approuver un service (admin uniquement)
     */
    public function approve(string $id)
    {
        $service = Service::findOrFail($id);

        if ($service->status !== 'en_attente') {
            throw new \Exception('Seuls les services en attente peuvent être approuvés.');
        }

        $service->update(['status' => 'published']);

        return $service->load([
            'freelance.user',
            'category',
            'images',
            'offers'
        ]);
    }

    /**
     * ✅ NOUVEAU : Rejeter un service (admin uniquement)
     */
    public function reject(string $id, ?string $reason = null)
    {
        $service = Service::findOrFail($id);

        if ($service->status !== 'en_attente') {
            throw new \Exception('Seuls les services en attente peuvent être rejetés.');
        }

        DB::beginTransaction();
        try {
            $updateData = ['status' => 'rejected'];

            // Ajouter la raison du rejet dans la description si fournie
            if ($reason) {
                $service->description .= "\n\n--- RAISON DU REJET ---\n{$reason}";
                $updateData['description'] = $service->description;
            }

            $service->update($updateData);

            DB::commit();

            return $service->load([
                'freelance.user',
                'category',
                'images',
                'offers'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * ✅ NOUVEAU : Archiver un service (admin ou freelance propriétaire)
     */
    public function archive(string $id)
    {
        $service = Service::findOrFail($id);
        $user = auth('api')->user();

        // Vérifier les permissions
        if ($user->user_type === 'freelance' && $service->freelance_id !== $user->freelance->id) {
            throw new \Exception('Vous ne pouvez pas archiver ce service.');
        }

        // Seuls les services publiés peuvent être archivés
        if ($service->status !== 'published') {
            throw new \Exception('Seuls les services publiés peuvent être archivés.');
        }

        $service->update(['status' => 'archived']);

        return $service->load([
            'freelance.user',
            'category',
            'images',
            'offers'
        ]);
    }

    /**
     * ✅ NOUVEAU : Obtenir les statistiques des services (admin)
     */
    public function getStatistics()
    {
        return [
            'total' => Service::count(),
            'en_attente' => Service::enAttente()->count(),
            'published' => Service::published()->count(),
            'archived' => Service::archived()->count(),
            'rejected' => Service::rejected()->count(),
        ];
    }

    public function addImages(string $id, array $images)
    {
        $service = Service::findOrFail($id);
        $user = auth('api')->user();

        // Vérifier que le service appartient au freelance connecté
        if ($user->user_type === 'freelance' && $service->freelance_id !== $user->freelance->id) {
            throw new \Exception('Vous ne pouvez pas ajouter des images à ce service.');
        }

        // ✅ Permettre l'ajout d'images seulement si en_attente ou rejected
        if ($user->user_type === 'freelance' && !in_array($service->status, ['en_attente', 'rejected'])) {
            throw new \Exception('Vous ne pouvez ajouter des images qu\'aux services en attente ou rejetés.');
        }

        $this->storeImages($service, $images);
        return $service->load(['images', 'offers']);
    }

    public function removeImage(string $serviceId, string $imageId)
    {
        $service = Service::findOrFail($serviceId);
        $user = auth('api')->user();

        // Vérifier que le service appartient au freelance connecté
        if ($user->user_type === 'freelance' && $service->freelance_id !== $user->freelance->id) {
            throw new \Exception('Vous ne pouvez pas supprimer des images de ce service.');
        }

        // ✅ Permettre la suppression d'images seulement si en_attente ou rejected
        if ($user->user_type === 'freelance' && !in_array($service->status, ['en_attente', 'rejected'])) {
            throw new \Exception('Vous ne pouvez supprimer des images que des services en attente ou rejetés.');
        }

        $image = ServiceImage::where('service_id', $serviceId)
            ->where('id', $imageId)
            ->firstOrFail();

        // ✅ CHANGEMENT: 'public' -> 's3'
        Storage::disk('s3')->delete($image->image_path);
        //Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return $service->load(['images', 'offers']);
    }

    private function storeImages(Service $service, array $images)
    {
        foreach ($images as $image) {
            // ✅ CHANGEMENT: 'public' -> 's3'
            $path = $image->store('services', 's3');
            //$path = $image->store('services', 'public');

            ServiceImage::create([
                'service_id' => $service->id,
                'image_path' => $path,
                'image_url' => Storage::disk('s3')->url($path), // ✅ Ajouter l'URL S3
            ]);
        }
    }
}
