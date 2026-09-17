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
                    'Most gut resets leave you guessing' => 'Most resets leave you guessing',
                    '30-Day Guided Gut Reset' => '30-Day Guided My CLOSQ',
                    '30-day Guided Gut Reset' => '30-day Guided My CLOSQ',
                    '30-Day Gut Reset' => '30-Day My CLOSQ',
                    '30-day Gut Reset' => '30-day My CLOSQ',
                    '30 Day Gut Reset' => '30-Day My CLOSQ',
                    'Shop Gut Reset' => 'Shop My CLOSQ',
                    'Start your Gut Reset journey' => 'Start your My CLOSQ journey',
                    'receive Gut Reset WhatsApp messages' => 'receive My CLOSQ WhatsApp messages',
                    'Gut Reset WhatsApp' => 'My CLOSQ WhatsApp',
                    'Gut Reset Admin' => 'My CLOSQ Admin',
                    'Gut Reset order' => 'My CLOSQ order',
                    'Gut Reset' => 'My CLOSQ',
                    'gut reset' => 'My CLOSQ',
                    'Gut reset' => 'My CLOSQ',
                    'GUT RESET' => 'MY CLOSQ',
                    'Gutreset' => 'My CLOSQ',
                ];

                $content = str_replace(array_keys($replacements), array_values($replacements), $content);
                $response->setContent($content);
            }
        }

        return $response;
    }
}
