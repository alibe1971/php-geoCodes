<?php

namespace Alibe\GeoCodes\Lib\DataObj\Elements\Country;

use Alibe\GeoCodes\Lib\DataObj\BaseDataObj;

class CcIdn extends BaseDataObj implements \JsonSerializable
{
    /**
     * @return array<string>
     */
    public function jsonSerialize(): array
    {
        return array_values(get_object_vars($this));
    }

    /**
     * @return array<int, array<int, string>>
     */
    protected function getObjectStructureParser(): array
    {
        return [
            [CcIdnItem::class]
        ];
    }
}
