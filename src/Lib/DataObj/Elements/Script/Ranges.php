<?php

namespace Alibe\GeoCodes\Lib\DataObj\Elements\Script;

use Alibe\GeoCodes\Lib\DataObj\BaseDataObj;

class Ranges extends BaseDataObj
{
    /**
     * @return array<int, string>
     */
    protected function getObjectStructureParser(): array
    {
        return [Range::class];
    }
}
