<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Branch;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::latest()->get();
        return view('branches.index', compact('branches'));
    }

    public function create()
    {
        return view('branches.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:branches,name',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        Branch::create($request->only('name', 'phone', 'address'));

        flash('تم إنشاء الفرع الجديد بنجاح.')->success();

        return redirect()->route('branches.index');
    }

    public function show($id)
    {
        $branch = Branch::findOrFail($id);
        return view('branches.show', compact('branch'));
    }

    public function edit($id)
    {
        $branch = Branch::findOrFail($id);
        return view('branches.edit', compact('branch'));
    }

    public function update(Request $request, $id)
    {
        $branch = Branch::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100|unique:branches,name,' . $branch->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $branch->update($request->only('name', 'phone', 'address'));

        flash('تم تحديث بيانات الفرع بنجاح.')->success();

        return redirect()->route('branches.index');
    }

    public function destroy($id)
    {
        $branch = Branch::findOrFail($id);
        
        // Prevent deletion of Main branch (e.g. branch ID 1)
        if ($branch->id == 1) {
            flash('لا يمكن حذف الفرع الرئيسي الافتراضي للنظام.')->error();
            return redirect()->route('branches.index');
        }

        $branch->delete();

        flash('تم حذف الفرع بنجاح.')->warning();

        return redirect()->route('branches.index');
    }
}
