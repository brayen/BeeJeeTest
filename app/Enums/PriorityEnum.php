<?php

namespace App\Enums;


use JetBrains\PhpStorm\Pure;

enum PriorityEnum: int
{
    case VeryLow = 1;
    case Low = 2;
    case Normal = 3;
    case High = 4;
    case VeryHigh = 5;

    /**
     * @return array
     */
    #[Pure]
    public static function getAllItems(): array
    {
        $list = [];

        foreach (self::cases() as $item) {
            $list[$item->value] = $item->name;
        }

        return $list;
    }
}
