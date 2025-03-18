<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WidgetChatController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the RouteServiceProvider within a group
| assigned to the "api" middleware group. 
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('widget')->group(function () {
    // 1) Start a new chat (includes creation of an OpenAI thread_id if configured)
    Route::post('/chats', [WidgetChatController::class, 'startChat']);

    // 2) Post a user message, also triggers AI if configured
    Route::post('/chats/{chatId}/messages', [WidgetChatController::class, 'storeMessage'])
         ->where('chatId', '[0-9]+');

    // 3) Poll or retrieve all messages for a given chat
    Route::get('/chats/{chatId}/messages', [WidgetChatController::class, 'getMessages'])
         ->where('chatId', '[0-9]+');

    // 4) Transfer chat to human support
    Route::post('/chats/{chatId}/transfer', [WidgetChatController::class, 'transferToHuman'])
         ->where('chatId', '[0-9]+');
});