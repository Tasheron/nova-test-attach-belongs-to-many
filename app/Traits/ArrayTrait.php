<?php

namespace App\Traits;

trait ArrayTrait
{
    public static function moveElement(array &$array, int $oldIndex, int $newIndex): void
    {
        $out = array_splice($array, $oldIndex, 1);
        array_splice($array, $newIndex, 0, $out);
    }

    public static function findIndexInArray(array $array, string $key, mixed $value): int|string|false
    {
        return array_search($value, array_column($array, $key));
    }
}
