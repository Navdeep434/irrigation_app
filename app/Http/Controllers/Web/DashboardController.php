<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $lat = 28.6139;
        $lon = 77.2090;
        $apiKey = config('services.openweather.key');

        $response = Http::get("https://api.openweathermap.org/data/2.5/onecall", [
            'lat' => $lat,
            'lon' => $lon,
            'exclude' => 'minutely,hourly,alerts',
            'units' => 'metric',
            'appid' => $apiKey,
        ]);

        // dd($response->body());

        $forecast = [];

        if ($response->successful()) {
            $data = $response->json();
            if (isset($data['daily'])) {
                $forecast = collect($data['daily'])->take(6);
            } else {
                logger()->warning("OpenWeather response missing 'daily' key", ['response' => $data]);
            }
        } else {
            logger()->error("OpenWeather API request failed", ['response' => $response->body()]);
        }

        return view('web.user-pages.dashboard', compact('user', 'forecast'));
    }

}
