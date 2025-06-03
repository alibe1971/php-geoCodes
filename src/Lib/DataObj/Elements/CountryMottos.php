<?php

namespace Alibe\GeoCodes\Lib\DataObj\Elements;

use Alibe\GeoCodes\Lib\DataObj\BaseDataObj;

class CountryMottos extends BaseDataObj
{
    /**
     * @return array<string, mixed>
     */
    protected function getObjectStructureParser(): array
    {
        return [
            'official' => Languages::class,
            'popular' => Languages::class,
            'royal' => Languages::class,
            'presidential' => Languages::class,
        ];
    }
}