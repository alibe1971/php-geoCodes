<?php

namespace Alibe\GeoCodes\Lib\DataObj\Elements\Country;

use Alibe\GeoCodes\Lib\DataObj\BaseDataObj;
use Alibe\GeoCodes\Lib\DataObj\Currencies as CurrencyList;

class Currencies extends BaseDataObj
{
    /**
     * @return array<string, mixed>
     */
    protected function getObjectStructureParser(): array
    {
        return [
            'legalTenders' => CurrencyList::class,
            'widelyAccepted' => CurrencyList::class,
        ];
    }
}
