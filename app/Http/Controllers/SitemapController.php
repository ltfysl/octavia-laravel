<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $baseUrl = rtrim(config('app.url'), '/');
        $routes = ['/', '/features', '/pricing', '/privacy', '/terms', '/login', '/register'];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n".
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n".
            collect($routes)
                ->map(fn (string $path) => "  <url><loc>{$baseUrl}{$path}</loc></url>")
                ->implode("\n")."\n</urlset>\n";

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
