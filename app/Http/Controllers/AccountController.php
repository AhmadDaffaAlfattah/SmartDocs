<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $searchQuery = $request->get('search', '');
        $perPage = $request->get('per_page', 10);

        $query = User::query();

        if ($searchQuery) {
            $query->where('name', 'like', "%{$searchQuery}%")
                ->orWhere('email', 'like', "%{$searchQuery}%")
                ->orWhere('bidang', 'like', "%{$searchQuery}%");
        }

        $accounts = $query->paginate($perPage);

        $bidangs = ['Engineering', 'Business Support', 'Operasi', 'Pemeliharaan', 'Keamanan', 'Lingkungan', 'Mesin'];
        $roles = ['super_admin', 'admin', 'user'];

        return view('account.index', [
            'accounts' => $accounts,
            'searchQuery' => $searchQuery,
            'perPage' => $perPage,
            'bidangs' => $bidangs,
            'roles' => $roles,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $bidangs = ['Engineering', 'Business Support', 'Operasi', 'Pemeliharaan', 'Keamanan', 'Lingkungan', 'Mesin'];
        $roles = ['super_admin', 'admin', 'user'];

        return view('account.create', [
            'bidangs' => $bidangs,
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'bidang' => 'nullable|string',
            'role' => 'required|in:super_admin,admin,user',
        ]);

        User::create($validated);

        return redirect()->route('account.index')->with('success', 'Akun berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $account)
    {
        return view('account.show', compact('account'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $account)
    {
        $bidangs = ['Engineering', 'Business Support', 'Operasi', 'Pemeliharaan', 'Keamanan', 'Lingkungan', 'Mesin'];
        $roles = ['super_admin', 'admin', 'user'];

        return view('account.edit', [
            'account' => $account,
            'bidangs' => $bidangs,
            'roles' => $roles,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $account)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $account->id,
            'password' => 'nullable|string|min:6',
            'bidang' => 'nullable|string',
            'role' => 'required|in:super_admin,admin,user',
        ]);

        if (!$validated['password']) {
            unset($validated['password']);
        }

        $account->update($validated);

        return redirect()->route('account.index')->with('success', 'Akun berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $account)
    {
        $account->delete();
        return redirect()->route('account.index')->with('success', 'Akun berhasil dihapus');
    }

    /**
     * Inline edit for modal
     */
    public function getAccount(User $account)
    {
        return response()->json($account);
    }

    /**
     * Inline update from modal
     */
    public function updateInline(Request $request, User $account)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $account->id,
            'password' => 'nullable|string|min:6',
            'bidang' => 'nullable|string',
            'role' => 'required|in:super_admin,admin,user',
        ]);

        if (!$validated['password']) {
            unset($validated['password']);
        }

        $account->update($validated);

        return response()->json(['message' => 'Akun berhasil diperbarui', 'data' => $account]);
    }
}
