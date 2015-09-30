<?php

namespace Notnull\DailyNews\Models;

class Math
{
    public static function average($arr)
    {
        if (!count($arr)) return 0;

        $sum = 0;
        for ($i = 0; $i < count($arr); $i++) {
            $sum += $arr[$i];
        }

        return $sum / count($arr);
    }

    public static function variance($arr)
    {
        if (!count($arr)) return 0;

        $mean = self::average($arr);

        $sos = 0;    // Sum of squares
        for ($i = 0; $i < count($arr); $i++) {
            $sos += ($arr[$i] - $mean);
        }

        return $sos / (count($arr)-1);  // denominator = n-1; i.e. estimating based on sample
        // n-1 is also what MS Excel takes by default in the
        // VAR function
    }

    public static function specialMean($arr, $range = 0.85)
    {
        if (!count($arr)) return 0;

        sort($arr);

        $sum = 0;
        $count = 0;
        for ($i = (int)floor(count($arr)*(1 - $range)); $i < (int)floor(count($arr)*$range); $i++) {
            $sum += $arr[$i];
            $count++;
        }

        return $sum / $count;
//        return $avg;
    }
}