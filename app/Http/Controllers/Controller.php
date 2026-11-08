<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

abstract class Controller
{
    /**
     * عميل HTTP جاهز لـ API البنك: الرابط الأساسي والمهلة من config/services.php
     */
    protected function tmaApi(?string $token = null): PendingRequest
    {
        $request = Http::baseUrl(config('services.tma.url'))
            ->acceptJson()
            ->timeout(config('services.tma.timeout'))
            ->connectTimeout(config('services.tma.connect_timeout'));

        return $token ? $request->withToken($token) : $request;
    }
}
