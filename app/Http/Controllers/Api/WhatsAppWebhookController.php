<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessWhatsAppWebhook;
use Illuminate\Http\Request;

class WhatsAppWebhookController extends Controller
{
    public function verify(Request $request)
    {
        if ($request->input('hub.mode') === 'subscribe'
            && hash_equals((string)config('services.whatsapp.verify_token'),(string)$request->input('hub.verify_token'))) {
            return response($request->input('hub.challenge'),200);
        }
        return response('Forbidden',403);
    }

    public function receive(Request $request)
    {
        ProcessWhatsAppWebhook::dispatch($request->getContent(),$request->header('X-Hub-Signature-256'));
        return response()->json(['received'=>true],200);
    }
}
