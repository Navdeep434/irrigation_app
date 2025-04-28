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
        $devices = Device::with('customer')->latest()->get();
        return view('admin.admin-pages.device-list', compact('devices'));
    }

    public function toggleStatus($deviceId)
    {
        $device = Device::findOrFail($deviceId);
        $device->status = $device->status === 'active' ? 'inactive' : 'active';
        $device->save();

        return response()->json(['message' => 'Device status toggled successfully']);
    }

    public function toggleRepair($deviceId)
    {
        $device = Device::findOrFail($deviceId);
        $device->in_repair = !$device->in_repair;
        $device->save();

        return response()->json(['message' => 'Device repair status toggled successfully']);
    }

    public function toggleBlocked($deviceId)
    {
        $device = Device::findOrFail($deviceId);
        $device->is_blocked = !$device->is_blocked;
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

    public function restore($id)
    {
        try {
            $device = Device::onlyTrashed()->findOrFail($id);
            $device->restore();

            return response()->json([
                'success' => true,
                'message' => "Device #{$device->device_number} has been restored successfully."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Failed to restore device. " . $e->getMessage()
            ], 500);
        }
    }

    public function repairList()
    {
        $devices = Device::with(['user', 'customer'])
                        ->where('in_repair', true)
                        ->orderBy('updated_at', 'desc')
                        ->get();

        return view('admin.admin-pages.device-repair-list', compact('devices'));
    }

    public function onboard(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'uid'           => 'required|string|exists:customers,uid',
            'device_number' => 'required|string|exists:devices,device_number',
            'ssid'          => 'required|string',
            'wifi_password' => 'required|string',
        ]);

        // Retrieve the customer based on the provided UID
        $customer = Customer::where('uid', $request->uid)->firstOrFail();

        // Retrieve the device based on the provided device number
        $device = Device::where('device_number', $request->device_number)->firstOrFail();

        // Ensure the device is not already assigned to a user
        if ($device->user_id !== null) {
            return response()->json(['message' => 'Device is already assigned to a user'], 403);
        }

        // Assign the customer and user ID to the device (assuming the customer has a user relationship)
        $user = User::find($customer->user_id);
        if (!$user) {
            return response()->json(['message' => 'User not found for the provided customer'], 404);
        }

        // Assign the user ID and customer ID to the device
        $device->user_id = $user->id;
        $device->customer_id = $customer->id;
        $device->save();

        // Process the Wi-Fi configuration (this can vary depending on your implementation)
        $device->wifi_ssid = $request->ssid;
        $device->wifi_password = $request->wifi_password;
        $device->save();

        // Return a response confirming the successful registration
        return response()->json([
            'message'     => 'Device registered successfully',
            'wifi_config' => [
                'ssid'        => $request->ssid,
                'password'    => $request->wifi_password,
                'mqtt_broker' => env('MQTT_BROKER_URL'),
                'mqtt_user'   => $device->device_number,
            ],
        ], 200);
    }

    public function showUnassociatedForm()
    {
        $devices = Device::whereNull('user_id')
                        ->whereNull('customer_id')
                        ->get();

        return view('admin.admin-pages.available-device-list', compact('devices'));
    }

}
