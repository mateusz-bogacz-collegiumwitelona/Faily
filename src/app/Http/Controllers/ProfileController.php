<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Services\Interfaces\UserServiceInterface;
use App\Models\User;

class ProfileController extends Controller
{

    protected $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    public function show()
    {
        $user = Auth::user();
        return view('account', compact('user'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,'.Auth::id(),
            'phone' => 'nullable|string|max:20',
            'age' => 'nullable|integer|min:1|max:120',
            'description' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'preferred_language' => 'nullable|string|in:en,pl,de,uk',
            'theme' => 'nullable|string|in:light,dark',
        ]);

        try {
            $this->userService->updateProfile($validated, Auth::id(), $request->hasFile('avatar') ? $request->file('avatar') : null);
            return redirect()->route('profile.show.blade.php')->with('success', 'Profil został zaktualizowany.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function editPhoto()
    {
        return view('profile.edit-photo', ['user' => Auth::user()]);
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|max:2048',
        ]);

        try {
            $this->userService->updatePhoto($request->file('photo'), Auth::id());
            return redirect()->route('profile.show.blade.php')->with('success', 'Zdjęcie profilowe zostało zaktualizowane.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function toggle2FA(Request $request)
    {
        try {
            $this->userService->toggle2FA($request->input('value'), Auth::id());
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        try {
            $this->userService->deleteAccount($request->password, Auth::id());

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return Redirect::to('/');
        } catch (\Exception $e) {
            return back()->withErrors(['password' => $e->getMessage()]);
        }
    }

    public function showUserProfile(User $user)
    {
        $isOwnProfile = Auth::check() && Auth::id() === $user->id;

        if ($isOwnProfile) {
            return redirect('/account');
        }

        return view('account', compact('user'));
    }
}
