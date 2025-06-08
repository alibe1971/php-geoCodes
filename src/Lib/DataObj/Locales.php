<?php

namespace Alibe\GeoCodes\Lib\DataObj;

class Locales extends BaseDataObj implements \JsonSerializable
{
    /**
     * @return array<string>
     */
    public function jsonSerialize(): array
    {
        return array_values(get_object_vars($this));
    }
}
