<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminWebsitesController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\OpenAIConfigController;
use App\Http\Controllers\WidgetScriptController;
use App\Http\Controllers\AIAssistantController;
use App\Http\Controllers\WebsiteWidgetController;
use App\Http\Controllers\WidgetWelcomeController;
use App\Http\Controllers\WidgetChatController;

// Public homepage
Route::get('/', function () {
    return view('welcome');
});

// Public route: test the chat widget
Route::get('/test-widget', function () {
    return view('test_widget'); // We'll define this Blade file below
})->name('test.widget');

// Public widget routes
Route::prefix('widget')->group(function () {
    // (If you also want these accessible via web, not strictly necessary if in api.php)
    Route::get('/chats/{chatId}/messages', [WidgetChatController::class, 'getMessages'])
         ->where('chatId', '[0-9]+');

    Route::get('/welcome-message/{websiteId}', [WidgetWelcomeController::class, 'getWelcomeMessage'])
         ->where('websiteId', '[0-9]+');
});

// Auth routes
require __DIR__.'/auth.php';

// Protected routes
Route::middleware(['auth'])->group(function () {

    Route::middleware(['verified'])->group(function () {
        // Profile
        Route::prefix('profile')->group(function () {
            Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
            Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
        });

        // Notifications
        Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
            Route::patch('/{id}/markAsRead', [NotificationController::class, 'markAsRead'])
                 ->name('notifications.markAsRead');
            Route::patch('/markAllAsRead', [NotificationController::class, 'markAllAsRead'])
                 ->name('notifications.markAllAsRead');
        });

        // Internal chat routes (admin/staff usage)
        Route::prefix('chats')->group(function () {
            Route::post('/{chat}/messages', [ChatController::class, 'sendMessage'])->name('chats.sendMessage');
        });
    });

    // Super Admin
    Route::group([
        'middleware' => ['verified', 'role:super_admin'],
        'prefix'     => 'super-admin',
        'as'         => 'super_admin.'
    ], function () {
        Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [SuperAdminController::class, 'listAdmins'])->name('users.list');
        Route::get('/users/create', [SuperAdminController::class, 'createAdminForm'])->name('users.create');
        Route::post('/users', [SuperAdminController::class, 'storeAdmin'])->name('users.store');
        Route::get('/users/{id}/edit', [SuperAdminController::class, 'editAdminForm'])->name('users.edit');
        Route::put('/users/{id}', [SuperAdminController::class, 'updateAdmin'])->name('users.update');
        Route::delete('/users/{id}', [SuperAdminController::class, 'destroyAdmin'])->name('users.destroy');

        // Websites
        Route::resource('websites', WebsiteController::class);

        // AI Assistants
        Route::get('/ai-assistants', [AIAssistantController::class, 'index'])->name('ai_assistants.index');
        Route::post('/ai-assistants', [AIAssistantController::class, 'createAssistant'])->name('ai_assistants.create');
        Route::get('/ai-assistants/list', [AIAssistantController::class, 'getAssistants'])->name('ai_assistants.list');
        Route::delete('/ai-assistants/{assistantId}', [AIAssistantController::class, 'deleteAssistant'])
             ->name('ai_assistants.delete');

        // OpenAI Config
        Route::prefix('openai-config')->group(function () {
            Route::get('/', [OpenAIConfigController::class, 'edit'])->name('openai_config.edit');
            Route::put('/', [OpenAIConfigController::class, 'update'])->name('openai_config.update');
        });
    });

    // Admin
    Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'role:admin'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // Admin Websites
        Route::resource('websites', AdminWebsitesController::class);

        // Website Widget
        Route::get('/websites/{website}/widget', [WebsiteWidgetController::class, 'edit'])
             ->name('websites.widget.edit');
        Route::match(['post', 'put'], '/websites/{website}/widget/welcome', [WebsiteWidgetController::class, 'updateWelcomeMessage'])
             ->name('websites.widget.welcome.update');
        Route::post('/websites/{website}/widget/buttons', [WebsiteWidgetController::class, 'storeButton'])
             ->name('websites.widget.buttons.store');
        Route::put('/websites/{website}/widget/buttons/reorder', [WebsiteWidgetController::class, 'reorderButtons'])
             ->name('websites.widget.buttons.reorder');
        Route::put('/websites/{website}/widget/buttons/{button}', [WebsiteWidgetController::class, 'updateButton'])
             ->name('websites.widget.buttons.update');
        Route::delete('/websites/{website}/widget/buttons/{button}', [WebsiteWidgetController::class, 'destroyButton'])
             ->name('websites.widget.buttons.destroy');

        // Chat management
        Route::get('/chats', [ChatController::class, 'index'])->name('chats.index');
        Route::get('/chats/{chat}', [ChatController::class, 'show'])->name('chats.show');
        Route::put('/chats/{chat}', [ChatController::class, 'update'])->name('chats.update');
        Route::post('/chats/{chat}/messages', [ChatController::class, 'sendMessage'])->name('chats.sendMessage');
        Route::get('/chats/{chat}/messages', [ChatController::class, 'getMessages'])->name('chats.getMessages');
        Route::get('/chats/list', [ChatController::class, 'getChatList'])->name('chats.list');

        // AI Assistants
        Route::get('/ai-assistants', [AIAssistantController::class, 'index'])->name('ai-assistants.index');
        Route::get('/ai-assistants/create', [AIAssistantController::class, 'create'])->name('ai-assistants.create');
        Route::post('/ai-assistants', [AIAssistantController::class, 'store'])->name('ai-assistants.store');
        Route::get('/ai-assistants/{assistant}/edit', [AIAssistantController::class, 'edit'])->name('ai-assistants.edit');
        Route::put('/ai-assistants/{assistant}', [AIAssistantController::class, 'update'])->name('ai-assistants.update');
        Route::delete('/ai-assistants/{assistant}', [AIAssistantController::class, 'destroy'])->name('ai-assistants.destroy');
    });

    // Agent
    Route::group([
        'middleware' => ['verified', 'role:agent'],
        'prefix'     => 'agent',
        'as'         => 'agent.'
    ], function () {
        Route::get('/dashboard', [AgentController::class, 'dashboard'])->name('dashboard');
    });

    // Test route for admin view of chats
    Route::get('/test-admin-chats', function () {
        return view('admin.chats.index');
    })->name('test.admin.chats');
});

// Fallback route
Route::fallback(function () {
    return view('errors.404');
});