<?php

namespace Alibe\GeoCodes\Lib\DataObj\Elements\Country;

use Alibe\GeoCodes\Lib\DataObj\BaseDataObj;

class CcIdnItem extends BaseDataObj
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
            'regionsOfUse' => CcIdnItemRegions::class
        ];
    }
}
