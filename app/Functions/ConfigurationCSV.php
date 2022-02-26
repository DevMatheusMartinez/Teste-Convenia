<?php

namespace App\Functions;

use Illuminate\Support\Facades\Config;

class ConfigurationCSV
{
    public static function setEnconding($file)
    {
        $enc = mb_detect_encoding(file_get_contents($file), mb_list_encodings(), true);
        Config::set('excel.imports.csv.input_encoding', $enc);
    }
}
