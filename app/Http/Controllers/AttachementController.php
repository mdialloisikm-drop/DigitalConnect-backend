<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Project;
use App\Services\AttachementService;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class AttachementController extends Controller
{
    protected $attachementService;

    public function __construct(AttachementService $attachementService)
    {
        $this->attachementService = $attachementService;
    }

    /**
     * Ajouter des pièces jointes à un projet (Client)
     */
    public function addProjectAttachments(Request $request, string $projectId)
    {
        $request->validate([
            'attachments' => 'required|array|max:10',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png,zip,rar,xls,xlsx|max:10240', // 10MB
        ]);

        try {
            $attachments = $this->attachementService->addProjectAttachments($projectId, $request->file('attachments'));
            return response()->json([
                'message' => 'Pièces jointes ajoutées avec succès',
                'attachments' => $attachments
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Ajouter un lien à un projet (Client)
     */
    public function addProjectLink(Request $request, string $projectId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:500',
        ]);

        try {
            $link = $this->attachementService->addProjectLink($projectId, $request->only(['name', 'url']));
            return response()->json([
                'message' => 'Lien ajouté avec succès',
                'attachment' => $link
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Ajouter des livrables à un projet (Freelance avec proposition acceptée)
     */
    public function addProjectDeliverables(Request $request, string $projectId)
    {
        $request->validate([
            'deliverables' => 'required|array|max:10',
            'deliverables.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png,zip,rar,psd,ai,fig|max:51200', // 50MB
        ]);

        try {
            $deliverables = $this->attachementService->addProjectDeliverables($projectId, $request->file('deliverables'));
            $notificationService = app(NotificationService::class);
            $project = Project::findOrFail($projectId);
            $freelance = auth()->user()->freelance;
            $client = $project->client;

            $notificationService->notifyDeliverableUploaded($client, $project, $freelance);
            return response()->json([
                'message' => 'Livrables ajoutés avec succès',
                'deliverables' => $deliverables
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Ajouter un lien comme livrable à un projet (Freelance)
     */
    public function addProjectDeliverableLink(Request $request, string $projectId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:500',
        ]);

        try {
            $link = $this->attachementService->addProjectDeliverableLink($projectId, $request->only(['name', 'url']));
            $notificationService = app(NotificationService::class);
            $project = Project::findOrFail($projectId);
            $freelance = auth()->user()->freelance;
            $client = $project->client;

            $notificationService->notifyDeliverableUploaded($client, $project, $freelance);
            return response()->json([
                'message' => 'Lien livrable ajouté avec succès',
                'deliverable' => $link
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Ajouter des livrables à une commande (Freelance)
     */
    public function addOrderDeliverables(Request $request, string $orderId)
    {
        $request->validate([
            'deliverables' => 'required|array|max:10',
            'deliverables.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png,zip,rar,psd,ai,fig|max:51200', // 50MB
        ]);

        try {
            $deliverables = $this->attachementService->addOrderDeliverables($orderId, $request->file('deliverables'));
            $notificationService = app(NotificationService::class);
            $order = Order::findOrFail($orderId);
            $freelance = auth()->user()->freelance;
            $client = $order->client;

            $notificationService->notifyDeliverableUploaded($client, $order, $freelance);
            return response()->json([
                'message' => 'Livrables ajoutés avec succès',
                'deliverables' => $deliverables
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Supprimer une pièce jointe ou un livrable
     */
    public function remove(string $id)
    {
        try {
            $result = $this->attachementService->remove($id);
            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }
}
