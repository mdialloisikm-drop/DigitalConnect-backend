<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AttachementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CallController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClientDashboardController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\DeviceTokenController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectTaskController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::prefix('v1')->group(function () {


    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
        Route::post('/resend-verification-email', [AuthController::class, 'resendVerificationEmail']);
    });

    // PROJETS OUVERTS - Visible par tous (seulement les projets avec status='open')
    Route::get('projects/open/list', [ProjectController::class, 'openProjects']);

    // Consultation des services (tous peuvent voir)
    Route::apiResource('services', ServiceController::class)->only(['index', 'show']);

    Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
    Route::apiResource('skills', SkillController::class)->only(['index', 'show']);

    Route::middleware('auth:api')->group(function () {

        Route::prefix('conversations')->group(function () {
            Route::get('/', [ConversationController::class, 'index']);
            Route::get('/{id}', [ConversationController::class, 'show']);
            Route::post('/start', [ConversationController::class, 'startConversation']);
            Route::get('/with/{userType}/{userId}', [ConversationController::class, 'getConversationWith']);
            Route::post('/{id}/archive', [ConversationController::class, 'archive']);
            Route::post('/{id}/unarchive', [ConversationController::class, 'unarchive']);
            Route::get('/unread/count', [ConversationController::class, 'unreadCount']);
            Route::delete('/{id}', [ConversationController::class, 'destroy']);
        });

        Route::prefix('messages')->group(function () {
            Route::get('/conversation/{conversationId}', [MessageController::class, 'index']);
            Route::post('/conversation/{conversationId}', [MessageController::class, 'store']);
            Route::post('/conversation/{conversationId}/typing', [MessageController::class, 'typing']);
            Route::put('/{messageId}/read', [MessageController::class, 'markAsRead']);
            Route::put('/conversation/{conversationId}/read-all', [MessageController::class, 'markAllAsRead']);
            Route::delete('/{messageId}', [MessageController::class, 'destroy']);
            Route::get('/conversation/{conversationId}/search', [MessageController::class, 'search']);
        });

        Route::prefix('fcm')->group(function () {
            Route::post('/token', [DeviceTokenController::class, 'store']);
            Route::delete('/token', [DeviceTokenController::class, 'destroy']);
            Route::post('/token/deactivate-all', [DeviceTokenController::class, 'deactivateAll']);
        });

        // Route personnalisée accessible à tous (client et freelance)
        Route::get('orders/my/list', [OrderController::class, 'myOrders']);

        // Consultation d'une commande spécifique (client ou freelance concerné)
        Route::get('orders/{id}', [OrderController::class, 'show']);

        // Consultation des projets (tous peuvent voir)
        Route::apiResource('projects', ProjectController::class)->only(['index', 'show']);

        // Mes contrats (client ou freelance connecté)
        Route::get('/contracts/my-contracts', [ContractController::class, 'myContracts']);

        // Statistiques des contrats
        Route::get('/contracts/statistics', [ContractController::class, 'statistics']);

        // Afficher un contrat spécifique
        Route::get('/contracts/{id}', [ContractController::class, 'show']);

        // Annuler un contrat
        Route::patch('/contracts/{id}/cancel', [ContractController::class, 'cancel']);

        // Créer un litige pour un contrat
        Route::patch('/contracts/{id}/dispute', [ContractController::class, 'dispute']);

        // ========================================
        // ROUTES ADMIN
        // ========================================
        Route::middleware('is.admin')->group(function () {
            Route::prefix('admin/dashboard')->group(function () {
                Route::get('/stats', [AdminDashboardController::class, 'getStatistics']);
                Route::get('/recent-users', [AdminDashboardController::class, 'getRecentUsers']);
                Route::get('/recent-projects', [AdminDashboardController::class, 'getRecentProjects']);
                Route::get('/recent-services', [AdminDashboardController::class, 'getRecentServices']);
                Route::get('/user-type-breakdown', [AdminDashboardController::class, 'getUserTypeBreakdown']);
                Route::get('/project-status-breakdown', [AdminDashboardController::class, 'getProjectStatusBreakdown']);
                Route::get('/service-status-breakdown', [AdminDashboardController::class, 'getServiceStatusBreakdown']);
                Route::get('/monthly-registrations', [AdminDashboardController::class, 'getMonthlyRegistrations']);
                Route::get('/monthly-projects', [AdminDashboardController::class, 'getMonthlyProjects']);
                Route::get('/top-categories', [AdminDashboardController::class, 'getTopCategories']);
                Route::get('/top-skills', [AdminDashboardController::class, 'getTopSkills']);
                Route::get('/overview', [AdminDashboardController::class, 'getOverview']);
            });

            Route::prefix('services')->group(function () {
                Route::get('/pending', [ServiceController::class, 'pendingServices']);
                Route::post('/{id}/approve', [ServiceController::class, 'approve']);
                Route::post('/{id}/reject', [ServiceController::class, 'reject']);
                Route::post('/{id}/archive', [ServiceController::class, 'archive']);
                Route::get('/statistics', [ServiceController::class, 'statistics']);
            });

            // Categories
            Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);

            // Skills
            Route::apiResource('skills', SkillController::class)->except(['index', 'show']);

            Route::prefix('users')->group(function () {
                Route::get('/', [UserController::class, 'index']);
                Route::get('/{id}', [UserController::class, 'show']);
                Route::put('/{id}/status', [UserController::class, 'updateStatus']);
                Route::delete('/{id}', [UserController::class, 'destroy']);
                Route::get('/freelances/list', [UserController::class, 'getFreelances']);
                Route::get('/clients/list', [UserController::class, 'getClients']);
            });

            Route::get('orders', [OrderController::class, 'index']);
            Route::get('/contracts', [ContractController::class, 'index']);

            // MODÉRATION DES PROJETS
            Route::prefix('projects')->group(function () {
                Route::get('/pending', [ProjectController::class, 'pendingProjects']); // Liste des projets en attente
                Route::post('/{id}/approve', [ProjectController::class, 'approve']); // Approuver un projet
                Route::post('/{id}/reject', [ProjectController::class, 'reject']); // Rejeter un projet
                Route::post('/{id}/archive', [ProjectController::class, 'archive']); // Archiver un projet
                Route::get('/statistics', [ProjectController::class, 'statistics']); // Statistiques
            });

            // Résoudre un litige de contrat
            Route::patch('/contracts/{id}/resolve-dispute', [ContractController::class, 'resolveDispute']);
        });

        // ========================================
        // ROUTES CLIENT
        // ========================================
        Route::middleware('is.client')->group(function () {
            // Mes projets
            Route::get('projects/my/list', [ProjectController::class, 'myProjects']);

            // CRUD des projets
            Route::apiResource('projects', ProjectController::class)->except(['index', 'show']);
            Route::post('projects/{id}/skills', [ProjectController::class, 'attachSkills']);
            Route::delete('projects/{id}/skills', [ProjectController::class, 'detachSkills']);
            Route::get('projects/{id}/proposals', [ProjectController::class, 'proposals']);
            Route::post('projects/{id}/archive', [ProjectController::class, 'archive']);

            // Gestion des tâches de projet (CLIENT)
            Route::prefix('projects/{project}')->group(function () {
                Route::get('tasks', [ProjectTaskController::class, 'index']);
                Route::get('tasks/by-priority', [ProjectTaskController::class, 'indexByPriority']);
                Route::post('tasks', [ProjectTaskController::class, 'store']);
                Route::post('tasks/reorder', [ProjectTaskController::class, 'reorder']);
                Route::get('tasks/statistics', [ProjectTaskController::class, 'statistics']);
            });

            // Gestion d'une tâche spécifique (CLIENT)
            Route::prefix('tasks/{task}')->group(function () {
                Route::get('/', [ProjectTaskController::class, 'show']);
                Route::put('/', [ProjectTaskController::class, 'update']);
                Route::patch('/', [ProjectTaskController::class, 'update']);
                Route::delete('/', [ProjectTaskController::class, 'destroy']);
                Route::post('priority', [ProjectTaskController::class, 'changePriority']);
            });

            // Propositions
            Route::post('proposals/{id}/accept', [ProposalController::class, 'accept']);
            Route::post('proposals/{id}/reject', [ProposalController::class, 'reject']);
            Route::get('proposals/received', [ProposalController::class, 'receivedProposals']);

            // GESTION DES PIÈCES JOINTES (Client)
            Route::post('projects/{id}/attachments', [AttachementController::class, 'addProjectAttachments']);
            Route::post('projects/{id}/attachments/link', [AttachementController::class, 'addProjectLink']);

            // Commandes
            Route::post('orders', [OrderController::class, 'store']);
            Route::put('orders/{id}', [OrderController::class, 'update']);
            Route::post('orders/{id}/cancel', [OrderController::class, 'cancel']);
            Route::post('orders/{id}/accept', [OrderController::class, 'acceptDelivery']);
            Route::post('orders/{id}/revision', [OrderController::class, 'requestRevision']);

            // Contrats
            Route::patch('/contracts/{id}/complete', [ContractController::class, 'complete']);

            // Dashboard Client
            Route::prefix('client/dashboard')->group(function () {
                Route::get('/stats', [ClientDashboardController::class, 'getStatistics']);
                Route::get('/recent-projects', [ClientDashboardController::class, 'getRecentProjects']);
                Route::get('/recent-proposals', [ClientDashboardController::class, 'getRecentProposals']);
                Route::get('/project-status-breakdown', [ClientDashboardController::class, 'getProjectStatusBreakdown']);
                Route::get('/monthly-spending', [ClientDashboardController::class, 'getMonthlySpending']);
                Route::get('/overview', [ClientDashboardController::class, 'getOverview']);
            });
        });

        // ========================================
        // ROUTES FREELANCE
        // ========================================
        Route::middleware('is.freelance')->group(function () {
            // Services
            Route::get('services/my/list', [ServiceController::class, 'myServices']);
            Route::post('services', [ServiceController::class, 'store']);
            Route::put('services/{id}', [ServiceController::class, 'update']);
            Route::delete('services/{id}', [ServiceController::class, 'destroy']);
            Route::post('services/{id}/images', [ServiceController::class, 'addImages']);
            Route::delete('services/{id}/images/{imageId}', [ServiceController::class, 'removeImage']);

            // Propositions - Les freelances ne peuvent soumettre des propositions que pour les projets 'open'
            Route::get('proposals/my/list', [ProposalController::class, 'myProposals']);
            Route::post('proposals', [ProposalController::class, 'store']);
            Route::get('proposals/{id}', [ProposalController::class, 'show']);
            Route::put('proposals/{id}', [ProposalController::class, 'update']);
            Route::delete('proposals/{id}', [ProposalController::class, 'destroy']);

            // Gestion des tâches (FREELANCE - changement de statut uniquement)
            Route::prefix('tasks/{task}')->group(function () {
                Route::post('complete', [ProjectTaskController::class, 'complete']);
                Route::post('status', [ProjectTaskController::class, 'changeStatus']);
            });

            // Livrables
            Route::post('projects/{id}/deliverables', [AttachementController::class, 'addProjectDeliverables']);
            Route::post('projects/{id}/deliverables/link', [AttachementController::class, 'addProjectDeliverableLink']);
            Route::post('orders/{id}/deliverables', [AttachementController::class, 'addOrderDeliverables']);
            Route::post('orders/{id}/deliverables/link', [AttachementController::class, 'addOrderDeliverableLink']);

            // Commandes
            Route::post('orders/{id}/start', [OrderController::class, 'startOrder']);
            Route::post('orders/{id}/deliver', [OrderController::class, 'deliverOrder']);
            Route::post('orders/{id}/cancel', [OrderController::class, 'cancel']);
        });

        // ========================================
        // ROUTES AUTH
        // ========================================
        Route::prefix('auth')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/refresh', [AuthController::class, 'refresh']);
        });

        Route::prefix('user')->group(function () {
            Route::put('/profile', [UserController::class, 'updateProfile']);
            Route::put('/change-password', [UserController::class, 'changePassword']);
            Route::delete('/account', [UserController::class, 'deleteAccount']);
        });

        // ========================================
        // APPELS
        // ========================================
        Route::prefix('calls')->group(function () {
            Route::post('/conversation/{conversationId}/initiate', [CallController::class, 'initiateCall']);
            Route::post('/conversation/{conversationId}/answer', [CallController::class, 'answerCall']);
            Route::post('/conversation/{conversationId}/end', [CallController::class, 'endCall']);
            Route::post('/conversation/{conversationId}/reject', [CallController::class, 'rejectCall']);
        });
    });

});
