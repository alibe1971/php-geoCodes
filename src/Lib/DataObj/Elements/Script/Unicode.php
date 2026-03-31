<?php

namespace Alibe\GeoCodes\Lib\DataObj\Elements\Script;

use Alibe\GeoCodes\Lib\DataObj\BaseDataObj;
use Alibe\GeoCodes\Lib\Enums\DataSets\Type;

class Unicode extends BaseDataObj
{
    /**
     * @return array<string, string|int|array<array<string>>>
     */
    protected function getObjectStructureParser(): array
    {
        return [
            'version' => Type::STRING,
            'ranges' => Ranges::class,
            'totalCodePoints' => Type::INTEGER
        ];
    }
}
