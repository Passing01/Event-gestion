<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Event;
use App\Models\Panelist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\AdminCreatedMail;

class AdminManagementController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_admins' => User::where('role', 'admin')->count(),
            'total_events' => Event::count(),
            'active_events' => Event::where('status', 'active')->count(),
        ];
        return view('admin.dashboard', compact('stats'));
    }

    public function index()
    {
        $admins = User::where('role', 'admin')->paginate(10);
        return view('admin.admins.index', compact('admins'));
    }

    public function create()
    {
        return view('admin.admins.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
        ]);

        $password = Str::random(12);

        $admin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        Mail::to($admin->email)->send(new AdminCreatedMail($admin, $password));

        return redirect()->route('admin.admins.index')->with('success', 'Administrateur créé avec succès et informations envoyées par mail.');
    }

    public function show($id)
    {
        $admin = User::findOrFail($id);
        return view('admin.admins.show', compact('admin'));
    }

    public function edit($id)
    {
        $admin = User::findOrFail($id);
        return view('admin.admins.edit', compact('admin'));
    }

    public function update(Request $request, $id)
    {
        $admin = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $admin->id,
        ]);

        $admin->update($request->only('name', 'email'));

        return redirect()->route('admin.admins.index')->with('success', 'Administrateur mis à jour.');
    }

    public function toggleStatus($id)
    {
        $admin = User::findOrFail($id);
        $admin->is_active = !$admin->is_active;
        $admin->save();

        return back()->with('success', 'Statut mis à jour.');
    }

    public function resetPassword($id)
    {
        $admin = User::findOrFail($id);
        $password = Str::random(12);
        $admin->password = Hash::make($password);
        $admin->save();

        Mail::to($admin->email)->send(new AdminCreatedMail($admin, $password));

        return back()->with('success', 'Nouveau mot de passe généré et envoyé par mail.');
    }

    public function destroy($id)
    {
        $admin = User::findOrFail($id);
        if ($admin->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }
        $admin->delete();
        return redirect()->route('admin.admins.index')->with('success', 'Administrateur supprimé.');
    }
}
