<?php

namespace Alibe\GeoCodes\Lib\DataObj\Elements\Country;

use Alibe\GeoCodes\Lib\DataObj\BaseDataObj;

class DialCodesExceptionsItem extends BaseDataObj
{
    /**
     * @return array<string, mixed>
     */
    protected function getObjectStructureParser(): array
    {
        return [
            'code' => 'string',
            'origin' => 'string'
        ];
    }
}
