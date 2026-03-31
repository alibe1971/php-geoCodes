<?php

namespace Alibe\GeoCodes\Lib\DataObj\Elements;

use Alibe\GeoCodes\Lib\DataObj\BaseDataObj;
use Alibe\GeoCodes\Lib\DataObj\Elements\Categories\Category;
use Alibe\GeoCodes\Lib\DataObj\Elements\Script\Unicode;
use Alibe\GeoCodes\Lib\Enums\DataSets\Type;

class Script extends BaseDataObj
{
    /**
     * @var string
     */
    protected string $xmlRootElement = "script";

    /**
     * @return array<string, array<string, array<string, array<string, array<string, string>|string>>>>
     */
    protected function getXmlMap(): array
    {
        return [
            'script' => [
                "unicode" => [
                    "ranges" => [
                        '@tag' => 'range',
                        '@childrenList' => [
                            "@tag" => "edge"
                        ]
                    ]
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
            'code' => Type::STRING,
            'numeric' => Type::STRING,
            'name' => Type::STRING,
            'writingDirection' => Category::class,
            'unicode' => Unicode::class
        ];
    }
}
