@props(['report'])

@php
    $routeName = auth()->user()->peran === 'Ketua' ? 'ketua.export' : 'pengurus.export';
    $exportUrl = fn (string $format, ?string $orientation = null) => route($routeName, array_merge(
        request()->query(),
        array_filter([
            'report' => $report,
            'format' => $format,
            'orientation' => $orientation,
        ], fn ($value) => $value !== null)
    ));
    $buttonClass = 'px-3 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-bold rounded-xl transition inline-flex items-center gap-1.5 shadow-sm';
@endphp

<div class="flex flex-wrap items-center gap-2 no-print" aria-label="Unduh data">
    <a href="{{ $exportUrl('csv') }}" class="{{ $buttonClass }}" download>CSV</a>
    <a href="{{ $exportUrl('xlsx') }}" class="{{ $buttonClass }}" download>Excel</a>
    <a href="{{ $exportUrl('pdf', 'portrait') }}" class="{{ $buttonClass }}" download>PDF Potret</a>
    <a href="{{ $exportUrl('pdf', 'landscape') }}" class="{{ $buttonClass }}" download>PDF Lanskap</a>
</div>
