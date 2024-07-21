<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    //this method will show permissions page
    public function index()
    {
        $permissions = Permission::orderBy('created_at', 'DESC')->paginate(10);
        return view('permissions.list', ['permissions' => $permissions]);
    }


    // this metod will show create permission page
    public function create()
    {

        return view('permissions.create');
    }

    // ?this will store a permission in DB
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:permissions|min:3'
        ]);

        if ($validator->passes()) {
            Permission::create(['name' => $request->name]);
            return redirect()->route('permissions.index')->with('success', 'Permission added successfully');
        } else {
            return redirect()->route('permissions.create')->withInput()->withErrors($validator);
        }
    }


    // ?this will edit a permission in DB
    public function edit($id)
    {
        $permission = Permission::findOrFail($id);

        return view('permissions.edit', [
            'permission' => $permission
        ]);
    }


    // ?this will update a permission in page
    public function update($id, Request $request)
    {
        $permission = Permission::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:permissions,name,' . $id . ',id'
        ]);

        if ($validator->passes()) {
            // Permission::create(['name' => $request->name]);
            $permission->name = $request->name;
            $permission->save();


            return redirect()->route('permissions.index')->with('success', 'Permission updated successfully');
        } else {
            return redirect()->route('permissions.edit', $id)->withInput()->withErrors($validator);
        }
    }


    // ?this will delete a permission in DB
    public function destroy(Request $request)
    {
        $id = $request->id;
        $permission = Permission::find($id);

        if ($permission == null) {
            session()->flash('error', 'Permission not found');
            return response()->json([
                'status' => false
            ]);
        }

        $permission->delete();

        session()->flash('success', 'Permission deleted successfully');
        return response()->json([
            'status' => true
        ]);
    }
}