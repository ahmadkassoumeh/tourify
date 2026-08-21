<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Enums\UserStatusEnum;
use Illuminate\Support\Facades\Auth;
use App\Models\Apartment;

class LoginWebController extends Controller
{
    // في AdminController
public function index2(Request $request)
{
    $query = User::with('roles')
        ->whereDoesntHave('roles', function ($roleQuery) {
            $roleQuery->where('name', 'admin');
        })
        ->orderBy('created_at', 'desc');

    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    if ($request->filled('search')) {

        $search = $request->search;

        $query->where(function ($q) use ($search) {

            $q->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('username', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone_number', 'like', "%{$search}%");

        });
    }

    /*
    |--------------------------------------------------------------------------
    | Role Filter
    |--------------------------------------------------------------------------
    */

    if ($request->filled('role')) {

        $query->whereHas('roles', function ($roleQuery) use ($request) {
            $roleQuery->where('name', $request->role);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Status Filter
    |--------------------------------------------------------------------------
    */

    if ($request->filled('status')) {

        $query->where('status', $request->status);
    }

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

    $users = $query
        ->paginate(15)
        ->withQueryString();


    /*
    |--------------------------------------------------------------------------
    | Statistics
    |--------------------------------------------------------------------------
    */

    $totalUsers = User::whereDoesntHave('roles', function ($q) {
        $q->where('name', 'admin');
    })->count();

    $usersCount = User::role('user')->count();

    $agenciesCount = User::role('agency')->count();

    $airlinesCount = User::role('airline')->count();

    $pendingCount = User::where('status', UserStatusEnum::PENDING)
        ->whereDoesntHave('roles', function ($q) {
            $q->where('name', 'admin');
        })
        ->count();

    $approvedCount = User::where('status', UserStatusEnum::APPROVED)
        ->whereDoesntHave('roles', function ($q) {
            $q->where('name', 'admin');
        })
        ->count();

    return view(
        'admin.users.index',
        compact(
            'users',
            'totalUsers',
            'usersCount',
            'agenciesCount',
            'airlinesCount',
            'pendingCount',
            'approvedCount'
        )
    );
}

public function destroy(User $user)
{
    try {
        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'تم حذف المستخدم بنجاح.');
    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'حدث خطأ أثناء حذف المستخدم.');
    }
}
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => 'بيانات الدخول غير صحيحة',
            ]);
        }

        $user = Auth::user();

        // فقط Admin
        if (! $user->hasRole('admin')) {
            Auth::logout();
            abort(403, 'غير مصرح لك بالدخول');
        }

        return redirect()->route('admin.users.pending');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    

    public function index()
    {
        $users = User::where('status', UserStatusEnum::PENDING)->get();

        return view('admin.users.pending', compact('users'));
    }

    public function approve(User $user)
    {

        $user->update([
            'status' => UserStatusEnum::APPROVED,
        ]);

        return redirect()->back()->with('success', 'تمت الموافقة على المستخدم');
    }

    public function reject(User $user)
    {
        $user->update([
            'status' => UserStatusEnum::REJECTED,
        ]);

        return redirect()->back()->with('success', 'تم رفض المستخدم');
    }

   
    
}
