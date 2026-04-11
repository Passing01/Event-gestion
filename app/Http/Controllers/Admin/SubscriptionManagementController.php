<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class SubscriptionManagementController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(15);
        return view('admin.subscriptions.index', compact('users'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'plan' => 'required|in:free,premium,enterprise',
        ]);

        $user->plan = $request->plan;
        $user->save();

        return back()->with('success', 'Plan mis à jour pour ' . $user->name);
    }
}
