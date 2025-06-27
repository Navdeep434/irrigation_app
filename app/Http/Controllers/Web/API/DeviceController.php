<?php

namespace App\Http\Controllers\Web\API;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function getStatus(Request $request)
    {
        $device = Device::where('device_number', $request->device_number)->firstOrFail();
        return response()->json([
            'status' => $device->status,
            'last_seen' => $device->last_seen
        ]);
    }

    public function heartbeat(Request $request)
    {
        $payload = $request->input(); // You’ll parse topic and payload here

        // Example topic: /greenmesh/UID/DEVICE123/heartbeat
        $topic = $payload['topic'] ?? '';
        $message = $payload['message'] ?? '';

        preg_match('#/greenmesh/([^/]+)/([^/]+)/heartbeat#', $topic, $matches);
        $uid = $matches[1] ?? null;
        $deviceNumber = $matches[2] ?? null;

        if ($deviceNumber) {
            \App\Models\Device::where('device_number', $deviceNumber)
                ->update(['last_seen' => now()]);
        }

        return response()->json(['status' => 'received']);
    }


}
