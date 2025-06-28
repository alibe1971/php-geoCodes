<?php

namespace Alibe\GeoCodes\Lib\DataObj\Elements;

use Alibe\GeoCodes\Lib\DataObj\BaseDataObj;

class CountryCcIdnItem extends BaseDataObj
{
    /**
     * @return array<string, mixed>
     */
    protected function getObjectStructureParser(): array
    {
        return [
            'unicode' => 'string',
            'punycode' => 'string',
            'language' => 'string',
            'regionsOfUse' => CountryCcIdnItemRegions::class
        ];
    }
}
