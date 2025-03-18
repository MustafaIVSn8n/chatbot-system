<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Send a new message to the chat.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $chatId
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request, $chatId)
    {
        $chat = \App\Models\Chat::find($chatId);

        if (!$chat) {
            return response()->json(['message' => 'Chat not found'], 404);
        }

        $message = \App\Models\Message::create([
            'chat_id' => $chatId,
            'user_id' => Auth::id(),
            'sender_type' => 'user',
            'content' => $request->input('content'),
        ]);

        $chat->last_activity_at = now();
        $chat->save();

        return response()->json($message, 201);
    }
}
