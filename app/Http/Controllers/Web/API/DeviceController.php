<?php

namespace App\Http\Controllers\Web\API;

use App\Http\Controllers\Controller;
use App\Models\DeviceData;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function __construct()
    {
        // Protect all methods with Sanctum auth
        $this->middleware('auth:sanctum');
    }

    // Store incoming device data (authenticated devices/users)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'device_id' => 'required|string',
            'temperature' => 'nullable|numeric',
            'valves' => 'nullable|array',
            'flow_sensors' => 'nullable|array',
        ]);

        $data = DeviceData::create([
            'device_id' => $validated['device_id'],
            'temperature' => $validated['temperature'] ?? null,
            'valves' => $validated['valves'] ?? [],
            'flow_sensors' => $validated['flow_sensors'] ?? [],
        ]);

        return response()->json(['status' => 'success', 'data' => $data], 201);
    }

    // Return Blade view with the user's device number
    public function deviceControlPanel()
    {
        $user = auth()->user();
        $customer = $user->customer;

        if (!$customer) {
            abort(403, 'No customer found for this user.');
        }

        $device = $customer->devices()->first();

        if (!$device) {
            abort(404, 'No device found for this customer.');
        }

        return view('device.control-panel', ['deviceNumber' => $device->device_number]);
    }

    // API endpoint: Show latest data for a device owned by the logged-in user
    public function showLatestData(Request $request)
    {
        $user = auth()->user();

        $deviceId = $request->query('device_id');
        if (!$deviceId) {
            return response()->json(['status' => 'error', 'message' => 'Device ID is required'], 400);
        }

        $customer = $user->customer;
        if (!$customer) {
            return response()->json(['status' => 'error', 'message' => 'Customer not found for user'], 403);
        }

        // Check if the device belongs to this customer
        $device = $customer->devices()->where('device_number', $deviceId)->first();

        if (!$device) {
            return response()->json(['status' => 'error', 'message' => 'Device not found or access denied'], 403);
        }

        // Get the latest DeviceData for this device
        $data = DeviceData::where('device_id', $device->device_number)->latest()->first();

        if (!$data) {
            return response()->json(['status' => 'error', 'message' => 'No data found for this device'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $data]);
    }
}
