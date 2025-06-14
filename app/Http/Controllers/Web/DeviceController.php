<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\DeviceData;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index()
    {
        $deviceId = 'esp32_001'; // or get it dynamically based on user/device
        $latestData = DeviceData::where('device_id', $deviceId)->latest()->first();

        return view('web.user-pages.device-control', compact('latestData'));
    }

}
