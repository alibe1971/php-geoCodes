<?php

namespace Alibe\GeoCodes\Lib\DataObj\Elements\Script;

use Alibe\GeoCodes\Lib\DataObj\BaseDataObj;
use Alibe\GeoCodes\Lib\DataObj\Elements\SerializedArray;

class Range extends BaseDataObj
{
    /**
     * @return array<string, mixed>
     */
    protected function getObjectStructureParser(): array
    {
        return [
            [SerializedArray::class]
        ];
    }
}
