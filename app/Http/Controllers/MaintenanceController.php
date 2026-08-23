<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\MaintenanceRequest;
use App\Models\Customer;
use App\Models\User;
use App\Models\MaintenanceStatusLog;
use App\Models\MaintenanceSparePart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $query = MaintenanceRequest::with(['customer', 'technician']);

        // Technician view constraint
        if (Auth::user()->role === 'technician') {
            $query->where('technician_id', Auth::id());
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('device_serial', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($sub) use ($search) {
                      $sub->where('name', 'like', "%{$search}%")
                          ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $requests = $query->latest()->get();

        return view('maintenance.index', compact('requests'));
    }

    public function create()
    {
        $customers = Customer::all();
        $technicians = User::where('role', 'technician')->where('is_active', true)->get();
        return view('maintenance.create', compact('customers', 'technicians'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'technician_id' => 'nullable|exists:users,id',
            'device_type' => 'required|string|max:50',
            'device_model' => 'required|string|max:100',
            'device_serial' => 'nullable|string|max:100',
            'problem_description' => 'required|string',
            'pre_repair_checklist' => 'nullable|array',
            'estimated_cost' => 'nullable|numeric|min:0',
            'advance_payment' => 'nullable|numeric|min:0',
            'priority' => 'required|in:low,normal,high,urgent',
            'estimated_delivery' => 'nullable|date',
        ]);

        $validated['branch_id'] = Auth::user()->branch_id ?? 1;
        $validated['ticket_number'] = 'MNT-' . date('Ymd') . '-' . str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT);
        $validated['status'] = 'received';
        $validated['pre_repair_checklist'] = $request->input('pre_repair_checklist', []);

        DB::transaction(function () use ($validated) {
            $maintenance = MaintenanceRequest::create($validated);

            // Log initial status
            MaintenanceStatusLog::create([
                'maintenance_request_id' => $maintenance->id,
                'old_status' => null,
                'new_status' => 'received',
                'notes' => 'تم استلام الجهاز وفتح تذكرة الصيانة بنجاح.',
                'changed_by' => Auth::id(),
            ]);
        });

        flash('تم تسجيل طلب الصيانة الجديد بنجاح.')->success();

        return redirect()->route('maintenance.index');
    }

    public function show($id)
    {
        $request = MaintenanceRequest::with(['customer', 'technician', 'statusLogs.changedBy', 'spareParts.product'])->findOrFail($id);
        $technicians = User::where('role', 'technician')->where('is_active', true)->get();
        $products = Product::whereHas('category', function ($q) {
            $q->where('name', 'like', '%غيار%')
              ->orWhere('name', 'like', '%قطع%');
        })->get();

        return view('maintenance.show', compact('request', 'technicians', 'products'));
    }

    public function edit($id)
    {
        $request = MaintenanceRequest::findOrFail($id);
        $customers = Customer::all();
        $technicians = User::where('role', 'technician')->where('is_active', true)->get();
        return view('maintenance.edit', compact('request', 'customers', 'technicians'));
    }

    public function update(Request $request, $id)
    {
        $maintenance = MaintenanceRequest::findOrFail($id);

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'technician_id' => 'nullable|exists:users,id',
            'device_type' => 'required|string|max:50',
            'device_model' => 'required|string|max:100',
            'device_serial' => 'nullable|string|max:100',
            'problem_description' => 'required|string',
            'diagnosis' => 'nullable|string',
            'pre_repair_checklist' => 'nullable|array',
            'estimated_cost' => 'nullable|numeric|min:0',
            'final_cost' => 'nullable|numeric|min:0',
            'advance_payment' => 'nullable|numeric|min:0',
            'priority' => 'required|in:low,normal,high,urgent',
            'estimated_delivery' => 'nullable|date',
        ]);

        $validated['pre_repair_checklist'] = $request->input('pre_repair_checklist', []);

        $maintenance->update($validated);

        flash('تم تحديث بيانات تذكرة الصيانة بنجاح.')->success();

        return redirect()->route('maintenance.index');
    }

    public function destroy($id)
    {
        $maintenance = MaintenanceRequest::findOrFail($id);
        $maintenance->delete();

        flash('تم حذف التذكرة بنجاح.')->warning();

        return redirect()->route('maintenance.index');
    }

    public function updateStatus(Request $request, $id)
    {
        $maintenance = MaintenanceRequest::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:received,diagnosed,waiting_parts,in_progress,completed,delivered,cancelled',
            'notes' => 'nullable|string',
        ]);

        $oldStatus = $maintenance->status;
        $newStatus = $request->input('status');

        DB::transaction(function () use ($maintenance, $oldStatus, $newStatus, $request) {
            $updateData = ['status' => $newStatus];
            if ($newStatus === 'delivered') {
                $updateData['delivered_at'] = now();
            }
            $maintenance->update($updateData);

            MaintenanceStatusLog::create([
                'maintenance_request_id' => $maintenance->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'notes' => $request->input('notes') ?? 'تغيير حالة الجهاز يدوياً.',
                'changed_by' => Auth::id(),
            ]);
        });

        flash('تم تحديث حالة تذكرة الصيانة بنجاح.')->success();

        return redirect()->route('maintenance.show', $id);
    }

    public function assign(Request $request, $id)
    {
        $maintenance = MaintenanceRequest::findOrFail($id);
        
        $request->validate([
            'technician_id' => 'required|exists:users,id',
        ]);

        $techId = $request->input('technician_id');
        $tech = User::findOrFail($techId);

        $maintenance->update(['technician_id' => $techId]);

        MaintenanceStatusLog::create([
            'maintenance_request_id' => $maintenance->id,
            'old_status' => $maintenance->status,
            'new_status' => $maintenance->status,
            'notes' => 'تم تعيين فني الصيانة: ' . $tech->name,
            'changed_by' => Auth::id(),
        ]);

        flash('تم إسناد التذكرة للفني بنجاح.')->success();

        return redirect()->route('maintenance.show', $id);
    }
}
