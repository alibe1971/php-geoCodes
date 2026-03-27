<?php

namespace Alibe\GeoCodes\Lib\DataObj\Elements\Categories;

use Alibe\GeoCodes\Lib\DataObj\BaseDataObj;
use Alibe\GeoCodes\Lib\Enums\DataSets\Type;

class Category extends BaseDataObj
{
    /**
     * @return array<string, mixed>
     */
    protected function getObjectStructureParser(): array
    {
        return [
            'code' => Type::STRING,
            'description' => Type::STRING,
        ];
    }
}
