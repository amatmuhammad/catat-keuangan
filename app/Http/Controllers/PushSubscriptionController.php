<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PushSubscriptionController extends Controller
{
    /**
     * Simpan endpoint push dari perangkat yang mengizinkan notifikasi.
     */
    public function store(Request $request): Response
    {
        $attributes = $request->validate([
            'endpoint' => ['required', 'string', 'max:500'],
            'keys.p256dh' => ['required', 'string'],
            'keys.auth' => ['required', 'string'],
            'contentEncoding' => ['nullable', 'string', 'in:aesgcm,aes128gcm'],
        ]);

        $request->user()->updatePushSubscription(
            $attributes['endpoint'],
            $attributes['keys']['p256dh'],
            $attributes['keys']['auth'],
            $attributes['contentEncoding'] ?? null,
        );

        return response()->noContent();
    }

    /**
     * Cabut endpoint push perangkat ini (misalnya setelah logout).
     */
    public function destroy(Request $request): Response
    {
        $attributes = $request->validate([
            'endpoint' => ['required', 'string', 'max:500'],
        ]);

        $request->user()->deletePushSubscription($attributes['endpoint']);

        return response()->noContent();
    }
}
