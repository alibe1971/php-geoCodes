<?php

namespace Alibe\GeoCodes\Lib\DataObj\Elements\Country;

use Alibe\GeoCodes\Lib\DataObj\BaseDataObj;

class Mottos extends BaseDataObj
{
    /**
     * @return array<string, mixed>
     */
    protected function getObjectStructureParser(): array
    {
        return [
            'official' => MottosStructure::class,
            'popular' => MottosStructure::class,
            'founding' => MottosStructure::class,
            'presidential' => MottosStructure::class,
            'royal' => MottosStructure::class,
            'military' => MottosStructure::class,
            'historical' => MottosStructure::class
        ];
    }
}
