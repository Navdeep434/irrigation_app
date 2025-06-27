<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

class MQTTController extends Controller
{
    public function publishValveCommand(Request $request)
    {
        $request->validate([
            'customer_uid' => 'required|string',
            'device_number' => 'required|string',
            'status' => 'required|in:ON,OFF',
        ]);

        $server   = env('MQTT_HOST');
        $port     = env('MQTT_PORT');
        $username = env('MQTT_USERNAME');
        $password = env('MQTT_PASSWORD');
        $useTls   = env('MQTT_TLS', false);

        $clientId = 'laravel-' . uniqid();
        $mqtt = new MqttClient($server, $port, $clientId, MqttClient::MQTT_3_1_1);

        $settings = (new ConnectionSettings)
            ->setUsername($username)
            ->setPassword($password)
            ->setUseTls($useTls);

        try {
            $mqtt->connect($settings, true);
            $topic = "greenmesh/{$request->uid}/{$request->device_number}/control";
            $mqtt->publish($topic, $request->status, 1);
            $mqtt->disconnect();

            return response()->json(['message' => 'Command sent']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

