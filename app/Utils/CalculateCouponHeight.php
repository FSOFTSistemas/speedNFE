<?php

namespace App\Utils;

class CalculateCouponHeight
{
    public static function calculate($itemsLength, $methodsLength, $client)
    {
        $height = 225;
        if ($itemsLength > 0) {
            for ($i = 0; $i < $itemsLength; $i++) {
                $height += 35;
            }
        }
        if ($methodsLength > 0) {
            for ($i = 0; $i < $methodsLength; $i++) {
                $height += 10;
            }
        }
        if ($client) {
            $height += 20;
        }
        return $height;
    }

}
