<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Branch;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('branch')->latest()->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $branches = Branch::all();
        $roles = ['admin' => 'مدير عام', 'branch_manager' => 'مدير فرع', 'cashier' => 'كاشير', 'technician' => 'فني صيانة', 'customer_service' => 'خدمة عملاء'];
        return view('users.create', compact('branches', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20|unique:users,phone',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,branch_manager,cashier,technician,customer_service',
            'branch_id' => 'nullable|exists:branches,id',
            'salary' => 'nullable|numeric|min:0',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'national_id' => 'nullable|string|max:30',
            'emergency_phone' => 'nullable|string|max:20',
            'hire_date' => 'nullable|date',
            'salary_payment_day' => 'nullable|integer|min:1|max:31',
            'salary_type' => 'nullable|in:monthly,daily,commission_only',
        ]);

        $user = User::create([
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role' => $request->input('role'),
            'branch_id' => $request->input('branch_id'),
            'salary' => $request->input('salary', 0),
            'commission_rate' => $request->input('commission_rate', 0),
            'national_id' => $request->input('national_id'),
            'emergency_phone' => $request->input('emergency_phone'),
            'hire_date' => $request->input('hire_date', now()->toDateString()),
            'salary_payment_day' => $request->input('salary_payment_day', 1),
            'salary_type' => $request->input('salary_type', 'monthly'),
            'is_active' => true,
        ]);

        // Sync Spatie role
        $user->assignRole($request->input('role'));

        flash('تمت إضافة الموظف الجديد وتعيين بيانات الراتب والفرع بنجاح.')->success();

        return redirect()->route('users.index');
    }

    public function show($id)
    {
        $user = User::with(['branch', 'adjustments.creator', 'payrolls'])->findOrFail($id);
        return view('users.show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $branches = Branch::all();
        $roles = ['admin' => 'مدير عام', 'branch_manager' => 'مدير فرع', 'cashier' => 'كاشير', 'technician' => 'فني صيانة', 'customer_service' => 'خدمة عملاء'];
        
        return view('users.edit', compact('user', 'branches', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20|unique:users,phone,' . $user->id,
            'email' => 'required|email|max:100|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:admin,branch_manager,cashier,technician,customer_service',
            'branch_id' => 'nullable|exists:branches,id',
            'salary' => 'nullable|numeric|min:0',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'national_id' => 'nullable|string|max:30',
            'emergency_phone' => 'nullable|string|max:20',
            'hire_date' => 'nullable|date',
            'salary_payment_day' => 'nullable|integer|min:1|max:31',
            'salary_type' => 'nullable|in:monthly,daily,commission_only',
            'is_active' => 'required|boolean',
        ]);

        $data = [
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'role' => $request->input('role'),
            'branch_id' => $request->input('branch_id'),
            'salary' => $request->input('salary', 0),
            'commission_rate' => $request->input('commission_rate', 0),
            'national_id' => $request->input('national_id'),
            'emergency_phone' => $request->input('emergency_phone'),
            'hire_date' => $request->input('hire_date'),
            'salary_payment_day' => $request->input('salary_payment_day', 1),
            'salary_type' => $request->input('salary_type', 'monthly'),
            'is_active' => $request->input('is_active'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->input('password'));
        }

        $user->update($data);

        // Sync Spatie role
        $user->syncRoles([$request->input('role')]);

        flash('تم تحديث بيانات وصلاحيات وراتب الموظف بنجاح.')->success();

        return redirect()->route('users.index');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id === auth()->id()) {
            flash('لا يمكنك حذف حسابك الشخصي النشط حالياً.')->error();
            return redirect()->route('users.index');
        }

        $user->delete();

        flash('تم حذف الموظف من النظام بنجاح.')->warning();

        return redirect()->route('users.index');
    }
}
