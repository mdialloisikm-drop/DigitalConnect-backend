<?php

namespace App\Services;

use App\Http\Requests\ProjectFormRequest;
use App\Mail\ProjectApprovedMail;
use App\Models\Project;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProjectService
{
    /**
     * Liste tous les projets (admin uniquement)
     */
    public function index()
    {
        return Project::with([
            'category',
            'client.user',
            'skills',
            'proposals.freelance.user',
            'contract',
            'attachments',
            'tasks'
        ])->orderBy('created_at', 'desc')->get();
    }

    /**
     * Liste les projets ouverts (visibles par les freelances)
     * Seulement les projets avec status = 'open'
     */
    public function getOpenProjects()
    {
        return Project::where('status', 'open')
            ->with([
                'category',
                'client.user',
                'skills',
                'attachments',
                'tasks'
            ])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Liste les projets en attente de modération (admin uniquement)
     */
    public function getPendingProjects()
    {
        return Project::where('status', 'en_attente')
            ->with([
                'category',
                'client.user',
                'skills',
                'attachments',
                'tasks'
            ])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Mes projets (client connecté)
     */
    public function myProjects()
    {
        $user = auth('api')->user();

        if ($user->user_type !== 'client') {
            throw new \Exception('Seuls les clients peuvent accéder à leurs projets.');
        }

        return Project::where('client_id', $user->client->id)
            ->with([
                'category',
                'client.user',
                'skills',
                'proposals.freelance.user',
                'contract',
                'attachments',
                'tasks'
            ])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Créer un projet (client)
     * Le projet est créé avec status = 'en_attente' pour modération
     */
    public function store(ProjectFormRequest $request)
    {
        $user = auth('api')->user();

        // Récupérer les données SANS 'skills' et 'attachments'
        $data = $request->except(['skills', 'attachments', 'deadline', 'start_date', 'status']);
        $data['client_id'] = $user->client->id;
        $data['deadline'] = null;
        $data['start_date'] = null;

        // Forcer le statut à 'en_attente' lors de la création
        $data['status'] = 'en_attente';

        // Créer le projet
        $project = Project::create($data);

        // Attacher les compétences si présentes
        if ($request->has('skills')) {
            $project->skills()->attach($request->skills);
        }

        // Gérer les fichiers uploadés
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $originalName = $file->getClientOriginalName();
                $filePath = $file->store('projects/attachments', 'public');

                $project->attachments()->create([
                    'uploaded_by' => $user->id,
                    'file_type' => 'attachment',
                    'file_name' => $originalName,
                    'file_path' => $filePath,
                    'format' => 'file',
                ]);
            }
        }

        return $project->load([
            'category',
            'client.user',
            'skills',
            'attachments',
            'tasks'
        ]);
    }

    /**
     * Afficher un projet
     */
    public function show(string $id)
    {
        return Project::with([
            'category',
            'client.user',
            'skills',
            'proposals.freelance.user',
            'contract',
            'attachments',
            'tasks'
        ])->findOrFail($id);
    }

    /**
     * Modifier un projet (client propriétaire uniquement)
     * Le client ne peut modifier que les projets en statut 'en_attente'
     */
    public function update(array $data, string $id)
    {
        $project = Project::findOrFail($id);
        $user = auth('api')->user();

        // Vérifier que le projet appartient au client connecté
        if ($user->user_type === 'client' && $project->client_id !== $user->client->id) {
            throw new \Exception('Vous ne pouvez pas modifier ce projet.');
        }

        // Le client ne peut modifier que les projets en attente
        if ($user->user_type === 'client' && $project->status !== 'en_attente') {
            throw new \Exception('Vous ne pouvez modifier que les projets en attente de modération.');
        }

        // Exclure 'skills', 'attachments' et 'status' des données à mettre à jour
        $updateData = collect($data)->except(['skills', 'attachments', 'deadline', 'start_date', 'status'])->toArray();
        $project->update($updateData);

        // Synchroniser les compétences si présentes
        if (isset($data['skills'])) {
            $project->skills()->sync($data['skills']);
        }

        return $project->load([
            'category',
            'client.user',
            'skills',
            'attachments',
            'tasks'
        ]);
    }

    /**
     * Supprimer un projet (client propriétaire uniquement)
     * Le client ne peut supprimer que les projets en statut 'en_attente'
     */
    public function destroy(string $id)
    {
        $project = Project::findOrFail($id);
        $user = auth('api')->user();

        // Vérifier que le projet appartient au client connecté
        if ($user->user_type === 'client' && $project->client_id !== $user->client->id) {
            throw new \Exception('Vous ne pouvez pas supprimer ce projet.');
        }

        // Le client ne peut supprimer que les projets en attente
        if ($user->user_type === 'client' && $project->status !== 'en_attente') {
            throw new \Exception('Vous ne pouvez supprimer que les projets en attente de modération.');
        }

        $project->skills()->detach();
        $project->delete();
    }

    /**
     * Approuver un projet (admin uniquement)
     * Change le statut de 'en_attente' à 'open'
     */
    public function approve(string $id)
    {
        $project = Project::findOrFail($id);

        if ($project->status !== 'en_attente') {
            throw new \Exception('Seuls les projets en attente peuvent être approuvés.');
        }

        $project->update(['status' => 'open']);
        Mail::to($project->client->user->email)->send(
            new ProjectApprovedMail($project, $project->client)
        );

        Log::info('Email de confirmation envoyé: ' . $project->client->user->email);

        return $project->load([
            'category',
            'client.user',
            'skills',
            'attachments',
            'tasks'
        ]);
    }

    /**
     * Rejeter un projet (admin uniquement)
     * Change le statut de 'en_attente' à 'cancelled' avec raison
     */
    public function reject(string $id, ?string $reason = null)
    {
        $project = Project::findOrFail($id);

        if ($project->status !== 'en_attente') {
            throw new \Exception('Seuls les projets en attente peuvent être rejetés.');
        }

        $updateData = ['status' => 'cancelled'];

        // Ajouter la raison de rejet dans la description si fournie
        if ($reason) {
            $project->description .= "\n\n--- RAISON DU REJET ---\n{$reason}";
            $updateData['description'] = $project->description;
        }

        $project->update($updateData);

        return $project->load([
            'category',
            'client.user',
            'skills',
            'attachments',
            'tasks'
        ]);
    }

    /**
     * Archiver un projet (admin ou client propriétaire)
     */
    public function archive(string $id)
    {
        $project = Project::findOrFail($id);
        $user = auth('api')->user();

        // Vérifier les permissions
        if ($user->user_type === 'client' && $project->client_id !== $user->client->id) {
            throw new \Exception('Vous ne pouvez pas archiver ce projet.');
        }

        // Seuls les projets complétés ou annulés peuvent être archivés
        if (!in_array($project->status, ['completed', 'cancelled'])) {
            throw new \Exception('Seuls les projets complétés ou annulés peuvent être archivés.');
        }

        $project->update(['status' => 'archived']);

        return $project->load([
            'category',
            'client.user',
            'skills',
            'attachments',
            'tasks'
        ]);
    }

    /**
     * Obtenir les propositions d'un projet (client propriétaire uniquement)
     */
    public function getProposals(string $id)
    {
        $project = Project::findOrFail($id);
        $user = auth('api')->user();

        // Vérifier que le projet appartient au client connecté
        if ($user->user_type === 'client' && $project->client_id !== $user->client->id) {
            throw new \Exception('Vous ne pouvez pas voir les propositions de ce projet.');
        }

        // Les propositions ne sont visibles que pour les projets 'open' ou 'in_progress'
        if (!in_array($project->status, ['open', 'in_progress'])) {
            throw new \Exception('Les propositions ne sont disponibles que pour les projets ouverts ou en cours.');
        }

        return $project->proposals()->with('freelance.user', 'attachments')->get();
    }

    /**
     * Attacher des compétences (client propriétaire, projet en attente)
     */
    public function attachSkills(string $id, array $skillIds)
    {
        $project = Project::findOrFail($id);
        $user = auth('api')->user();

        // Vérifier que le projet appartient au client connecté
        if ($user->user_type === 'client' && $project->client_id !== $user->client->id) {
            throw new \Exception('Vous ne pouvez pas modifier les compétences de ce projet.');
        }

        // Le client ne peut modifier que les projets en attente
        if ($user->user_type === 'client' && $project->status !== 'en_attente') {
            throw new \Exception('Vous ne pouvez modifier que les projets en attente de modération.');
        }

        $project->skills()->syncWithoutDetaching($skillIds);
        return $project->load('skills');
    }

    /**
     * Détacher des compétences (client propriétaire, projet en attente)
     */
    public function detachSkills(string $id, array $skillIds)
    {
        $project = Project::findOrFail($id);
        $user = auth('api')->user();

        // Vérifier que le projet appartient au client connecté
        if ($user->user_type === 'client' && $project->client_id !== $user->client->id) {
            throw new \Exception('Vous ne pouvez pas modifier les compétences de ce projet.');
        }

        // Le client ne peut modifier que les projets en attente
        if ($user->user_type === 'client' && $project->status !== 'en_attente') {
            throw new \Exception('Vous ne pouvez modifier que les projets en attente de modération.');
        }

        $project->skills()->detach($skillIds);
        return $project->load('skills');
    }

    /**
     * Obtenir les statistiques des projets (admin)
     */
    public function getStatistics()
    {
        return [
            'total' => Project::count(),
            'en_attente' => Project::where('status', 'en_attente')->count(),
            'open' => Project::where('status', 'open')->count(),
            'in_progress' => Project::where('status', 'in_progress')->count(),
            'completed' => Project::where('status', 'completed')->count(),
            'cancelled' => Project::where('status', 'cancelled')->count(),
            'archived' => Project::where('status', 'archived')->count(),
        ];
    }
}
