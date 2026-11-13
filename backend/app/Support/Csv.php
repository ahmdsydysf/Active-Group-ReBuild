<?php

namespace App\Support;

use Symfony\Component\HttpFoundation\StreamedResponse;

/** CSV downloads that open correctly in Excel with Arabic text. */
final class Csv
{
    /** @param iterable<array<int, scalar|null>> $rows */
    public static function download(string $name, array $header, iterable $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($header, $rows): void {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $header, escape: '');
            foreach ($rows as $row) {
                // A leading = + - @ would run as a formula in Excel.
                fputcsv($out, array_map(fn ($v) => is_string($v) && preg_match('/^[=+\-@\t\r]/', $v) ? "'".$v : $v, $row), escape: '');
            }
            fclose($out);
        }, $name.'-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
