<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.serverKey') ?? 'SB-Mid-server-placeholder';
        Config::$isProduction = config('services.midtrans.isProduction', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Generate Snap Token from Midtrans.
     *
     * @param array $params
     * @return string
     */
    public function createSnapToken(array $params): string
    {
        return Snap::getSnapToken($params);
    }
}
