<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Customer;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query()->with('branch');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->latest()->get();

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20|unique:customers,phone',
            'secondary_phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'branch_id' => 'nullable|exists:branches,id',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if (empty($validated['branch_id'])) {
            $validated['branch_id'] = auth()->user()->branch_id ?? 1;
        }

        $customer = Customer::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل العميل الجديد بنجاح.',
                'customer' => $customer
            ]);
        }

        flash('تم تسجيل العميل الجديد بنجاح.')->success();

        return redirect()->route('customers.index');
    }

    public function show($id)
    {
        $customer = Customer::with(['branch', 'sales', 'maintenanceRequests'])->findOrFail($id);
        return view('customers.show', compact('customer'));
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20|unique:customers,phone,' . $customer->id,
            'secondary_phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'branch_id' => 'required|exists:branches,id',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $customer->update($validated);

        flash('تم تحديث بيانات العميل بنجاح.')->success();

        return redirect()->route('customers.index');
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        flash('تم حذف العميل بنجاح.')->warning();

        return redirect()->route('customers.index');
    }
}
