<?php

namespace App\Utils;

class CalculateCouponHeight
{
    public static function calculate($itemsLength)
    {
        $height = 280;
        if ($itemsLength > 1) {
            for ($i = 1; $i < $itemsLength; $i++) {
                $height += 20;
            }
        }
        return $height;
    }

}
