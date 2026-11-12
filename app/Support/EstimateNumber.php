<?php

namespace App\Support;

use App\Models\Estimate;
use Illuminate\Support\Str;

class EstimateNumber
{
    /** `EST-` + 6 uppercase hex characters, as in the original desk. */
    public static function generate(): string
    {
        do {
            $number = 'EST-'.Str::upper(substr(str_replace('-', '', (string) Str::uuid()), 0, 6));
        } while (Estimate::where('number', $number)->exists());

        return $number;
    }
}
