<?php

namespace Alibe\GeoCodes\Lib\DataObj\Elements\Country;

use Alibe\GeoCodes\Lib\DataObj\BaseDataObj;
use Alibe\GeoCodes\Lib\Enums\DataSets\Type;

class OtherAppsIds extends BaseDataObj
{
    /**
     * @return array<string, mixed>
     */
    protected function getObjectStructureParser(): array
    {
        return [
            'geoNamesOrg' => Type::INTEGER,
            'wikiData' => Type::STRING,
            'openStreetMap' => OtherAppsIdsOsm::class
        ];
    }
}
