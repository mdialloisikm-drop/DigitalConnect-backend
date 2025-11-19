<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderFormRequest;
use App\Services\NotificationService;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Liste de toutes les commandes (admin uniquement)
     */
    public function index()
    {
        try {
            $orders = $this->orderService->index();
            return response()->json($orders, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Mes commandes (client ou freelance)
     */
    public function myOrders()
    {
        try {
            $orders = $this->orderService->myOrders();
            return response()->json($orders, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Créer une nouvelle commande
     */
    public function store(OrderFormRequest $request)
    {
        try {
            $order = $this->orderService->store($request);
            $notificationService = app(NotificationService::class);
            $service = $order->service;
            $freelance = $service->freelance;
            $client = auth()->user()->client;

            $notificationService->notifyServiceOrdered($freelance, $service, $client);
            return response()->json([
                'message' => 'Commande créée avec succès',
                'order' => $order
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Afficher une commande spécifique
     */
    public function show(string $id)
    {
        try {
            $order = $this->orderService->show($id);
            return response()->json($order, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Mettre à jour une commande
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'requirements' => 'nullable|string|max:2000',
        ]);

        try {
            $order = $this->orderService->update($request->all(), $id);
            return response()->json([
                'message' => 'Commande modifiée avec succès',
                'order' => $order
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Annuler une commande
     */
    public function cancel(string $id)
    {
        try {
            $order = $this->orderService->cancel($id);
            return response()->json([
                'message' => 'Commande annulée avec succès',
                'order' => $order
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Démarrer une commande (freelance)
     */
    public function startOrder(string $id)
    {
        try {
            $order = $this->orderService->startOrder($id);
            return response()->json([
                'message' => 'Commande démarrée',
                'order' => $order
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Livrer une commande (freelance)
     */
    public function deliverOrder(string $id)
    {
        try {
            $order = $this->orderService->deliverOrder($id);
            return response()->json([
                'message' => 'Commande livrée avec succès',
                'order' => $order
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Accepter la livraison (client)
     */
    public function acceptDelivery(string $id)
    {
        try {
            $order = $this->orderService->acceptDelivery($id);
            return response()->json([
                'message' => 'Livraison acceptée, commande complétée',
                'order' => $order
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * Demander une révision (client)
     */
    public function requestRevision(Request $request, string $id)
    {
        $request->validate([
            'revision_notes' => 'nullable|string|max:1000',
        ]);

        try {
            $order = $this->orderService->requestRevision($id, $request->all());
            return response()->json([
                'message' => 'Révision demandée',
                'order' => $order
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }
}
