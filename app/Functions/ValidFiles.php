<?php

namespace App\Functions;

use Illuminate\Support\Facades\Validator;

class ValidFiles
{
    public static function validFile($file)
    {
        $errors = Validator::make(
            [
                'extension' => strtolower($file->getClientOriginalExtension())
            ],
            [
                'extension' => 'required|in:csv'
            ]
        )->errors();

        if ($errors->has('extension')) {
            return response()->json("NOT ACCEPTABLE: {$errors->first('extension')}", 406);
        }
    }
}
