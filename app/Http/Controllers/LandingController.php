<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class LandingController extends Controller
{
    public function home(): Response
    {
        return Inertia::render('Landing', ['page' => 'home']);
    }

    public function features(): Response
    {
        return Inertia::render('Landing', ['page' => 'features']);
    }

    public function pricing(): Response
    {
        return Inertia::render('Landing', ['page' => 'pricing']);
    }

    public function privacy(): Response
    {
        return Inertia::render('Legal', ['page' => 'privacy']);
    }

    public function terms(): Response
    {
        return Inertia::render('Legal', ['page' => 'terms']);
    }
}
