<?php

namespace Alibe\GeoCodes\Lib\DataObj\Elements;

use Alibe\GeoCodes\Lib\DataObj\BaseDataObj;

class CountryLanguages extends BaseDataObj
{
    /**
     * @return array<string, mixed>
     */
    protected function getObjectStructureParser(): array
    {
        return [
//            'official' => CountryLanguagesOfficial::class,
//            'popular' => Language::class,
//            'founding' => CountryMottosStructure::class,
//            'military' => CountryMottosStructure::class,
//            'historical' => CountryMottosStructure::class,
//            'royal' => CountryMottosStructure::class,
//            'presidential' => CountryMottosStructure::class,
        ];
    }
}
