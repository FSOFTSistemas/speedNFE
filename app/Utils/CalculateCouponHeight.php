<?php

namespace App\Utils;

class CalculateCouponHeight
{
    public static function calculate($itemsLength, $methodsLength, $client)
    {
        $height = 220;
        if ($itemsLength > 0) {
            for ($i = 0; $i < $itemsLength; $i++) {
                $height += 25;
            }
        }
        if ($methodsLength > 0) {
            for ($i = 0; $i < $methodsLength; $i++) {
                $height += 10;
            }
        }
        if ($client) {
            $height += 35;
        }
        return $height;
    }

}
