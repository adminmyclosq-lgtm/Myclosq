<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessRazorpayWebhook;
use Illuminate\Http\Request;

class RazorpayWebhookController extends Controller
{
    public function receive(Request $request)
    {
        $raw=$request->getContent();
        $signature=(string)$request->header('X-Razorpay-Signature');

        ProcessRazorpayWebhook::dispatch($raw,$signature);

        return response()->json(['received'=>true],200);
    }
}
