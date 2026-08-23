<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AuditLogController extends Controller
{
    public function index(Request $request): Response
    {
        $query = AuditLog::where('user_id', $request->user()->id)->latest();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('entity_name', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(20)->withQueryString();

        return Inertia::render('audit/Index', [
            'logs' => $logs,
            'filters' => $request->only(['category', 'severity', 'search']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'action' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'entity_type' => ['nullable', 'string', 'max:255'],
            'entity_id' => ['nullable', 'string', 'max:255'],
            'entity_name' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'metadata' => ['nullable', 'array'],
            'severity' => ['nullable', 'string', 'in:info,success,warning,error'],
        ]);

        $log = AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => $data['action'],
            'category' => $data['category'],
            'entity_type' => $data['entity_type'] ?? null,
            'entity_id' => $data['entity_id'] ?? null,
            'entity_name' => $data['entity_name'] ?? null,
            'description' => $data['description'],
            'metadata' => $data['metadata'] ?? null,
            'severity' => $data['severity'] ?? 'info',
        ]);

        if ($request->wantsJson()) {
            return response()->json($log, 201);
        }

        return back()->with('success', 'Audit logged');
    }
}
