<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Device;

class CheckDeviceStatus extends Command
{
    protected $signature = 'app:check-device-status';
    protected $description = 'Update device online/offline based on last_seen';

    public function handle()
    {
        $cutoff = now()->subMinutes(2);

        $offline = Device::where('last_seen', '<', $cutoff)
                         ->where('status', '!=', 'offline')
                         ->update(['status' => 'offline']);

        $online = Device::where('last_seen', '>=', $cutoff)
                        ->where('status', '!=', 'online')
                        ->update(['status' => 'online']);

        $this->info("✅ Updated: $online online, $offline offline.");
    }
}
