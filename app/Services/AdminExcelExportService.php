<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminExcelExportService
{
    public function download(string $filename, array $headings, iterable $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headings, $rows): void {
            echo '<?xml version="1.0" encoding="UTF-8"?>';
            echo '<?mso-application progid="Excel.Sheet"?>';
            echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">';
            echo '<Worksheet ss:Name="Export"><Table>';
            $this->writeRow($headings);

            foreach ($rows as $row) {
                $this->writeRow($row);
            }

            echo '</Table></Worksheet></Workbook>';
        }, $filename.'.xls', [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    private function writeRow(array $values): void
    {
        echo '<Row>';

        foreach ($values as $value) {
            $value = htmlspecialchars((string) ($value ?? ''), ENT_XML1 | ENT_QUOTES, 'UTF-8');
            echo '<Cell><Data ss:Type="String">'.$value.'</Data></Cell>';
        }

        echo '</Row>';
    }
}