<?php

namespace App\Functions;

class ErrorHandling
{
    public static function getErrorsReport($failures, $rowsFailedCount)
    {
        $errorsReport = [
            "rowsFailedCount" => $rowsFailedCount,
            'errors' => []
        ];

        foreach ($failures as $failure) {
            array_push(
                $errorsReport["errors"],
                "Linha {$failure->row()} {$failure->errors()[0]}"
            );
        }
        return $errorsReport;
    }
}
