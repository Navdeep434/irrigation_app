<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\DeviceReading;

class DeviceController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $devices = Device::where('user_id', $user->id)->get();
        $deviceCount = $devices->count();

        return view('web.user-pages.my-devices', compact('devices', 'deviceCount'));
    }

    public function showControlForm()
    {
        $user = auth()->user();
        $uid = Customer::where('user_id', $user->id)->value('uid');
        $devices = Device::where('user_id', $user->id)->get();
        $selectedDevice = $devices->firstWhere('device_number', request('device_number')) ?? $devices->first();

        return view('web.user-pages.device-control', compact('devices', 'selectedDevice', 'uid'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'device_number' => 'required|string|exists:devices,device_number',
            'flow_rates' => 'required|array|min:1|max:8',
            'flow_rates.*' => 'numeric',
            'temperature' => 'required|numeric',
        ]);

        // Map flow_rates array to flow_rate1..8 fields
        $data = [
            'device_number' => $request->device_number,
            'temperature' => $request->temperature,
        ];

        foreach ($request->flow_rates as $index => $value) {
            $data['flow_rate' . ($index + 1)] = $value;
        }

        DeviceReading::create($data);

        return response()->json(['status' => 'ok']);
    }

    public function latest($device_number)
    {
        $reading = DeviceReading::where('device_number', $device_number)
                    ->latest()
                    ->first();

        if (!$reading) {
            return response()->json(['message' => 'No data found'], 404);
        }

        // Add this for debugging
        // \Log::info('API Response:', $reading->toArray());
        
        return response()->json($reading);
    }

}
