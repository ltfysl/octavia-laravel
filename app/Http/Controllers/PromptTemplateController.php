<?php

namespace App\Http\Controllers;

use App\Models\PromptTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PromptTemplateController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $category = $request->query('category', 'all');

        return response()->json(
            PromptTemplate::forCategory($category)
                ->orderByDesc('created_at')
                ->get()
        );
    }

    public function show(PromptTemplate $template): JsonResponse
    {
        return response()->json($template);
    }
}
