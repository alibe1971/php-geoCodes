<?php

namespace Alibe\GeoCodes\Lib\DataObj\Elements\Country;

use Alibe\GeoCodes\Lib\DataObj\BaseDataObj;
use Alibe\GeoCodes\Lib\DataObj\Elements\SerializedArray;

class Languages extends BaseDataObj
{
    /**
     * @return array<string, mixed>
     */
    protected function getObjectStructureParser(): array
    {
        return [
            'official' => LanguagesOfficial::class,
            'regional' => SerializedArray::class,
            'widelySpoken' => SerializedArray::class,
            'localCommunities' => SerializedArray::class,
            'extraTerritorialCommunities' => SerializedArray::class,
            'signs' => LanguagesSigns::class,
            'dialects' => SerializedArray::class,
        ];
    }
}
