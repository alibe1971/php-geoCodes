<?php

namespace Alibe\GeoCodes\Lib\DataObj\Elements;

use Alibe\GeoCodes\Lib\DataObj\BaseDataObj;
use Alibe\GeoCodes\Lib\DataObj\Elements\Country\Currencies;
use Alibe\GeoCodes\Lib\DataObj\Elements\Country\Flags;
use Alibe\GeoCodes\Lib\DataObj\Elements\Country\OtherAppsIds;
use Alibe\GeoCodes\Lib\DataObj\Elements\Country\CcIdn;
use Alibe\GeoCodes\Lib\DataObj\Elements\Country\DialCodes;
use Alibe\GeoCodes\Lib\DataObj\Elements\Country\Mottos;
use Alibe\GeoCodes\Lib\Enums\DataSets\Type;

class Country extends BaseDataObj
{
    /**
     * @var string
     */
    protected string $xmlRootElement = "country";

    /**
     * @return array<string, array<string, array<string, array<string, array<string, string>|string>|string>>>
     */
    protected function getXmlMap(): array
    {
        return [
            'country' => [
                "officialName" => [
                    "@attribute" => "lang",
                    "@tag" => "name"
                ],
                "flags" => [
                    "@type" => [
                        "svg" => "CDATA"
                    ]
                ],
                "mottos" => [
                    "official" => [
                        "@attribute" => "lang",
                        "@tag" => "motto"
                    ],
                    "popular" => [
                        "@attribute" => "lang",
                        "@tag" => "motto"
                    ],
                    "royal" => [
                        "@attribute" => "lang",
                        "@tag" => "motto"
                    ],
                    "presidential" => [
                        "@attribute" => "lang",
                        "@tag" => "motto"
                    ],
                ],
                "currencies" => [
                    "legalTenders" => [
                        "@tag" => "currency"
                    ],
                    "widelyAccepted" => [
                        "@tag" => "currency"
                    ]
                ],
                "ccIdn" => [
                    "@tag" => "idn",
                    '@children' => [
                        "regionsOfUse" => [
                            "@tag" => "region"
                        ]
                    ]
                ],
                "dialCodes" => [
                    "deJure" => [
                        "@tag" => "dial"
                    ],
                    "deFacto" => [
                        "@tag" => "dial"
                    ],
                    "exceptions" => [
                        "@tag" => "dial"
                    ]
                ],
                "demonyms" => [
                    "@tag" => "demonym"
                ],
                "timeZones" => [
                    "@tag" => "tz"
                ],
                "localesIcu" => [
                    "@tag" => "locale"
                ]
            ]
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getObjectStructureParser(): array
    {
        return [
            'alpha2' => Type::STRING,
            'alpha3' => Type::STRING,
            'unM49' => Type::STRING,
            'name' => Type::STRING,
            'fullName' => Type::STRING,
            'officialName' => Standard::class,
            'flags' => Flags::class,
            'dependency' => Type::STRING,
            'mottos' => Mottos::class,
            'currencies' => Currencies::class,
            'dialCodes' => DialCodes::class,
            'ccTld' => Type::STRING,
            'ccIdn' => CcIdn::class,
            'timeZones' => SerializedArray::class,
            'languages' => Type::STRING, //'string',
            'localesIcu' => SerializedArray::class,
            'demonyms' => SerializedArray::class,
            'otherAppsIds' => OtherAppsIds::class,
        ];
    }
}
