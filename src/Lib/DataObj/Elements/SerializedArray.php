<?php

namespace Alibe\GeoCodes\Lib\DataObj\Elements;

use Alibe\GeoCodes\Lib\DataObj\BaseDataObj;

class SerializedArray extends BaseDataObj implements \JsonSerializable
{
    /**
     * @return array<string>
     */
    public function jsonSerialize(): array
    {
        return array_values(get_object_vars($this));
    }
}
