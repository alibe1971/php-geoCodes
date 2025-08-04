<?php

namespace Alibe\GeoCodes\Lib\DataObj\Elements\Country;

use Alibe\GeoCodes\Lib\DataObj\BaseDataObj;
use Alibe\GeoCodes\Lib\DataObj\Elements\SerializedArray;

class DialCodes extends BaseDataObj
{
    /**
     * @return array<string, mixed>
     */
    protected function getObjectStructureParser(): array
    {
        return [
            'deJure' => SerializedArray::class,
            'deFacto' => SerializedArray::class,
            'exceptions' => DialCodesExceptions::class,
        ];
    }
}
