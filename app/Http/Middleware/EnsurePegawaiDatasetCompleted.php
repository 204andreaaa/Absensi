<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePegawaiDatasetCompleted
{
    private const MIN_DATASET = 15;

    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user || $user->role !== 'pegawai') {
            return $next($request);
        }

        $allowedRoutes = [
            'pegawai.dataset',
            'pegawai.dataset.store',
            'pegawai.dataset.load',
            'logout',
        ];

        if ($request->routeIs(...$allowedRoutes)) {
            return $next($request);
        }

        $datasetCount = $user->dataset_wajahs()->count();

        if ($datasetCount < self::MIN_DATASET) {
            return redirect()
                ->route('pegawai.dataset')
                ->with('force_dataset_registration', true);
        }

        return $next($request);
    }
}
