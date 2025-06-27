<?php

namespace App\Http\Controllers\Web\API;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Device;
use Illuminate\Http\Request;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

class ValveController extends Controller
{

    public function sendCommand(Request $request)
    {
        $request->validate([
            'uid' => 'required|string',
            'device_number' => 'required|string',
            'valve_number' => 'required|integer|min:1|max:4',
            'action' => 'required|in:on,off',
        ]);

        $host = config('services.mqtt.host');
        $port = config('services.mqtt.port');
        $username = config('services.mqtt.username');
        $password = config('services.mqtt.password');

        $connectionSettings = (new ConnectionSettings)
            ->setUsername($username)
            ->setPassword($password)
            ->setUseTls(true);

        $mqtt = new MqttClient($host, (int) $port, 'laravel-client');
        $mqtt->connect($connectionSettings, true);

        $topic = "/greenmesh/{$request->uid}/{$request->device_number}/control";
        $payload = json_encode([
            'valve_number' => $request->valve_number,
            'action' => $request->action,
        ]);

        $mqtt->publish($topic, $payload);
        $mqtt->disconnect();

        return back()->with('status', 'Valve command sent successfully!');
    }

}
