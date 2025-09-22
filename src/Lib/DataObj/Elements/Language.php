<?php

namespace Alibe\GeoCodes\Lib\DataObj\Elements;

use Alibe\GeoCodes\Lib\DataObj\BaseDataObj;
use Alibe\GeoCodes\Lib\Enums\DataSets\Type;

class Language extends BaseDataObj
{
    /**
     * @var string
     */
    protected string $xmlRootElement = "language";

    /**
     * @return array<string, array<string, array<string, array<string, string>|string>>>
     */
    protected function getXmlMap(): array
    {
        return [
            'language' => [
                "scripts" => [
                    "@tag" => "script"
                ],
            ]
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getObjectStructureParser(): array
    {
        return [
            'isoCode' => Type::STRING,
            'name' => Type::STRING,
            'part2b' => Type::STRING,
            'part2t' => Type::STRING,
            'part1' => Type::STRING,
            'glottoCode' => Type::STRING,
            'scope' => Type::STRING,
            'type' => Type::STRING,
            'macroLanguageRef' => Type::STRING,
            'scripts' => SerializedArray::class
        ];
    }
}
