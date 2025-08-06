<?php

namespace Alibe\GeoCodes\Lib\DataObj\Elements;

use Alibe\GeoCodes\Lib\DataObj\BaseDataObj;
use Alibe\GeoCodes\Lib\DataObj\Elements\Country\Currencies;
use Alibe\GeoCodes\Lib\DataObj\Elements\Country\Languages;
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
     * @return array<string, string>
     */
    private function getLanguagesXmlMap(): array
    {
        return [
            "@tag" => "language"
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function getMottosXmlMap(): array
    {
        return [
            "@tag" => "entry",
            '@children' => [
                "text" => [
                    "@tag" => "motto",
                    "@attribute" => "lang",
                ]
            ]
        ];
    }

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
                    "official" => $this->getMottosXmlMap(),
                    "popular" => $this->getMottosXmlMap(),
                    "founding" => $this->getMottosXmlMap(),
                    "military" => $this->getMottosXmlMap(),
                    "historical" => $this->getMottosXmlMap(),
                    "royal" => $this->getMottosXmlMap(),
                    "presidential" => $this->getMottosXmlMap(),
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
                ],
                "languages" => [
                    "official" => [
                        "deJure" => $this->getLanguagesXmlMap(),
                        "deFacto" => $this->getLanguagesXmlMap()
                    ],
                    "regional" => $this->getLanguagesXmlMap(),
                    "widelySpoken" => $this->getLanguagesXmlMap(),
                    "localCommunities" => $this->getLanguagesXmlMap(),
                    "extraTerritorialCommunities" => $this->getLanguagesXmlMap(),
                    "signs" => [
                        "official" => $this->getLanguagesXmlMap(),
                        "recognized" => $this->getLanguagesXmlMap(),
                        "used" => $this->getLanguagesXmlMap(),
                    ],
                    "dialects" => $this->getLanguagesXmlMap(),
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
            'languages' => Languages::class,
            'localesIcu' => SerializedArray::class,
            'demonyms' => SerializedArray::class,
            'otherAppsIds' => OtherAppsIds::class,
        ];
    }
}
