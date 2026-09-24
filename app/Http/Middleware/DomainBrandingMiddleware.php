<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DomainBrandingMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $host = $request->getHost();
        $isMyClosq = in_array($host, ['myclosq.com', 'www.myclosq.com']);

        if (!$isMyClosq) {
            return $response;
        }

        // Only process HTML responses
        $contentType = $response->headers->get('Content-Type') ?? '';
        $isHtml = str_contains($contentType, 'text/html') || $response instanceof \Illuminate\Http\Response;

        if ($isHtml && method_exists($response, 'getContent')) {
            $content = $response->getContent();

            if (is_string($content) && $content !== '') {
                $replacements = [
                    'Most myclosqs leave you guessing' => 'Most resets leave you guessing',
                    '30-Day Guided Myclosq' => '30-Day Guided My CLOSQ',
                    '30-day Guided Myclosq' => '30-day Guided My CLOSQ',
                    '30-Day Myclosq' => '30-Day My CLOSQ',
                    '30-day Myclosq' => '30-day My CLOSQ',
                    '30 Day Myclosq' => '30-Day My CLOSQ',
                    'Shop Myclosq' => 'Shop My CLOSQ',
                    'Start your Myclosq journey' => 'Start your My CLOSQ journey',
                    'receive Myclosq WhatsApp messages' => 'receive My CLOSQ WhatsApp messages',
                    'Myclosq WhatsApp' => 'My CLOSQ WhatsApp',
                    'Myclosq Admin' => 'My CLOSQ Admin',
                    'Myclosq order' => 'My CLOSQ order',
                    'Myclosq' => 'My CLOSQ',
                    'myclosq' => 'My CLOSQ',
                    'myclosq' => 'My CLOSQ',
                    'myclosq' => 'MY CLOSQ',
                    'Gutreset' => 'My CLOSQ',
                ];

                $content = str_replace(array_keys($replacements), array_values($replacements), $content);
                $response->setContent($content);
            }
        }

        return $response;
    }
}
