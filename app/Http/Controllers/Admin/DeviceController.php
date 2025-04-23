<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Device;
use App\Models\User;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function create()
    {
        return view('admin.admin-pages.create-device');
    }

    public function store(Request $request)
    {
        $request->validate([
            'total_valves' => 'required|integer|min:1|max:10',
            'total_flow_sensors' => 'required|integer|min:1|max:10',
            'total_temp_sensors' => 'required|integer|min:1|max:1',
        ]);

        $year = now()->year;
        $lastDevice = Device::withTrashed()->orderBy('id', 'desc')->first();
        $nextId = $lastDevice ? $lastDevice->id + 1 : 1;
        $deviceNumber = 'SSNM' . $year . 'ESP' . str_pad($nextId, 6, '0', STR_PAD_LEFT);

        $device = Device::create([
            'device_number'        => $deviceNumber,
            'status'               => 'inactive',
            'total_valves'         => $request->total_valves,
            'total_flow_sensors'   => $request->total_flow_sensors,
            'total_temp_sensors'   => $request->total_temp_sensors,
        ]);

        return response()->json([
            'message' => 'Device created successfully.',
            'device'  => $device
        ], 201);
    }

    public function deviceList()
    {
        $devices = Device::with('user')->latest()->get();
        return view('admin.admin-pages.device-list', compact('devices'));
    }

    public function toggleStatus($deviceId)
    {
        $device = Device::findOrFail($deviceId);
        $device->status = $device->status === 'active' ? 'inactive' : 'active'; // Toggle status
        $device->save();

        return response()->json(['message' => 'Device status toggled successfully']);
    }

    public function toggleRepair($deviceId)
    {
        $device = Device::findOrFail($deviceId);
        $device->in_repair = !$device->in_repair; // Toggle repair status
        $device->save();

        return response()->json(['message' => 'Device repair status toggled successfully']);
    }

    public function toggleBlocked($deviceId)
    {
        $device = Device::findOrFail($deviceId);
        $device->is_blocked = !$device->is_blocked; // Toggle blocked status
        $device->save();

        return response()->json(['message' => 'Device blocked status toggled successfully']);
    }

    public function edit($id)
    {
        $device = Device::findOrFail($id);
        $users = Customer::all();

        return view('admin.admin-pages.edit-device', compact('device', 'users'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'device_number' => 'required|string|max:255|unique:devices,device_number,' . $id,
            'customer_id' => 'nullable|exists:customers,id',
            'total_valves' => 'required|integer|min:0|max:10',
            'total_flow_sensors' => 'required|integer|min:0|max:10',
            'total_water_temp_sensors' => 'required|integer|min:1|max:1',
            'status' => 'required|in:active,inactive',
            'in_repair' => 'required|boolean',
            'is_blocked' => 'required|boolean',
        ]);

        $device = Device::findOrFail($id);

        // Fetch user_id from the customer model using the given customer_id
        $userId = null;
        if ($request->filled('customer_id')) {
            $customer = \App\Models\Customer::find($request->customer_id);
            if ($customer) {
                $userId = $customer->user_id;
            }
        }

        $device->update([
            'device_number' => $request->device_number,
            'customer_id' => $request->customer_id,
            'user_id' => $userId,
            'total_valves' => $request->total_valves,
            'total_flow_sensors' => $request->total_flow_sensors,
            'total_water_temp_sensors' => $request->total_water_temp_sensors,
            'status' => $request->status,
            'in_repair' => $request->in_repair,
            'is_blocked' => $request->is_blocked,
        ]);

        return redirect()->route('admin.devices.list')->with('success', 'Device updated successfully.');
    }


    public function destroy(Device $device)
    {
        $device->delete();

        return response()->json([
            'message' => 'Device deleted successfully'
        ]);
    }

    public function trashed()
    {
        $trashedDevices = Device::onlyTrashed()->with('user')->get();
        return view('admin.admin-pages.deleted-device-list', compact('trashedDevices'));
    }


}
