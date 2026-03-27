<?php

namespace App\Http\Controllers;

use App\Models\ActionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class ActionLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = ActionLog::with('user')
            ->when($request->search, fn ($q, $s) => $q->where('description', 'like', "%{$s}%"))
            ->when($request->action, fn ($q, $action) => $q->where('action', $action))
            ->when($request->target_type, fn ($q, $type) => $q->where('target_type', $type))
            ->when($request->date_debut, fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($request->date_fin, fn ($q, $d) => $q->whereDate('created_at', '<=', $d))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $actions = ActionLog::query()
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        $targetTypes = ActionLog::query()
            ->whereNotNull('target_type')
            ->select('target_type')
            ->distinct()
            ->orderBy('target_type')
            ->pluck('target_type');

        return view('historique.index', compact('logs', 'actions', 'targetTypes'));
    }

    public function backup(Request $request)
    {
        Artisan::call('backup:database', [
            '--initiator' => (string) $request->user()->id,
        ]);

        $output = trim(Artisan::output());

        return back()->with(
            str_contains(strtolower($output), 'failed') || str_contains(strtolower($output), 'error') ? 'error' : 'success',
            $output ?: 'Backup lance.'
        );
    }
}
