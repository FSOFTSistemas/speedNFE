<?php

namespace App\Utils;

class CalculateCouponHeight
{
    public static function calculate($itemsLength)
    {
        $height = 150;
        if ($itemsLength > 1) {
            for ($i = 1; $i < $itemsLength; $i++) {
                $height += 15;
            }
        }
        return $height;
    }

}
