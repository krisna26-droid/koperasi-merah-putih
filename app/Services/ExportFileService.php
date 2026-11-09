<?php

namespace App\Services;

use App\Services\CooperativeIdentity;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class ExportFileService
{
    /** @param list<string> $headers @param list<list<mixed>> $rows */
    public function download(
        string $title,
        array $headers,
        array $rows,
        string $format,
        string $orientation = 'portrait'
    ): Response|StreamedResponse {
        $filename = Str::slug($title) . '.' . $format;

        return match ($format) {
            'csv' => $this->csvDownload($filename, $headers, $rows),
            'xlsx' => $this->xlsxDownload($filename, $headers, $rows),
            'pdf' => $this->pdfDownload($filename, $title, $headers, $rows, $orientation),
            default => abort(404),
        };
    }

    /** @param list<string> $headers @param list<list<mixed>> $rows */
    private function csvDownload(string $filename, array $headers, array $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows): void {
            $output = fopen('php://output', 'wb');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, $headers, ',', '"', '\\');

            foreach ($rows as $row) {
                fputcsv($output, array_map($this->csvSafeValue(...), $row), ',', '"', '\\');
            }

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }

    /** @param list<string> $headers @param list<list<mixed>> $rows */
    private function xlsxDownload(string $filename, array $headers, array $rows): Response
    {
        $temporaryPath = tempnam(sys_get_temp_dir(), 'koperasi-xlsx-');
        if ($temporaryPath === false) {
            abort(500, 'Tidak dapat menyiapkan berkas Excel.');
        }

        $zip = new ZipArchive();
        if ($zip->open($temporaryPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            @unlink($temporaryPath);
            abort(500, 'Tidak dapat membuat berkas Excel.');
        }

        $zip->addFromString('[Content_Types].xml', $this->contentTypesXml());
        $zip->addFromString('_rels/.rels', $this->rootRelationshipsXml());
        $zip->addFromString('xl/workbook.xml', $this->workbookXml());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbookRelationshipsXml());
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->worksheetXml($headers, $rows));
        $zip->close();

        return response()->download($temporaryPath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ])->deleteFileAfterSend(true);
    }

    /** @param list<string> $headers @param list<list<mixed>> $rows */
    private function pdfDownload(
        string $filename,
        string $title,
        array $headers,
        array $rows,
        string $orientation
    ): Response {
        $body = $this->buildPdf($title, $headers, $rows, $orientation === 'landscape');

        return response($body, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length' => (string) strlen($body),
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }

    private function csvSafeValue(mixed $value): string|int|float
    {
        if (is_int($value) || is_float($value)) {
            return $value;
        }

        $text = (string) ($value ?? '');
        if (preg_match('/^[\s]*[=+@]/u', $text) === 1 || preg_match('/^[\s]*-[^0-9.]/u', $text) === 1) {
            return "'" . $text;
        }

        return $text;
    }

    private function contentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '</Types>';
    }

    private function rootRelationshipsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';
    }

    private function workbookXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
            . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="Data" sheetId="1" r:id="rId1"/></sheets></workbook>';
    }

    private function workbookRelationshipsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '</Relationships>';
    }

    /** @param list<string> $headers @param list<list<mixed>> $rows */
    private function worksheetXml(array $headers, array $rows): string
    {
        $allRows = [$headers, ...$rows];
        $sheetRows = '';

        foreach ($allRows as $rowIndex => $row) {
            $excelRow = $rowIndex + 1;
            $cells = '';

            foreach (array_values($row) as $columnIndex => $value) {
                $cellReference = $this->excelColumn($columnIndex + 1) . $excelRow;
                if (is_int($value) || is_float($value)) {
                    $number = is_float($value)
                        ? json_encode($value, JSON_PRESERVE_ZERO_FRACTION)
                        : (string) $value;
                    $cells .= '<c r="' . $cellReference . '"><v>' . $number . '</v></c>';
                    continue;
                }

                $text = $this->xmlSafeValue((string) ($value ?? ''));
                $cells .= '<c r="' . $cellReference . '" t="inlineStr"><is><t xml:space="preserve">'
                    . $text . '</t></is></c>';
            }

            $sheetRows .= '<row r="' . $excelRow . '">' . $cells . '</row>';
        }

        $lastColumn = $this->excelColumn(max(1, count($headers)));
        $lastRow = max(1, count($allRows));

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<dimension ref="A1:' . $lastColumn . $lastRow . '"/>'
            . '<sheetViews><sheetView workbookViewId="0"><pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>'
            . '<sheetFormatPr defaultRowHeight="15"/><sheetData>' . $sheetRows . '</sheetData>'
            . '<autoFilter ref="A1:' . $lastColumn . $lastRow . '"/>'
            . '</worksheet>';
    }

    private function excelColumn(int $column): string
    {
        $letters = '';
        while ($column > 0) {
            $remainder = ($column - 1) % 26;
            $letters = chr(65 + $remainder) . $letters;
            $column = intdiv($column - 1, 26);
        }

        return $letters;
    }

    private function xmlSafeValue(string $value): string
    {
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $value) ?? '';
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    /** @param list<string> $headers @param list<list<mixed>> $rows */
    private function buildPdf(string $title, array $headers, array $rows, bool $landscape): string
    {
        $pageWidth = $landscape ? 842.0 : 595.0;
        $pageHeight = $landscape ? 595.0 : 842.0;
        $left = 30.0;
        $right = 30.0;
        $usableWidth = $pageWidth - $left - $right;
        $fontSize = count($headers) >= 10 ? ($landscape ? 6.0 : 5.0) : 8.0;
        $columnWidth = $usableWidth / max(1, count($headers));
        $contentPages = [];
        $current = '';
        $cursorY = 0.0;

        $printedBy = auth()->user()?->name ?? auth()->user()?->username ?? 'Tidak teridentifikasi';
        $identity = app(CooperativeIdentity::class)->current();
        $startPage = function () use (&$current, &$cursorY, $pageHeight, $title, $left, $headers, $columnWidth, $fontSize, $printedBy, $identity): void {
            $current = '';
            $this->pdfText($current, $left, $pageHeight - 25, 9, mb_strtoupper($identity->name), true);
            $this->pdfText($current, $left, $pageHeight - 37, 7, $identity->address ?? '', false);
            $this->pdfText($current, $left, $pageHeight - 49, 12, $title, true);
            $this->pdfText($current, $left, $pageHeight - 62, 7, 'Tanggal cetak: ' . now()->format('d-m-Y H:i'), false);
            $this->pdfText($current, $left, $pageHeight - 73, 7, 'Dicetak oleh: ' . $printedBy, false);
            $cursorY = $pageHeight - 85;
            $this->pdfTableRow($current, $left, $cursorY, array_map('strval', $headers), $columnWidth, $fontSize, true);
            $cursorY -= $this->pdfRowHeight(array_map('strval', $headers), $columnWidth, $fontSize);
        };

        $startPage();
        foreach ($rows as $row) {
            $values = array_map(fn ($value) => $this->pdfSafeValue($value), array_values($row));
            $rowHeight = $this->pdfRowHeight($values, $columnWidth, $fontSize);

            if ($cursorY - $rowHeight < 28) {
                $contentPages[] = $current;
                $startPage();
            }

            $this->pdfTableRow($current, $left, $cursorY, $values, $columnWidth, $fontSize, false);
            $cursorY -= $rowHeight;
        }
        $contentPages[] = $current;

        $objects = [
            1 => '<< /Type /Catalog /Pages 2 0 R >>',
            3 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>',
            4 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>',
        ];
        $pageIds = [];

        foreach ($contentPages as $index => $stream) {
            $pageId = 5 + ($index * 2);
            $contentId = $pageId + 1;
            $pageIds[] = $pageId . ' 0 R';
            $objects[$pageId] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 ' . $pageWidth . ' ' . $pageHeight . '] '
                . '/Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> /Contents ' . $contentId . ' 0 R >>';
            $objects[$contentId] = '<< /Length ' . strlen($stream) . ">>\nstream\n" . $stream . "\nendstream";
        }

        $objects[2] = '<< /Type /Pages /Kids [' . implode(' ', $pageIds) . '] /Count ' . count($pageIds) . ' >>';
        ksort($objects);
        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        $lastObject = max(array_keys($objects));

        for ($objectId = 1; $objectId <= $lastObject; $objectId++) {
            $offsets[$objectId] = strlen($pdf);
            $pdf .= $objectId . " 0 obj\n" . ($objects[$objectId] ?? 'null') . "\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 " . ($lastObject + 1) . "\n0000000000 65535 f \n";
        for ($objectId = 1; $objectId <= $lastObject; $objectId++) {
            $pdf .= sprintf('%010d 00000 n ', $offsets[$objectId]) . "\n";
        }
        $pdf .= "trailer\n<< /Size " . ($lastObject + 1) . " /Root 1 0 R >>\nstartxref\n" . $xrefOffset . "\n%%EOF";

        return $pdf;
    }

    /** @param list<string> $values */
    private function pdfRowHeight(array $values, float $columnWidth, float $fontSize): float
    {
        $lineCount = 1;
        $maxCharacters = max(5, (int) floor(($columnWidth - 8) / ($fontSize * 0.52)));

        foreach ($values as $value) {
            $wrapped = $this->wrapText($value, $maxCharacters);
            $lineCount = max($lineCount, count($wrapped));
        }

        return max(17.0, ($lineCount * ($fontSize + 2)) + 6);
    }

    /** @param list<string> $values */
    private function pdfTableRow(
        string &$content,
        float $left,
        float $top,
        array $values,
        float $columnWidth,
        float $fontSize,
        bool $header
    ): void {
        $height = $this->pdfRowHeight($values, $columnWidth, $fontSize);
        $content .= '0.65 w ' . $left . ' ' . ($top - $height) . ' ' . (count($values) * $columnWidth) . ' ' . $height . " re S\n";
        $maxCharacters = max(5, (int) floor(($columnWidth - 8) / ($fontSize * 0.52)));

        foreach ($values as $index => $value) {
            $x = $left + ($index * $columnWidth);
            if ($index > 0) {
                $content .= $x . ' ' . ($top - $height) . ' m ' . $x . ' ' . $top . " l S\n";
            }

            foreach ($this->wrapText($value, $maxCharacters) as $lineIndex => $line) {
                $this->pdfText(
                    $content,
                    $x + 4,
                    $top - 11 - ($lineIndex * ($fontSize + 2)),
                    $fontSize,
                    $line,
                    $header
                );
            }
        }
    }

    /** @return list<string> */
    private function wrapText(string $value, int $maxCharacters): array
    {
        $normalized = preg_replace('/\s+/u', ' ', $value) ?? '';
        $indent = min(4, strlen($normalized) - strlen(ltrim($normalized)));
        $value = trim($normalized);
        if ($value === '') {
            return [''];
        }

        $lines = explode("\n", wordwrap($value, max(5, $maxCharacters - $indent), "\n", true));
        if ($indent > 0) {
            $prefix = str_repeat(' ', $indent);
            $lines = array_map(fn (string $line) => $prefix . $line, $lines);
        }

        return $lines;
    }

    private function pdfText(string &$content, float $x, float $y, float $fontSize, string $text, bool $bold): void
    {
        $encoded = iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $text);
        $encoded = $encoded === false ? $text : $encoded;
        $escaped = str_replace(['\\', '(', ')', "\r", "\n"], ['\\\\', '\\(', '\\)', '', ''], $encoded);
        $font = $bold ? 'F2' : 'F1';
        $content .= 'BT /' . $font . ' ' . $fontSize . ' Tf 1 0 0 1 ' . $x . ' ' . $y . ' Tm (' . $escaped . ") Tj ET\n";
    }

    private function pdfSafeValue(mixed $value): string
    {
        if ($value === null) {
            return '';
        }
        if (is_bool($value)) {
            return $value ? 'Ya' : 'Tidak';
        }
        if (is_float($value)) {
            return number_format($value, 2, ',', '.');
        }

        return (string) $value;
    }
}
