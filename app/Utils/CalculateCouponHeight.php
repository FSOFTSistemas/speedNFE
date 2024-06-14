<?php

namespace App\Utils;

class CalculateCouponHeight
{
    public static function calculate($itemsLength, $methodsLength, $client)
    {
        $height = 245;
        if ($itemsLength > 0) {
            for ($i = 0; $i < $itemsLength; $i++) {
                $height += 13;
            }
        }
        if ($methodsLength > 0) {
            for ($i = 0; $i < $methodsLength; $i++) {
                $height += 12;
            }
        }
        if ($client) {
            $height += 13;
        }
        return $height;
    }

}
