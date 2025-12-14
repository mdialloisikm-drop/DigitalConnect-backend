<?php

namespace App\Services;

use App\Models\Attachement;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\Order;
use Illuminate\Support\Facades\Storage;

class AttachementService
{
    /**
     * Ajouter des pièces jointes à un projet (Client uniquement)
     */
    public function addProjectAttachments(string $projectId, array $files)
    {
        $user = auth('api')->user();
        $project = Project::findOrFail($projectId);

        // Vérifier que c'est le client propriétaire
        if ($user->user_type !== 'client' || $project->client_id !== $user->client->id) {
            throw new \Exception('Seul le client propriétaire peut ajouter des pièces jointes au projet.');
        }

        return $this->storeAttachments(Project::class, $projectId, $files, $user->id, 'attachment');
    }

    /**
     * Ajouter un lien à un projet (Client uniquement - Attachment)
     */
    public function addProjectLink(string $projectId, array $data)
    {
        $user = auth('api')->user();
        $project = Project::findOrFail($projectId);

        // Vérifier que c'est le client propriétaire
        if ($user->user_type !== 'client' || $project->client_id !== $user->client->id) {
            throw new \Exception('Seul le client propriétaire peut ajouter des liens au projet.');
        }

        return $this->storeLink(Project::class, $projectId, $data, $user->id, 'attachment');
    }

    /**
     * Ajouter un lien comme livrable à un projet (Freelance avec proposition acceptée)
     */
    public function addProjectDeliverableLink(string $projectId, array $data)
    {
        $user = auth('api')->user();
        $project = Project::findOrFail($projectId);

        // Vérifier que le freelance a une proposition acceptée pour ce projet
        if ($user->user_type !== 'freelance') {
            throw new \Exception('Seuls les freelances peuvent ajouter des livrables.');
        }

        $acceptedProposal = Proposal::where('project_id', $projectId)
            ->where('freelance_id', $user->freelance->id)
            ->where('status', 'accepted')
            ->first();

        if (!$acceptedProposal) {
            throw new \Exception('Vous devez avoir une proposition acceptée pour ajouter des livrables à ce projet.');
        }

        return $this->storeLink(Project::class, $projectId, $data, $user->id, 'deliverable');
    }

    /**
     * Ajouter des livrables à un projet (Freelance avec proposition acceptée)
     */
    public function addProjectDeliverables(string $projectId, array $files)
    {
        $user = auth('api')->user();
        $project = Project::findOrFail($projectId);

        // Vérifier que le freelance a une proposition acceptée pour ce projet
        if ($user->user_type !== 'freelance') {
            throw new \Exception('Seuls les freelances peuvent ajouter des livrables.');
        }

        $acceptedProposal = Proposal::where('project_id', $projectId)
            ->where('freelance_id', $user->freelance->id)
            ->where('status', 'accepted')
            ->first();

        if (!$acceptedProposal) {
            throw new \Exception('Vous devez avoir une proposition acceptée pour ajouter des livrables à ce projet.');
        }

        return $this->storeAttachments(Project::class, $projectId, $files, $user->id, 'deliverable');
    }

    /**
     * Ajouter des livrables à une commande (Freelance propriétaire)
     */
    public function addOrderDeliverables(string $orderId, array $files)
    {
        $user = auth('api')->user();
        $order = Order::findOrFail($orderId);

        // Vérifier que c'est le freelance de la commande
        if ($user->user_type !== 'freelance' || $order->service->freelance_id !== $user->freelance->id) {
            throw new \Exception('Seul le freelance assigné peut ajouter des livrables à cette commande.');
        }

        // Vérifier que la commande est en cours
        if (!in_array($order->status, ['in_progress', 'revision'])) {
            throw new \Exception('Vous ne pouvez ajouter des livrables qu\'aux commandes en cours.');
        }

        return $this->storeAttachments(Order::class, $orderId, $files, $user->id, 'deliverable');
    }

    /**
     * Supprimer une pièce jointe ou un livrable
     */
    public function remove(string $attachmentId)
    {
        $user = auth('api')->user();
        $attachment = Attachement::findOrFail($attachmentId);

        // Vérifier les permissions selon le type
        if ($attachment->attachable_type === Project::class) {
            $project = $attachment->attachable;

            // Si c'est un attachment, seul le client propriétaire peut supprimer
            if ($attachment->file_type === 'attachment') {
                if ($user->user_type !== 'client' || $project->client_id !== $user->client->id) {
                    throw new \Exception('Seul le client propriétaire peut supprimer cette pièce jointe.');
                }
            }

            // Si c'est un deliverable, seul le freelance qui l'a uploadé peut supprimer
            if ($attachment->file_type === 'deliverable') {
                if ($user->id !== $attachment->uploaded_by) {
                    throw new \Exception('Seul le freelance qui a uploadé ce livrable peut le supprimer.');
                }
            }
        }

        if ($attachment->attachable_type === Order::class) {
            $order = $attachment->attachable;

            // Seul le freelance propriétaire peut supprimer
            if ($user->user_type !== 'freelance' || $order->freelance_id !== $user->freelance->id) {
                throw new \Exception('Seul le freelance assigné peut supprimer ce livrable.');
            }
        }

        // Supprimer le fichier du storage si c'est un fichier
        if ($attachment->format === 'file' && $attachment->file_path) {
            //Storage::disk('public')->delete($attachment->file_path);
            Storage:: disk('s3')->delete($attachment->file_path);
        }

        $attachment->delete();

        return ['message' => 'Pièce jointe supprimée avec succès'];
    }

    /**
     * Stocker des fichiers
     */
    private function storeAttachments(string $type, int $id, array $files, int $uploadedBy, string $fileType)
    {
        $folder = $fileType === 'deliverable' ? 'deliverables' : 'attachments';
        $attachments = [];

        foreach ($files as $file) {
            $originalName = $file->getClientOriginalName();
            //$path = $file->store($folder, 'public');
            $path = $file->store($folder, 's3');

            $attachment = Attachement::create([
                'attachable_type' => $type,
                'attachable_id' => $id,
                'uploaded_by' => $uploadedBy,
                'file_type' => $fileType,
                'file_name' => $originalName,
                'file_path' => $path,
                'file_url' => Storage::disk('s3')->url($path), // ✅ Ajouter l'URL S3
                'format' => 'file',
            ]);

            $attachments[] = $attachment;
        }

        return $attachments;
    }

    /**
     * Stocker un lien
     */
    private function storeLink(string $type, int $id, array $data, int $uploadedBy, string $fileType)
    {
        return Attachement::create([
            'attachable_type' => $type,
            'attachable_id' => $id,
            'uploaded_by' => $uploadedBy,
            'file_type' => $fileType,
            'file_name' => $data['name'],
            'url' => $data['url'],
            'format' => 'link',
        ]);
    }
}
