<?php

namespace App\Http\Controllers\Admin\API;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\User;

class DeviceController extends Controller
{
    public function onboard(Request $request)
    {\Log::info('Device onboard Payload:', $request->all());
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

        // Assign the customer and user ID to the device
        $user = User::find($customer->user_id);
        if (!$user) {
            return response()->json(['message' => 'User not found for the provided customer'], 404);
        }

        // Assign user ID and customer ID to the device
        $device->user_id = $user->id;
        $device->customer_id = $customer->id;
        $device->status = "active";
        $device->assigned_at = now();
        $device->save();

        // Return a response confirming the successful registration
        return response()->json([
            'message'     => 'Device registered successfully',
            'wifi_config' => [
                'ssid'     => $request->ssid,
                'password' => $request->wifi_password,
            ],
        ], 200);
    }

    public function updateDevice(Request $request)
    {
        $request->validate([
            'uid' => 'required|string|exists:customers,uid',
            'device_number' => 'required|string|exists:devices,device_number',
            'valve_connected' => 'required|integer|min:0|max:8',
            'flow_sensor_connected' => 'required|integer|min:0|max:8',
            // 'temp_sensor_connected' => 'required|boolean',
            // 'temp_sensor' => 'required_if:temp_sensor_connected,true|numeric',
        ]);

        \Log::info('Device Update Payload:', $request->all());

        // Retrieve the device based on the provided device number
        $device = Device::where('device_number', $request->device_number)->firstOrFail();
        
        if($device){
            $device->total_valves = $request->valve_connected;
            $device->total_flow_sensors = $request->flow_sensor_connected;
            $device->total_water_temp_sensors = $request->temp_sensor_connected ? 1 : 0;
            $device->save();
        } else {
            return response()->json(['message' => 'Device not found'], 404);
        }
        // Return the device details
        return response()->json([
            'message' => 'Device details updated successfully',
            'status'=> 'success',
            'status_code'=> 200,
        ], 200);
    }
    
}
