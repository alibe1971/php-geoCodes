<?php

namespace Alibe\GeoCodes\Lib\DataObj\Elements;

use Alibe\GeoCodes\Lib\DataObj\BaseDataObj;

class CountryMottos extends BaseDataObj
{
    /**
     * @return array<string, mixed>
     */
    protected function getObjectStructureParser(): array
    {
        return [
            'official' => CountryMottosStructure::class,
            'popular' => CountryMottosStructure::class,
            'founding' => CountryMottosStructure::class,
            'military' => CountryMottosStructure::class,
            'historical' => CountryMottosStructure::class,
            'royal' => CountryMottosStructure::class,
            'presidential' => CountryMottosStructure::class,
        ];
    }
}
