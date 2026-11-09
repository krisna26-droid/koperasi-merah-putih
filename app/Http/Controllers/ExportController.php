<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Services\ExportFileService;
use App\Services\ReportExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function __invoke(
        Request $request,
        string $report,
        string $format,
        ReportExportService $reportExportService,
        ExportFileService $exportFileService
    ): Response|StreamedResponse {
        abort_unless(in_array($format, ['csv', 'xlsx', 'pdf'], true), 404);

        $filters = $request->validate([
            'orientation' => ['sometimes', 'in:portrait,landscape'],
            'start_date' => ['sometimes', 'nullable', 'date'],
            'end_date' => ['sometimes', 'nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['sometimes', 'nullable', 'string', 'max:30'],
            'jenis' => ['sometimes', 'nullable', 'string', 'max:30'],
            'jenis_simpanan' => ['sometimes', 'nullable', 'string', 'max:30'],
            'kode_akun' => ['sometimes', 'nullable', 'string', 'max:10'],
            'month' => ['sometimes', 'integer', 'between:1,12'],
            'year' => ['sometimes', 'integer', 'between:2000,2100'],
            'search' => ['sometimes', 'nullable', 'string', 'max:100'],
        ]);

        $dataset = $reportExportService->make($report, $request);
        if (Schema::hasTable('activity_logs')) {
            ActivityLog::create([
                'actor_username' => $request->user()?->username,
                'actor_name' => $request->user()?->name,
                'action' => $format === 'pdf' ? 'printed' : 'exported',
                'subject_type' => 'Laporan',
                'subject_id' => $report,
                'changed_fields' => ['format' => $format, 'orientation' => $filters['orientation'] ?? 'portrait'],
            ]);
        }

        return $exportFileService->download(
            $dataset['title'],
            $dataset['headers'],
            $dataset['rows'],
            $format,
            $filters['orientation'] ?? 'portrait'
        );
    }
}
