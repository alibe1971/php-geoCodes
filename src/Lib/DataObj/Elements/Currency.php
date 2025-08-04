<?php

namespace Alibe\GeoCodes\Lib\DataObj\Elements;

use Alibe\GeoCodes\Lib\DataObj\BaseDataObj;
use Alibe\GeoCodes\Lib\Enums\DataSets\Type;

class Currency extends BaseDataObj
{
    /**
     * @var string
     */
    protected string $xmlRootElement = "currency";

    /**
     * @return array<string, array<string, array<string, array<string, string>|string>>>
     */
    protected function getXmlMap(): array
    {
        return [
            'currency' => []
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getObjectStructureParser(): array
    {
        return [
            'isoAlpha' => Type::STRING,
            'isoNumber' => Type::STRING,
            'name' => Type::STRING,
            'symbol' => Type::STRING,
            'decimal' => Type::INTEGER
        ];
    }
}
