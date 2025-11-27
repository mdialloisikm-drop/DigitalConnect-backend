<?php

namespace App\Services;

use App\Http\Requests\OrderFormRequest;
use App\Models\Order;
use App\Models\Attachement;
use App\Models\ServiceOffer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class OrderService
{
    /**
     * Liste de toutes les commandes (admin uniquement)
     */
    public function index()
    {
        return Order::with([
            'service.freelance.user',
            'service.category',
            'client.user',
            'deliverables',
            'attachments'
        ])->orderBy('created_at', 'desc')->get();
    }

    /**
     * Mes commandes (client ou freelance)
     */
    public function myOrders()
    {
        $user = auth('api')->user();

        if ($user->user_type === 'client') {
            return Order::where('client_id', $user->client->id)
                ->with([
                    'serviceOffer.service.freelance.user',
                    'serviceOffer.service.category',
                    'serviceOffer.service.images',
                    'client.user',
                    'deliverables',
                    'attachments'
                ])
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif ($user->user_type === 'freelance') {
            // Récupérer les commandes via les services du freelance
            return Order::whereHas('service', function ($query) use ($user) {
                $query->where('freelance_id', $user->freelance->id);
            })
                ->with([
                    'serviceOffer.service.category',
                    'serviceOffer.service.images',
                    'serviceOffer.service.offers',
                    'client.user',
                    'deliverables',
                    'attachments'
                ])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        throw new \Exception('Type d\'utilisateur non autorisé.');
    }

    /**
     * Créer une nouvelle commande
     */
    public function store(OrderFormRequest $request)
    {
        $user = auth('api')->user();

        // Vérifier que l'utilisateur est un client
        if ($user->user_type !== 'client') {
            throw new \Exception('Seuls les clients peuvent passer des commandes.');
        }

        // ✅ Récupérer l'offre sélectionnée (qui contient service_id)
        $serviceOffer = ServiceOffer::with('service')->findOrFail($request->service_offer_id);
        $service = $serviceOffer->service;

        // Vérifier que le service est publié
        if ($service->status !== 'published') {
            throw new \Exception('Ce service n\'est pas disponible.');
        }

        // Vérifier que le client ne commande pas son propre service
        if ($service->freelance->user_id === $user->id) {
            throw new \Exception('Vous ne pouvez pas commander votre propre service.');
        }

        DB::beginTransaction();
        try {
            // Calculer la date de livraison en fonction de l'offre
            $dueDate = Carbon::now()->addDays($serviceOffer->delivery_days);


            $order = Order::create([
                'service_offer_id' => $serviceOffer->id,
                'client_id' => $user->client->id,
                'amount' => $serviceOffer->price,
                'due_date' => $dueDate,
                'requirements' => $request->requirements,
                'status' => 'pending'
            ]);

            // Gérer les pièces jointes si présentes
            if ($request->hasFile('attachments')) {
                $this->storeAttachments($order, $request->file('attachments'), $user->id);
            }

            DB::commit();

            $service = $order->serviceOffer->service;
            Mail::to($service->freelance->user->email)->send(
                new \App\Mail\NewServiceOrderMail($service, $order, $order->client, $service->freelance)
            );

            return $order->load([
                'serviceOffer',
                'serviceOffer.service.freelance.user',
                'serviceOffer.service.category',
                'client.user',
                'attachments'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Afficher une commande spécifique
     */
    public function show(string $id)
    {
        $order = Order::with([
            'service.freelance.user',
            'service.category',
            'service.images',
            'client.user',
            'deliverables',
            'attachments'
        ])->findOrFail($id);

        $user = auth('api')->user();

        // Vérifier les permissions
        if ($user->user_type === 'client' && $order->client_id !== $user->client->id) {
            throw new \Exception('Vous ne pouvez pas voir cette commande.');
        }

        if ($user->user_type === 'freelance' && $order->service->freelance_id !== $user->freelance->id) {
            throw new \Exception('Vous ne pouvez pas voir cette commande.');
        }

        return $order;
    }

    /**
     * Mettre à jour une commande
     */
    public function update(array $data, string $id)
    {
        $order = Order::findOrFail($id);
        $user = auth('api')->user();

        // Seul le client peut modifier les requirements et seulement si la commande est pending
        if ($user->user_type === 'client' && $order->client_id === $user->client->id) {
            if ($order->status !== 'pending') {
                throw new \Exception('Vous ne pouvez modifier que les commandes en attente.');
            }

            $order->update(collect($data)->only(['requirements'])->toArray());

            return $order->load([
                'service.freelance.user',
                'client.user',
                'attachments'
            ]);
        }

        throw new \Exception('Vous ne pouvez pas modifier cette commande.');
    }

    /**
     * Annuler une commande
     */
    public function cancel(string $id)
    {
        $order = Order::findOrFail($id);
        $user = auth('api')->user();

        // Le client peut annuler si pending ou in_progress
        // Le freelance peut annuler si pending
        if ($user->user_type === 'client' && $order->client_id === $user->client->id) {
            if (!in_array($order->status, ['pending', 'in_progress'])) {
                throw new \Exception('Cette commande ne peut plus être annulée.');
            }
        } elseif ($user->user_type === 'freelance' && $order->service->freelance_id === $user->freelance->id) {
            if ($order->status !== 'pending') {
                throw new \Exception('Vous ne pouvez annuler que les commandes en attente.');
            }
        } else {
            throw new \Exception('Vous ne pouvez pas annuler cette commande.');
        }

        $order->update(['status' => 'cancelled']);

        return $order->load([
            'service.freelance.user',
            'client.user'
        ]);
    }

    /**
     * Démarrer une commande (freelance)
     */
    public function startOrder(string $id)
    {
        $order = Order::findOrFail($id);
        $user = auth('api')->user();

        // Vérifier que c'est le freelance du service
        if ($user->user_type !== 'freelance' || $order->service->freelance_id !== $user->freelance->id) {
            throw new \Exception('Seul le freelance assigné peut démarrer cette commande.');
        }

        // Vérifier que la commande est pending
        if ($order->status !== 'pending') {
            throw new \Exception('Cette commande ne peut plus être démarrée.');
        }

        $order->update(['status' => 'in_progress']);

        return $order->load([
            'service',
            'client.user'
        ]);
    }

    /**
     * Livrer une commande (freelance)
     */
    public function deliverOrder(string $id)
    {
        $order = Order::findOrFail($id);
        $user = auth('api')->user();

        // Vérifier que c'est le freelance du service
        if ($user->user_type !== 'freelance' || $order->service->freelance_id !== $user->freelance->id) {
            throw new \Exception('Seul le freelance assigné peut livrer cette commande.');
        }

        // Vérifier que la commande est in_progress ou revision
        if (!in_array($order->status, ['in_progress', 'revision'])) {
            throw new \Exception('Cette commande ne peut pas être livrée.');
        }

        // Vérifier qu'il y a au moins un livrable
        if ($order->deliverables()->count() === 0) {
            throw new \Exception('Vous devez ajouter au moins un livrable avant de livrer la commande.');
        }

        $order->update([
            'status' => 'delivered',
            'delivered_at' => Carbon::now()
        ]);

        return $order->load([
            'service',
            'client.user',
            'deliverables'
        ]);
    }

    /**
     * Accepter la livraison (client)
     */
    public function acceptDelivery(string $id)
    {
        $order = Order::findOrFail($id);
        $user = auth('api')->user();

        // Vérifier que c'est le client de la commande
        if ($user->user_type !== 'client' || $order->client_id !== $user->client->id) {
            throw new \Exception('Seul le client peut accepter la livraison.');
        }

        // Vérifier que la commande est delivered
        if ($order->status !== 'delivered') {
            throw new \Exception('Cette commande n\'est pas encore livrée.');
        }

        $order->update(['status' => 'completed']);

        return $order->load([
            'service.freelance.user',
            'deliverables'
        ]);
    }

    /**
     * Demander une révision (client)
     */
    public function requestRevision(string $id, array $data)
    {
        $order = Order::findOrFail($id);
        $user = auth('api')->user();

        // Vérifier que c'est le client de la commande
        if ($user->user_type !== 'client' || $order->client_id !== $user->client->id) {
            throw new \Exception('Seul le client peut demander une révision.');
        }

        // Vérifier que la commande est delivered
        if ($order->status !== 'delivered') {
            throw new \Exception('Vous ne pouvez demander une révision que pour les commandes livrées.');
        }

        $requirements = $order->requirements . "\n\n--- RÉVISION DEMANDÉE ---\n" . ($data['revision_notes'] ?? 'Révision demandée par le client.');

        $order->update([
            'status' => 'revision',
            'requirements' => $requirements
        ]);

        return $order->load([
            'service.freelance.user',
            'deliverables'
        ]);
    }

    /**
     * Stocker les pièces jointes
     */
    private function storeAttachments(Order $order, array $files, int $uploadedBy)
    {
        foreach ($files as $file) {
            $originalName = $file->getClientOriginalName();
            $path = $file->store('orders/attachments', 'public');

            Attachement::create([
                'attachable_type' => Order::class,
                'attachable_id' => $order->id,
                'uploaded_by' => $uploadedBy,
                'file_type' => 'attachment',
                'file_name' => $originalName,
                'file_path' => $path,
                'format' => 'file',
            ]);
        }
    }
}
