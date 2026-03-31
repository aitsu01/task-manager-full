<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function updateAvatar(Request $request)
{
    $request->validate([
        'avatar' => 'required|image|mimes:jpeg,jpg,png,webp|max:2048',
    ]);

    $user = auth()->user();

    // Cancella vecchio avatar
    if ($user->avatar) {
        \Storage::disk('public')->delete($user->avatar);
    }

    $path = $request->file('avatar')->store('avatars', 'public');

    $user->avatar = $path;
    $user->save();

    return response()->json([
        'avatar' => $path
    ]);
}


public function updateProfile(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . auth()->id(),
    ]);

    $user = auth()->user();
    $user->update($validated);

    return response()->json($user);
}




public function updatePassword(Request $request)
{
    $request->validate([
        'current_password' => 'required',
        'password' => 'required|min:8|confirmed',
    ]);

    $user = $request->user();

    if (!Hash::check($request->current_password, $user->password)) {
        throw ValidationException::withMessages([
            'current_password' => ['Password attuale non corretta.'],
        ]);
    }

    $user->update([
        'password' => Hash::make($request->password)
    ]);

    return response()->json([
        'message' => 'Password aggiornata con successo'
    ]);
}



}
