<?php

namespace Alibe\GeoCodes\Lib\DataObj\Elements\Country;

use Alibe\GeoCodes\Lib\DataObj\BaseDataObj;
use Alibe\GeoCodes\Lib\DataObj\Elements\Standard;

class MottosStructureItem extends BaseDataObj
{
    /**
     * @return array<string, mixed>
     */
    protected function getObjectStructureParser(): array
    {
        return [
            'text' => Standard::class
        ];
    }
}
