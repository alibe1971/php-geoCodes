<?php

namespace Alibe\GeoCodes\Lib\DataObj\Elements\Country;

use Alibe\GeoCodes\Lib\DataObj\BaseDataObj;
use Alibe\GeoCodes\Lib\DataObj\Elements\SerializedArray;

class LanguagesSigns extends BaseDataObj
{
    /**
     * @return array<string, mixed>
     */
    protected function getObjectStructureParser(): array
    {
        return [
            'deJure' => SerializedArray::class,
            'recognized' => SerializedArray::class,
            'used' => SerializedArray::class,
        ];
    }
}
