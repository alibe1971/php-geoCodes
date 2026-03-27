<?php

namespace Alibe\GeoCodes\Tests;

use Alibe\GeoCodes\Lib\DataSets;
use DateTimeZone;
use PHPUnit\Framework\TestCase;

/**
 * @testdox Config Data Structure
 */
final class BaseDataStructureTest extends TestCase
{
    /**
     * @var string
     */
    private static string $dataDir;


    /**
     * @var string
     */
    private static string $defaultLanguage;


    private static string $timeZoneVersion;

    /**
     * @var array<string, mixed>
     */
    private static array $currentStructure = [
        'dataSetName' => null,
        'itemPosition' => null,
        'mainKey' => null,
        'mainKeyValue' => null,
        'propertyName' => null,
        'jsonItem' => null
    ];

    /**
     * @var array<string, array<string, array<string>>>
     */
    private const CATEGORIES = [
        'countries' => [],
        'geoSets' => [
            'scope' => [ 'ORGS', 'GEOG', 'CONV' ]
        ],
        'currencies' => [
            'scope' => [ 'M', 'F', 'P', 'S' ]
        ],
        'languages' => [
            'scope' => [ 'I', 'M', 'S' ],
            'type' => [ 'A', 'C', 'E', 'H', 'L', 'S' ],
        ],
        'scripts' => [
            'writingDirection' => [ 'nla', 'rtl', 'ltr' ]
        ],
    ];

    /**
     * @phpstan-type KeysCheck array{
     *     type: 'string',
     *     validateBCP47?: bool
     * }
     *
     * @phpstan-type DynamicString array{
     *     type: 'string',
     *     regex?: string,
     *     checkDuplicate?: string,
     *     checkExistInDataSet?: string,
     *     keysChecks?: KeysCheck
     * }
     *
     * @phpstan-type FixedKeys array<string, array{
     *     type: 'array',
     *     isDynamic: array{
     *         typeOfArray: 'associative'|'list',
     *         dynamicBuild: DynamicString
     *     }
     * }>
     *
     * @phpstan-type DynamicArray array{
     *     type: 'array',
     *     canBeEmpty?: bool,
     *     isDynamic: array{
     *         typeOfArray: 'associative'|'list',
     *         dynamicBuild: array{
     *             type: 'array',
     *             canBeEmpty?: bool,
     *             isDynamic: array{
     *                 typeOfArray: 'associative'|'list',
     *                 dynamicBuild: array{
     *                     fixedKeys: FixedKeys
     *                 }
     *             }
     *         }|DynamicString
     *     }
     * }
     *
     * @phpstan-type RecurringStructure array{
     *     countries: array{
     *         mottos: DynamicArray,
     *         languages: DynamicArray
     *     }
     * }
     */
    private const RECURRING_STRUCTURE = [
        'countries' => [
            'mottos' => [
                'type' => 'array',
                'canBeEmpty' => true,
                'isDynamic' => [
                    'typeOfArray' => 'list', // or `associative`
                    'dynamicBuild' => [
                        'type' => 'array',
                        'canBeEmpty' => false,
                        'isDynamic' => [
                            'typeOfArray' => 'associative', // or `list`
                            'dynamicBuild' => [
                                'fixedKeys' => [
                                    'text' => [
                                        'type' => 'array',
                                        'isDynamic' => [
                                            'typeOfArray' => 'associative', // or `list`
                                            'dynamicBuild' => [
                                                'type' => 'string',
                                                'keysChecks' => [
                                                    'type' => 'string',
                                                    'validateBCP47' => true
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'languages' => [
                'type' => 'array',
                'canBeEmpty' => true,
                'isDynamic' => [
                    'typeOfArray' => 'list', // or `associative`
                    'dynamicBuild' => [
                        'type' => 'string',
                        'regex' => '/^[a-z]{3}$/',
                        'checkDuplicate' => 'countries.languagesDynBuilt',
                        'checkExistInDataSet' => 'languages.indexes.main'
                    ]
                ]
            ]
        ]
    ];

    /**
     * @var array<string, mixed>
     */
    private static array $geocodeDataStructure = [
        'config' => [],
        'countries' => [
            'indexes' => [
                'main' => [
                    'key' => 'alpha2',
                    'values' => []
                ],
                'secondary' => [
                    'key' => 'unM49',
                    'values' => []
                ]
            ],
            'properties' => [
                'alpha2' => [
                    'type' => 'string',
                    'nullable' => false,
                    'regex' => '/^[A-Z]{2}$/',
                    'checkDuplicate' => 'countries.alpha2'
                ],
                'alpha3' => [
                    'type' => 'string',
                    'nullable' => false,
                    'regex' => '/^[A-Z]{3}$/',
                    'checkDuplicate' => 'countries.alpha3'
                ],
                'unM49' => [
                    'type' => 'string',
                    'nullable' => false,
                    'regex' => '/^\d{3}$/',
                    'checkDuplicate' => 'countries.alpha3'
                ],
                'officialName' => [
                    'type' => 'array',
                    'canBeEmpty' => true,
                    'isDynamic' => [
                        'typeOfArray' => 'associative', // or `list`
                        'dynamicBuild' => [
                            'type' => 'string',
                            'keysChecks' => [
                                'type' => 'string',
                                'validateBCP47' => true
                            ]
                        ]
                    ]
                ],
                'flags' => [
                    'type' => 'array',
                    'canBeEmpty' => false,
                ],
                'flags.emoji' => [
                    'type' => 'string',
                    'nullable' => false,
                    'regex' => '/^\p{Regional_Indicator}{2}$/u',
                ],
                'flags.svg' => [
                    'type' => 'string',
                    'nullable' => false,
                    'isValidSVG' => true,
                ],
                'dependency' => [
                    'type' => 'string',
                    'nullable' => true,
                    'regex' => '/^[A-Z]{2}$/',
                    'checkExistInDataSet' => 'countries.indexes.main'
                ],
                'mottos' => [
                    'type' => 'array',
                    'canBeEmpty' => false,
                ],
                'mottos.official' => self::RECURRING_STRUCTURE['countries']['mottos'],
                'mottos.popular' => self::RECURRING_STRUCTURE['countries']['mottos'],
                'mottos.presidential' => self::RECURRING_STRUCTURE['countries']['mottos'],
                'mottos.royal' => self::RECURRING_STRUCTURE['countries']['mottos'],
                'mottos.military' => self::RECURRING_STRUCTURE['countries']['mottos'],
                'mottos.historical' => self::RECURRING_STRUCTURE['countries']['mottos'],
                'currencies' => [
                    'type' => 'array',
                    'canBeEmpty' => false,
                ],
                'currencies.legalTenders' => [
                    'type' => 'array',
                    'canBeEmpty' => true,
                    'isDynamic' => [
                        'typeOfArray' => 'list', // or `associative`
                        'dynamicBuild' => [
                            'type' => 'string',
                            'regex' => '/^[A-Z]{3}$/',
                            'checkExistInDataSet' => 'currencies.indexes.main'
                        ]
                    ]
                ],
                'currencies.widelyAccepted' => [
                    'type' => 'array',
                    'canBeEmpty' => true,
                    'isDynamic' => [
                        'typeOfArray' => 'list', // or `associative`
                        'dynamicBuild' => [
                            'type' => 'string',
                            'regex' => '/^[A-Z]{3}$/',
                            'checkExistInDataSet' => 'currencies.indexes.main'
                        ]
                    ]
                ],
                'dialCodes' => [
                    'type' => 'array',
                    'canBeEmpty' => false,
                ],
                'dialCodes.deJure' => [
                    'type' => 'array',
                    'canBeEmpty' => true,
                    'isDynamic' => [
                        'typeOfArray' => 'list', // or `associative`
                        'dynamicBuild' => [
                            'type' => 'string',
                            'regex' => '/^\+\d+$/',
                        ]
                    ]
                ],
                'dialCodes.deFacto' => [
                    'type' => 'array',
                    'canBeEmpty' => true,
                    'isDynamic' => [
                        'typeOfArray' => 'list', // or `associative`
                        'dynamicBuild' => [
                            'type' => 'string',
                            'regex' => '/^\+\d+$/',
                        ]
                    ]
                ],
                'dialCodes.exceptions' => [
                    'type' => 'array',
                    'canBeEmpty' => true,
                    'isDynamic' => [
                        'typeOfArray' => 'list', // or `associative`
                        'dynamicBuild' => [
                            'type' => 'array',
                            'canBeEmpty' => false,
                            'isDynamic' => [
                                'typeOfArray' => 'associative', // or `list`
                                'dynamicBuild' => [
                                    'fixedKeys' => [
                                        'code' => [
                                            'type' => 'string',
                                            'regex' => '/^\d+$/',
                                        ],
                                        'origin' => [
                                            'type' => 'string',
                                            'regex' => '/^[A-Z]{2}$/',
                                            'checkExistInDataSet' => 'countries.indexes.main'
                                        ]
                                    ],

                                ]
                            ]
                        ]
                    ]
                ],
                'ccTld' => [
                    'type' => 'string',
                    'nullable' => true,
                    'regex' => '/^\.[a-z]{2}$/',
                ],
                'ccIdn' => [
                    'type' => 'array',
                    'canBeEmpty' => true,
                    'isDynamic' => [
                        'typeOfArray' => 'list', // or `associative`
                        'dynamicBuild' => [
                            'type' => 'array',
                            'canBeEmpty' => false,
                            'isDynamic' => [
                                'typeOfArray' => 'associative', // or `list`
                                'dynamicBuild' => [
                                    'fixedKeys' => [
                                        'unicode' => [
                                            'type' => 'string',
                                            'regex' => '/^\.\S+$/u',
                                            'validateUnicodeIdn' => true,
                                        ],
                                        'punycode' => [
                                            'type' => 'string',
                                            'regex' => '/^\.(xn--)[a-z0-9]+(?:-[a-z0-9]+)*$/i',
                                        ],
                                        'language' => [
                                            'type' => 'string',
                                            'validateBCP47' => true
                                        ],
                                        'regionsOfUse' => [
                                            'type' => 'array',
                                            'isDynamic' => [
                                                'typeOfArray' => 'list', // or `associative`
                                                'dynamicBuild' => [
                                                    'type' => 'string',
                                                    'regex' => '/^[A-Z]{2}$/',
                                                    'checkExistInDataSet' => 'countries.indexes.main'
                                                ]
                                            ]
                                        ]
                                    ],
                                ]
                            ]
                        ]
                    ]
                ],
                'timeZones' => [
                    'type' => 'array',
                    'canBeEmpty' => false,
                    'isDynamic' => [
                        'typeOfArray' => 'list', // or `associative`
                        'dynamicBuild' => [
                            'type' => 'string',
                            'regex' => '/^[A-Za-z][A-Za-z0-9._+-]*(?:\/[A-Za-z0-9][A-Za-z0-9._+-]*)+$/',
                            'checkTimeZone' => true
                        ]
                    ]
                ],
                'languages' => [
                    'type' => 'array',
                    'canBeEmpty' => false,
                ],
                'languages.official' => [
                    'type' => 'array',
                    'canBeEmpty' => false,
                ],
                'languages.official.deJure' => self::RECURRING_STRUCTURE['countries']['languages'],
                'languages.official.deFacto' => self::RECURRING_STRUCTURE['countries']['languages'],
                'languages.regional' => self::RECURRING_STRUCTURE['countries']['languages'],
                'languages.widelySpoken' => self::RECURRING_STRUCTURE['countries']['languages'],
                'languages.localCommunities' => self::RECURRING_STRUCTURE['countries']['languages'],
                'languages.extraTerritorialCommunities' => self::RECURRING_STRUCTURE['countries']['languages'],
                'languages.signs' => [
                    'type' => 'array',
                    'canBeEmpty' => false
                ],
                'languages.signs.official' => self::RECURRING_STRUCTURE['countries']['languages'],
                'languages.signs.recognized' => self::RECURRING_STRUCTURE['countries']['languages'],
                'languages.signs.used' => self::RECURRING_STRUCTURE['countries']['languages'],
                'languages.dialects' => self::RECURRING_STRUCTURE['countries']['languages'],
                'localesIcu' => [
                    'type' => 'array',
                    'canBeEmpty' => true,
                    'isDynamic' => [
                        'typeOfArray' => 'list', // or `associative`
                        'dynamicBuild' => [
                            'type' => 'string',
                            'validateBCP47' => true
                        ]
                    ]
                ],
                'otherAppsIds' => [
                    'type' => 'array',
                    'canBeEmpty' => false
                ],
                'otherAppsIds.geoNamesOrg' => [
                    'type' => 'integer',
                    'nullable' => false,
                    'min' => 1,
                    'checkDuplicate' => 'countries.otherAppsIds.geoNamesOrg'
                ],
                'otherAppsIds.wikiData' => [
                    'type' => 'string',
                    'nullable' => false,
                    'regex' => '/^Q\d+$/',
                    'checkDuplicate' => 'countries.otherAppsIds.wikiData'
                ],
                'otherAppsIds.openStreetMapRelation' => [
                    'type' => 'integer',
                    'nullable' => false,
                    'min' => 1,
                    'checkDuplicate' => 'countries.otherAppsIds.openStreetMapRelation'
                ]
            ]
        ],
        'currencies' => [
            'indexes' => [
                'main' => [
                    'key' => 'isoAlpha',
                    'values' => []
                ],
                'secondary' => [
                    'key' => 'isoNumber',
                    'values' => []
                ]
            ],
            'properties' => [
                'isoAlpha' => [
                    'type' => 'string',
                    'nullable' => false,
                    'regex' => '/^[A-Z]{3}$/',
                    'checkDuplicate' => 'currencies.isoAlpha'
                ],
                'isoNumber' => [
                    'type' => 'string',
                    'nullable' => false,
                    'regex' => '/^\d{3}$/',
                    'checkDuplicate' => 'currencies.isoNumber'
                ],
                'symbol' => [
                    'type' => 'string',
                    'nullable' => true
                ],
                'decimal' => [
                    'type' => 'integer',
                    'nullable' => true
                ],
                'scope' => [
                    'type' => 'array',
                    'canBeEmpty' => false,
                ],
                'scope.code' => [
                    'type' => 'string',
                    'canBeEmpty' => false,
                    'regex' => '/^[A-Z]{1}$/',
                    'checkCategory' => self::CATEGORIES['currencies']['scope']
                ],
            ]
        ],
        'geoSets' => [
            'indexes' => [
                'main' => [
                    'key' => 'internalCode',
                    'values' => []
                ],
                'secondary' => [
                    'key' => 'unM49',
                    'values' => []
                ],
                'checkCountries' => [
                    'doNotSetOnInit' => true,
                    'key' => 'countryCodes',
                    'values' => []
                ]
            ],
            'properties' => [
                'internalCode' => [
                    'type' => 'string',
                    'nullable' => false,
                    'regex' => '/^[A-Z]{4}(?:-[A-Z0-9]{2,8})+$/',
                    'checkDuplicate' => 'geoSets.internalCode'
                ],
                'unM49' => [
                    'type' => 'string',
                    'nullable' => true,
                    'exceptionNullable' => [
                        'key' => 'internalCode',
                        'regex' => '/^GEOG-/',
                    ],
                    'mustBeNull' => [
                        'key' => 'internalCode',
                        'regex' => '/^(?!GEOG-)/',
                    ],
                    'mustBeNotNull' => [
                        'key' => 'internalCode',
                        'regex' => '/^GEOG-/',
                    ],
                    'regex' => '/^[0-9]{3}$/',
                    'checkDuplicate' => 'geoSets.unM49',
                    'checkNotExistInDataSet' => 'countries.indexes.secondary',
                    'conditionCheckNotExistInDataSet' => [
                        'key' => 'internalCode',
                        'regex' => '/^(?!GEOG-AQ)$/',
                    ],
                    'checkExistInDataSet' => 'countries.indexes.secondary',
                    'conditionCheckExistInDataSet' => [
                        'key' => 'internalCode',
                        'regex' => '/^GEOG-AQ$/',
                    ],
                ],
                'scope' => [
                    'type' => 'array',
                    'canBeEmpty' => false,
                ],
                'scope.code' => [
                    'type' => 'string',
                    'canBeEmpty' => false,
                    'regex' => '/^[A-Z]{4}$/',
                    'checkCategory' => self::CATEGORIES['geoSets']['scope']
                ],
                'tags' => [
                    'type' => 'array',
                    'canBeEmpty' => false,
                    'isDynamic' => [
                        'typeOfArray' => 'list', // or `associative`
                        'dynamicBuild' => [
                            'type' => 'string',
                            'regex' => '/^[a-z]{3,}$/',
                        ]
                    ]
                ],
                'countryCodes' => [
                    'type' => 'array',
                    'canBeEmpty' => false,
                    'checkGeographicalZones' => true,
                    'isDynamic' => [
                        'typeOfArray' => 'list', // or `associative`
                        'dynamicBuild' => [
                            'type' => 'string',
                            'checkExistInDataSet' => 'countries.indexes.main'
                        ]
                    ]
                ]
            ]
        ],
        'languages' => [
            'indexes' => [
                'main' => [
                    'key' => 'isoCode',
                    'values' => []
                ],
                'secondary' => [
                    'key' => 'part1',
                    'values' => []
                ]
            ],
            'properties' => [
                'isoCode' => [
                    'type' => 'string',
                    'nullable' => false,
                    'regex' => '/^[a-z]{3}$/',
                    'checkDuplicate' => 'languages.isoCode'
                ],
                'part2b' => [
                    'type' => 'string',
                    'nullable' => true,
                    'regex' => '/^[a-z]{3}$/',
                    'checkDuplicate' => 'languages.part2b'
                ],
                'part2t' => [
                    'type' => 'string',
                    'nullable' => true,
                    'regex' => '/^[a-z]{3}$/',
                    'checkDuplicate' => 'languages.part2t'
                ],
                'part1' => [
                    'type' => 'string',
                    'nullable' => true,
                    'regex' => '/^[a-z]{2}$/',
                    'checkDuplicate' => 'languages.part1'
                ],
                'glottoCode' => [
                    'type' => 'string',
                    'nullable' => true,
                    'regex' => '/^[a-z]{4}\d{4}$/',
                    'checkDuplicate' => null
                ],
                'scope' => [
                    'type' => 'array',
                    'canBeEmpty' => false,
                ],
                'scope.code' => [
                    'type' => 'string',
                    'canBeEmpty' => false,
                    'regex' => '/^[A-Z]{1}$/',
                    'checkCategory' => self::CATEGORIES['languages']['scope']
                ],
                'type' => [
                    'type' => 'array',
                    'canBeEmpty' => false,
                ],
                'type.code' => [
                    'type' => 'string',
                    'canBeEmpty' => false,
                    'regex' => '/^[A-Z]{1}$/',
                    'checkCategory' => self::CATEGORIES['languages']['type']
                ],
                'macroLanguageRef' => [
                    'type' => 'string',
                    'nullable' => true,
                    'regex' => '/^[a-z]{3}$/',
                    'checkExistInDataSet' => 'languages.indexes.main'
                ],
                'scripts' => [
                    'type' => 'array',
                    'nullable' => false,
                    'canBeEmpty' => true,
                    'isDynamic' => [
                        'typeOfArray' => 'list', // or `associative`
                        'dynamicBuild' => [
                            'type' => 'string',
                            'regex' => '/^[A-Z][a-z]{3}$/',
                            'checkExistInDataSet' => 'scripts.indexes.main'
                        ]
                    ]
                ]
            ]
        ],
        'scripts' => [
            'indexes' => [
                'main' => [
                    'key' => 'code',
                    'values' => []
                ],
                'secondary' => [
                    'key' => 'numeric',
                    'values' => []
                ]
            ],
            'properties' => [
                'code' => [
                    'type' => 'string',
                    'regex' => '/^[A-Z][a-z]{3}$/',
                    'checkDuplicate' => 'scripts.code'
                ],
                'numeric' => [
                    'type' => 'string',
                    'regex' => '/^[0-9]{3}$/',
                    'checkDuplicate' => 'scripts.numeric'
                ],
                'writingDirection' => [
                    'type' => 'array',
                    'canBeEmpty' => false,
                ],
                'writingDirection.code' => [
                    'type' => 'string',
                    'canBeEmpty' => false,
                    'regex' => '/^[a-z]{3}$/',
                    'checkCategory' => self::CATEGORIES['scripts']['writingDirection']
                ],
                'unicode' => [],
                'unicode.version' => [
                    'type' => 'string',
                    'nullable' => true,
                    'regex' => '/^[0-9]+\.[0-9]$/',
                ],
                'unicode.ranges' => [
                    'type' => 'array',
                    'canBeEmpty' => true,
                    'isDynamic' => [
                        'typeOfArray' => 'list', // or `associative`
                        'dynamicBuild' => [
                            'type' => 'array',
                            'canBeEmpty' => false,
                            'minCount' => 1,
                            'maxCount' => 2,
                            'isDynamic' => [
                                'typeOfArray' => 'list', // or `associative`
                                'dynamicBuild' => [
                                    'type' => 'string',
                                    'regex' => '/^(?:[0-9A-F]{4,5}|10[0-9A-F]{4})$/',
                                    'checkHexadecimalContinuity' => true,
                                ]
                            ]
                        ]
                    ]
                ],
                'unicode.totalCodePoints' => [
                    'type' => 'integer',
                    'min' => 0

                ]
            ],
            'afterRecordTest' => [
                'checkIntegrityOfRangesPoints' => true
            ]
        ]
    ];

    /**
     * @var array<string, mixed>
     */
    private static array $geocodeTranslationsProperties = [
        'countries' => [
            'indexes' => [
                'main' => [
                    'key' => 'translationIndex',
                    'values' => []
                ]
            ],
            'properties' => [
                'translationIndex' => [
                    'type' => 'string',
                    'regex' => '/^[A-Z]{2}$/',
                    'checkDuplicate' => null,
                    'checkExistInDataSet' => 'countries.indexes.main'
                ],
                'name' => [
                    'type' => 'string',
                    'defaultNotNullable' => true,
                    'checkDuplicate' => null
                ],
                'fullName' => [
                    'type' => 'string',
                    'defaultNotNullable' => true,
                    'checkDuplicate' => null
                ],
                'demonyms' => [
                    'type' => 'array'
                ],
                'keywords' => [
                    'type' => 'array'
                ]
            ]
        ],
        'geoSets' => [
            'indexes' => [
                'main' => [
                    'key' => 'translationIndex',
                    'values' => []
                ]
            ],
            'properties' => [
                'translationIndex' => [
                    'type' => 'string',
                    'regex' => '/^[A-Z]{4}(?:-[A-Z0-9]{2,8})+$/',
                    'checkDuplicate' => null,
                    'checkExistInDataSet' => 'geoSets.indexes.main'
                ],
                'name' => [
                    'type' => 'string',
                    'defaultNotNullable' => true,
                    'checkDuplicate' => null
                ]
            ]
        ],
        'currencies' => [
            'indexes' => [
                'main' => [
                    'key' => 'translationIndex',
                    'values' => []
                ]
            ],
            'properties' => [
                'translationIndex' => [
                    'type' => 'string',
                    'regex' => '/^[A-Z]{3}$/',
                    'checkDuplicate' => null,
                    'checkExistInDataSet' => 'currencies.indexes.main'
                ],
                'name' => [
                    'type' => 'string',
                    'defaultNotNullable' => true,
                    'checkDuplicate' => null
                ]
            ]
        ],
        'languages' => [
            'indexes' => [
                'main' => [
                    'key' => 'translationIndex',
                    'values' => []
                ]
            ],
            'properties' => [
                'translationIndex' => [
                    'type' => 'string',
                    'regex' => '/^[a-z]{3}$/',
                    'checkDuplicate' => null,
                    'checkExistInDataSet' => 'languages.indexes.main'
                ],
                'name' => [
                    'type' => 'string',
                    'defaultNotNullable' => true,
                    'checkDuplicate' => null
                ]
            ]
        ],
        'scripts' => [
            'indexes' => [
                'main' => [
                    'key' => 'translationIndex',
                    'values' => []
                ]
            ],
            'properties' => [
                'translationIndex' => [
                    'type' => 'string',
                    'regex' => '/^[A-Z][a-z]{3}$/',
                    'checkDuplicate' => null,
                    'checkExistInDataSet' => 'scripts.indexes.main'
                ],
                'name' => [
                    'type' => 'string',
                    'defaultNotNullable' => true,
                    'checkDuplicate' => null
                ]
            ]
        ]
    ];

    /**
     * @var array<string, mixed>
     */
    private static array $geocodeTranslationsCategoriesProperties = [
        'countries' => [],
        'geoSets' => [
            'indexes' => [
                'main' => [
                    'key' => 'translationIndex',
                    'values' => []
                ],
                'scope' => [
                    'key' => 'scope',
                    'useKeys' => true,
                    'values' => []
                ]
            ],
            "properties" => [
                'translationIndex' => [
                    'type' => 'string',
                    'checkDuplicate' => null
                ],
                'scope' => [
                    'type' => 'array',
                    'canBeEmpty' => false,
                    'isDynamic' => [
                        'typeOfArray' => 'associative', // or `list`
                        'dynamicBuild' => [
                            'type' => 'string',
                            'defaultNotNullable' => true,
                            'keysChecks' => [
                                'type' => 'string',
                                'checkCategory' => self::CATEGORIES['geoSets']['scope']
                            ]
                        ]
                    ]
                ]
            ]
        ],
        'currencies' => [
            'indexes' => [
                'main' => [
                    'key' => 'translationIndex',
                    'values' => []
                ],
                'scope' => [
                    'key' => 'scope',
                    'useKeys' => true,
                    'values' => []
                ]
            ],
            "properties" => [
                'translationIndex' => [
                    'type' => 'string',
                    'checkDuplicate' => null
                ],
                'scope' => [
                    'type' => 'array',
                    'canBeEmpty' => false,
                    'isDynamic' => [
                        'typeOfArray' => 'associative', // or `list`
                        'dynamicBuild' => [
                            'type' => 'string',
                            'defaultNotNullable' => true,
                            'keysChecks' => [
                                'type' => 'string',
                                'checkCategory' => self::CATEGORIES['currencies']['scope']
                            ]
                        ]
                    ]
                ]
            ]
        ],
        'languages' => [
            'indexes' => [
                'main' => [
                    'key' => 'translationIndex',
                    'values' => []
                ],
                'scope' => [
                    'key' => 'scope',
                    'useKeys' => true,
                    'values' => []
                ],
                'type' => [
                    'key' => 'type',
                    'useKeys' => true,
                    'values' => []
                ]
            ],
            "properties" => [
                'translationIndex' => [
                    'type' => 'string',
                    'checkDuplicate' => null
                ],
                'scope' => [
                    'type' => 'array',
                    'canBeEmpty' => false,
                    'isDynamic' => [
                        'typeOfArray' => 'associative', // or `list`
                        'dynamicBuild' => [
                            'type' => 'string',
                            'defaultNotNullable' => true,
                            'keysChecks' => [
                                'type' => 'string',
                                'checkCategory' => self::CATEGORIES['languages']['scope']
                            ]
                        ]
                    ]
                ],
                'type' => [
                    'type' => 'array',
                    'canBeEmpty' => false,
                    'isDynamic' => [
                        'typeOfArray' => 'associative', // or `list`
                        'dynamicBuild' => [
                            'type' => 'string',
                            'defaultNotNullable' => true,
                            'keysChecks' => [
                                'type' => 'string',
                                'checkCategory' => self::CATEGORIES['languages']['type']
                            ]
                        ]
                    ]
                ]
            ]
        ],
        'scripts' => [
            'indexes' => [
                'main' => [
                    'key' => 'translationIndex',
                    'values' => []
                ],
                'writingDirection' => [
                    'key' => 'writingDirection',
                    'useKeys' => true,
                    'values' => []
                ]
            ],
            "properties" => [
                'translationIndex' => [
                    'type' => 'string',
                    'checkDuplicate' => null
                ],
                'writingDirection' => [
                    'type' => 'array',
                    'canBeEmpty' => false,
                    'isDynamic' => [
                        'typeOfArray' => 'associative', // or `list`
                        'dynamicBuild' => [
                            'type' => 'string',
                            'defaultNotNullable' => true,
                            'keysChecks' => [
                                'type' => 'string',
                                'checkCategory' => self::CATEGORIES['scripts']['writingDirection']
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ];

    /**
     * @var array<string, mixed>
     */
    private static array $geocodeDataSet = [];

    /**
     * @var array<string, mixed>
     */
    private static array $duplicatesControl = [];

    /**
     * Setup
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        self::$dataDir = dirname(__DIR__) . '/src/Data';
        self::$timeZoneVersion = timezone_version_get();
    }

    /**
     * @test
     * @return void
     */
    public function testDataSourceIntegrity(): void
    {
        $this->assertDirectoryExists(self::$dataDir, 'The data directory `' . self::$dataDir . '` does not exist');
        $this->assertDirectoryIsReadable(self::$dataDir, 'The data directory `' . self::$dataDir . '` is not readable');

        foreach (self::$geocodeDataStructure as $dataSetName => $structure) {
            $dataSetPath = self::$dataDir . '/' . $dataSetName . '.php';
            $this->parseDataSet($dataSetName, $dataSetPath, $structure);
        }
    }

    /**
     * @test
     * @depends testDataSourceIntegrity
     * @return void
     */
    public function testValidationConfigFile(): void
    {
        $this->assertArrayHasKey(
            'settings',
            self::$geocodeDataSet['config'],
            'The section `settings` is not present in the config file'
        );
        $this->assertIsArray(
            self::$geocodeDataSet['config']['settings'],
            'The section `settings` must be an array'
        );
        $this->assertNotEmpty(
            self::$geocodeDataSet['config']['settings'],
            'The section `settings` cannot be empty'
        );

        $this->assertArrayHasKey(
            'languages',
            self::$geocodeDataSet['config']['settings'],
            'The section `settings.languages` is not present inside the `settings`'
        );
        $this->assertIsArray(
            self::$geocodeDataSet['config']['settings']['languages'],
            'The section `settings.languages` must be an array'
        );
        $this->assertNotEmpty(
            self::$geocodeDataSet['config']['settings']['languages'],
            'The section `settings.languages` cannot be empty'
        );

        $this->assertArrayHasKey(
            'default',
            self::$geocodeDataSet['config']['settings']['languages'],
            'The property `settings.languages.default` is not present inside the `languages`'
        );
        $this->assertIsString(
            self::$geocodeDataSet['config']['settings']['languages']['default'],
            'The property `settings.languages.default` must be a string'
        );
        $this->assertNotEmpty(
            self::$geocodeDataSet['config']['settings']['languages']['default'],
            'The property `settings.languages.default` cannot be empty'
        );
        self::$defaultLanguage = self::$geocodeDataSet['config']['settings']['languages']['default'];

        $this->assertArrayHasKey(
            'inPackage',
            self::$geocodeDataSet['config']['settings']['languages'],
            'The property `settings.languages.inPackage` is not present inside the `languages`'
        );
        $this->assertIsArray(
            self::$geocodeDataSet['config']['settings']['languages']['inPackage'],
            'The property `settings.languages.inPackage` must be an array'
        );
        $this->assertNotEmpty(
            self::$geocodeDataSet['config']['settings']['languages']['inPackage'],
            'The property `settings.languages.inPackage` cannot be empty'
        );
        $this->assertContains(
            self::$defaultLanguage,
            self::$geocodeDataSet['config']['settings']['languages']['inPackage'],
            'The default language `'
            . self::$defaultLanguage .
            '` is not present inside the configuration set of the `settings.languages.inPackage` section'
        );
    }

    /**
     * @test
     * @depends testDataSourceIntegrity
     * @return void
     */
    public function testValidationCountryData(): void
    {
        $this->commonDataTests('countries');
    }

    /**
     * @test
     * @depends testDataSourceIntegrity
     * @return void
     */
    public function testValidationGeoSetsData(): void
    {
        $this->commonDataTests('geoSets');
        /** Additional check for matches in the `countries` dataset */
        $diff = array_diff(
            self::$geocodeDataStructure['countries']['indexes']['main']['values'],
            self::$geocodeDataStructure['geoSets']['indexes']['checkCountries']['values'],
        );
        $this->assertTrue(
            empty($diff),
            'The following country codes in the `countries` dataset '
            . 'have no correspondence in the `geoSets` geographic zone dataset: ' . "\n"
            . '[' . implode(', ', $diff) . ']'
        );
    }

    /**
     * @test
     * @depends testDataSourceIntegrity
     * @return void
     */
    public function testValidationCurrencyData(): void
    {
        $this->commonDataTests('currencies');
    }

    /**
     * @test
     * @depends testDataSourceIntegrity
     * @return void
     */
    public function testValidationLanguagesData(): void
    {
        $this->commonDataTests('languages');
    }

    /**
     * @test
     * @depends testDataSourceIntegrity
     * @return void
     */
    public function testValidationScriptsData(): void
    {
        $this->commonDataTests('scripts');
    }

    /**
     * @test
     * @depends testValidationConfigFile
     * @return void
     */
    public function testDataTranslationsSourceIntegrity(): void
    {
        foreach (self::$geocodeDataSet['config']['settings']['languages']['inPackage'] as $language) {
            foreach (array_keys(self::$geocodeTranslationsProperties) as $translationsSet) {
                $dataSetName = 'translations.' . $language . '.' . $translationsSet;
                $dataSetPath = self::$dataDir . '/Translations/' . $language . '/' . $translationsSet . '.php';
                self::$geocodeDataStructure[$dataSetName] = self::$geocodeTranslationsProperties[$translationsSet];
                $this->parseDataSet($dataSetName, $dataSetPath, self::$geocodeDataStructure[$dataSetName]);

                /** Categories */
                if (!empty(self::$geocodeTranslationsCategoriesProperties[$translationsSet])) {
                    $dataSetCatName = 'translationsCategory.' . $language . '.' . $translationsSet;
                    $dataSetPath = self::$dataDir . '/Translations/' . $language . '/Categories/'
                        . $translationsSet . '.php';
                    self::$geocodeDataStructure[$dataSetCatName] =
                        self::$geocodeTranslationsCategoriesProperties[$translationsSet];
                    $this->parseDataSet($dataSetCatName, $dataSetPath, self::$geocodeDataStructure[$dataSetCatName]);

                    /** Additional check for matches between the categories and the related translation datasets */
                    if ($language == self::$defaultLanguage) {
                        foreach (self::$geocodeDataStructure[$dataSetCatName]['indexes'] as $index) {
                            if (isset(self::CATEGORIES[$translationsSet][$index['key']])) {
                                $diff = array_diff(
                                    self::CATEGORIES[$translationsSet][$index['key']],
                                    $index['values'],
                                );
                                $this->assertEmpty(
                                    $diff,
                                    'Translation Categories dataset: `' . $translationsSet . '`' . "\n"
                                    . 'Language: `' . $language . '`' . "\n"
                                    . 'The following key codes in the `' . $translationsSet . '`, '
                                    . 'Category `' . $index['key'] . '` dataset '
                                    . 'have no correspondence in the related translation data: ' . "\n"
                                    . '[' . implode(', ', $diff) . ']'
                                );
                            }
                        }
                    }
                }

                /** Additional check for matches between the main and the related translation datasets */
                if ($language == self::$defaultLanguage) {
                    $diff = array_diff(
                        self::$geocodeDataStructure[$translationsSet]['indexes']['main']['values'],
                        self::$geocodeDataStructure[$dataSetName]['indexes']['main']['values'],
                    );
                    $this->assertTrue(
                        empty($diff),
                        'Translation dataset: `' . $translationsSet . '`' . "\n"
                        . 'Language: `' . $language . '`' . "\n"
                        . 'The following key codes in the `' . $translationsSet . '` dataset '
                        . 'have no correspondence in the related translation data: ' . "\n"
                        . '[' . implode(', ', $diff) . ']'
                    );
                }
            }
        }
    }

    /**
     * @test
     * @depends testDataTranslationsSourceIntegrity
     * @return void
     */
    public function testValidationTranslationsData(): void
    {
        foreach (self::$geocodeDataSet['config']['settings']['languages']['inPackage'] as $language) {
            foreach (array_keys(self::$geocodeTranslationsProperties) as $translationsSet) {
                $dataSetName = 'translations.' . $language . '.' . $translationsSet;
                $this->commonDataTests($dataSetName, $language);
            }

            /** Categories */
            foreach (array_keys(self::$geocodeTranslationsCategoriesProperties) as $translationsSet) {
                if (!empty(self::$geocodeTranslationsCategoriesProperties[$translationsSet])) {
                    $dataSetName = 'translationsCategory.' . $language . '.' . $translationsSet;
                    self::$geocodeDataSet[$dataSetName] = [
                        $translationsSet => self::$geocodeDataSet[$dataSetName],
                    ];
                    $this->commonDataTests($dataSetName, $language);
                }
            }
        }
    }

    /**
     * @param string $dataSetName
     * @param string|null $transLanguage
     * @return void
     */
    private function commonDataTests(string $dataSetName, string $transLanguage = null): void
    {
        $mainKey = self::$currentStructure['mainKey'] =
            self::$geocodeDataStructure[$dataSetName]['indexes']['main']['key'];
        self::$currentStructure['dataSetName'] = $dataSetName;

        /**
         * Execute the iteration of the dataset item
         */
        self::$currentStructure['itemPosition'] = 0;
        foreach (self::$geocodeDataSet[$dataSetName] as $idx => $item) {
            self::$currentStructure['propertyName'] = self::$currentStructure['mainKeyValue'] = null;

            /**
             * The translations have a different structure.
             * So, it is needed to align the structure to execute this method.
             */
            if ($transLanguage) {
                if (is_string($idx)) {
                    self::$currentStructure['mainKeyValue'] = $idx;
                }
                $item[self::$geocodeDataStructure[$dataSetName]['indexes']['main']['key']] = $idx;
            }

            $this->assertIsArray(
                $item,
                $this->getErrorMessage(
                    'The item must be an array. '
                    . '"' . gettype($item) . '" returned.'
                )
            );

            /**
             * In the case of `countries`, the `languages` must not be repeated within the entire group.
             * Therefore, the duplicate container must be reset for each record.
             */
            if ($dataSetName == 'countries') {
                self::$duplicatesControl['countries']['languagesDynBuilt'] = [];
            }

            /**
             * In the case of `scripts`, the `ranges` must be checked for hexadecimal continuity.
             * Therefore, there are some variables that must be reset for each record.
             * The anchor for the check is anyway the duplicate control
             */
            if ($dataSetName == 'scripts') {
                self::$duplicatesControl['scripts']['rangesDynBuilt'] = [
                    'checkOrder' => [],
                    'countPoints' => 0
                ];
            }

            $processed = [];
            $queue = array_keys(self::$geocodeDataStructure[$dataSetName]['properties']);

            /**
             * Execute the iteration of the structure properties
             */
            while (!empty($queue)) {
                $property = (string) array_shift($queue);

                if (isset($processed[$property])) {
                    continue;
                }
                $processed[$property] = true;


                $functions = self::$geocodeDataStructure[$dataSetName]['properties'][$property];
                $propertyToCheck = self::$currentStructure['propertyName'] = $property;
                $itemPartWhereToCheck = $item;
                $parentProperty = null;
                if (preg_match('/\./', $property)) {
                    $propArr = explode('.', $property);
                    $propertyToCheck = array_pop($propArr);
                    $parentProperty = join('.', $propArr);
                    $itemPartWhereToCheck = (array) $this->arrayGetDot($item, $parentProperty);
                }

                if ($transLanguage) {
                    if (
                        $transLanguage == self::$defaultLanguage &&
                        array_key_exists('defaultNotNullable', $functions) &&
                        $functions['defaultNotNullable'] === true
                    ) {
                        $functions['nullable'] = false;
                        $functions['canBeEmpty'] = false;
                    } else {
                        $functions['nullable'] = true;
                        $functions['canBeEmpty'] = true;
                    }

                    if (array_key_exists('checkDuplicate', $functions)) {
                        $functions['checkDuplicate'] = $dataSetName . '.' . $propertyToCheck;
                    }
                }

                self::$currentStructure['jsonItem'] = ($property == $mainKey) ? json_encode($item) ?: null : null;

                /** Check if the property exists */
                if (
                    (
                        array_key_exists('requireParent', $functions) &&
                        is_string($functions['requireParent']) &&
                        $this->arrayGetDot($item, $functions['requireParent']) === null
                    ) ||
                    (
                        array_key_exists('noKeyExistsCheck', $functions) &&
                        !empty($functions['noKeyExistsCheck']) &&
                        !array_key_exists($propertyToCheck, $itemPartWhereToCheck)
                    )
                ) {
                    continue;
                }

                $this->assertArrayHasKey(
                    $propertyToCheck,
                    $itemPartWhereToCheck,
                    $this->getErrorMessage('Missing property')
                );

                self::$currentStructure['mainKeyValue'] = $item[$mainKey];
                self::$currentStructure['jsonItem'] = null;

                $propertyFunctions = [
                    'key' => null,
                    'value' => $functions
                ];
                $propertyValues = [
                    'key' => $propertyToCheck,
                    'value' => $this->arrayGetDot($itemPartWhereToCheck, $propertyToCheck)
                ];

                if (
                    array_key_exists('keysChecks', $functions) &&
                    !empty($functions['keysChecks']) &&
                    is_array($functions['keysChecks'])
                ) {
                    $propertyFunctions['key'] = $functions['keysChecks'];
                }

                foreach ($propertyFunctions as $name => $functions) {
                    if (empty($functions)) {
                        continue;
                    }
                    $itemPropertyValue = $propertyValues[$name];

                    $itemPropertyType = gettype($itemPropertyValue);
                    $isNullable = (array_key_exists('nullable', $functions) && $functions['nullable']);

                    if (
                        !empty($functions['exceptionNullable']['key']) &&
                        !empty($functions['exceptionNullable']['regex'])
                    ) {
                        $getExceptionNullable = $this->arrayGetDot($item, $functions['exceptionNullable']['key']);
                        if (
                            preg_match(
                                $functions['exceptionNullable']['regex'],
                                is_string($getExceptionNullable) ? $getExceptionNullable : ''
                            )
                        ) {
                            $isNullable = !$isNullable;
                        }
                    }

                    /** Check the type of the element */
                    if (array_key_exists('type', $functions) && !empty($functions['type'])) {
                        $errAdditionalMex = ($isNullable) ? ' or null' : '';
                        $checkType = $functions['type'] == $itemPropertyType;
                        $this->assertTrue(
                            ($isNullable) ?
                                $checkType || is_null($itemPropertyValue) : $checkType,
                            $this->getErrorMessage(
                                'The property\'s type must be "' . $functions['type'] . '"'
                                . $errAdditionalMex . '. '
                                . '"' . $itemPropertyType . '" returned.'
                            )
                        );
                    }
                    if (
                        array_key_exists('mustBeNull', $functions) &&
                        !empty($functions['mustBeNull'])
                    ) {
                        $getMustBeNull = $this->arrayGetDot($item, $functions['mustBeNull']['key']);
                        if (
                            preg_match(
                                $functions['mustBeNull']['regex'],
                                is_string($getMustBeNull) ? $getMustBeNull : ''
                            )
                        ) {
                            $this->assertNull(
                                $itemPropertyValue,
                                $this->getErrorMessage(
                                    'The property ' . $name . '\'s type must be null.'
                                )
                            );
                        }
                    }
                    if (
                        array_key_exists('mustBeNotNull', $functions) &&
                        !empty($functions['mustBeNotNull'])
                    ) {
                        $getMustBeNotNull = $this->arrayGetDot($item, $functions['mustBeNotNull']['key']);
                        if (
                            preg_match(
                                $functions['mustBeNotNull']['regex'],
                                is_string($getMustBeNotNull) ? $getMustBeNotNull : ''
                            )
                        ) {
                            $this->assertNotNull(
                                $itemPropertyValue,
                                $this->getErrorMessage(
                                    'The property ' . $name . '\'s type must be NOT null.'
                                )
                            );
                        }
                    }

                    /** Check if empty is allowed */
                    if (!array_key_exists('canBeEmpty', $functions) || !$functions['canBeEmpty']) {
                        if (is_string($itemPropertyValue)) {
                            $this->assertNotEmpty(
                                trim(preg_replace('/\s+/u', '', $itemPropertyValue)),
                                $this->getErrorMessage('The property cannot be an empty string')
                            );
                        } elseif (is_array($itemPropertyValue)) {
                            $this->assertNotEmpty(
                                $itemPropertyValue,
                                $this->getErrorMessage('The property ' . $name . ' cannot be an empty array')
                            );
                        }
                    }

                    /** Check the regex */
                    if (array_key_exists('regex', $functions) && $functions['regex'] && is_string($itemPropertyValue)) {
                        $this->assertMatchesRegularExpression(
                            $functions['regex'],
                            $itemPropertyValue,
                            $this->getErrorMessage(
                                'The property ' . $name . ' `' . $itemPropertyValue
                                . '`  does not match with the pattern `' . $functions['regex'] . '`'
                            )
                        );
                    }

                    /** Check the minimum and maximum in case of integer */
                    if (is_integer($itemPropertyValue)) {
                        if (array_key_exists('min', $functions) && $functions['min']) {
                            $this->assertGreaterThanOrEqual(
                                $functions['min'],
                                $itemPropertyValue,
                                $this->getErrorMessage(
                                    'The property ' . $name . ' must be greater or equal to `' . $functions['min'] . '`'
                                )
                            );
                        }
                        if (array_key_exists('max', $functions) && $functions['max']) {
                            $this->assertLessThanOrEqual(
                                $functions['max'],
                                $itemPropertyValue,
                                $this->getErrorMessage(
                                    'The property ' . $name . ' cannot be greater than `' . $functions['max'] . '`'
                                )
                            );
                        }
                    }


                    /** Check the minimum and maximum allowed elements in case of array */
                    if (is_array($itemPropertyValue) and !empty($itemPropertyValue)) {
                        $arraySize = count($itemPropertyValue);
                        if (array_key_exists('minCount', $functions) && $functions['minCount']) {
                            $this->assertGreaterThanOrEqual(
                                $functions['minCount'],
                                $arraySize,
                                $this->getErrorMessage(
                                    'The property ' . $name . ' must have a number of elements greater or equal to `'
                                    . $functions['minCount'] . '`. Resulting: ' . $arraySize
                                )
                            );
                        }
                        if (array_key_exists('maxCount', $functions) && $functions['maxCount']) {
                            $this->assertLessThanOrEqual(
                                $functions['maxCount'],
                                $arraySize,
                                $this->getErrorMessage(
                                    'The property ' . $name . ' cannot have a number of elements greater than `'
                                    . $functions['maxCount'] . '`. Resulting: ' . $arraySize
                                )
                            );
                        }
                    }

                    /** Check the duplicates */
                    if (
                        array_key_exists('checkDuplicate', $functions) &&
                        $functions['checkDuplicate'] &&
                        is_string($itemPropertyValue) &&
                        !empty($itemPropertyValue)
                    ) {
                        $haystack = $this->arrayGetDot(self::$duplicatesControl, $functions['checkDuplicate']) ?? [];
                        if (is_array($haystack)) {
                            $this->assertNotContains(
                                $itemPropertyValue,
                                $haystack,
                                $this->getErrorMessage('Duplicated ' . $name . ' for the property')
                            );
                            $this->addItemToList(
                                self::$duplicatesControl,
                                $functions['checkDuplicate'],
                                $itemPropertyValue
                            );
                        }
                    }

                    /** Check the category */
                    if (
                        array_key_exists('checkCategory', $functions) &&
                        is_array($functions['checkCategory']) &&
                        !empty($functions['checkCategory']) &&
                        is_string($itemPropertyValue)
                    ) {
                        $this->assertContains(
                            $itemPropertyValue,
                            $functions['checkCategory'],
                            $this->getErrorMessage(
                                'The property ' . $name . ' is not included in the allowed values:' .  "\n" .
                                '[`' . implode('`, `', $functions['checkCategory']) . '`]'
                            )
                        );
                    }

                    /** Check if the value exists in a preset structure values */
                    $getConditionCheckExistInDataSet = !empty($functions['conditionCheckExistInDataSet']) ?
                        $this->arrayGetDot($item, $functions['conditionCheckExistInDataSet']['key']) : null;
                    if (
                        array_key_exists('checkExistInDataSet', $functions) &&
                        $functions['checkExistInDataSet'] &&
                        is_string($itemPropertyValue) &&
                        (
                            !array_key_exists('conditionCheckExistInDataSet', $functions) ||
                            preg_match(
                                $functions['conditionCheckExistInDataSet']['regex'],
                                is_string($getConditionCheckExistInDataSet) ?
                                    $getConditionCheckExistInDataSet : ''
                            )
                        )
                    ) {
                        $getCheckExistInDataSetKey = $this->arrayGetDot(
                            self::$geocodeDataStructure,
                            $functions['checkExistInDataSet'] . '.key'
                        );
                        $haystack =
                            $this->arrayGetDot(
                                self::$geocodeDataStructure,
                                $functions['checkExistInDataSet'] . '.values'
                            ) ?? [];
                        if (is_array($haystack)) {
                            $this->assertContains(
                                $itemPropertyValue,
                                $haystack,
                                $this->getErrorMessage(
                                    'The property ' . $name . ' `' . $itemPropertyValue . '` '
                                    . 'does not exist for the key '
                                    . '`'
                                    . (is_string($getCheckExistInDataSetKey) ? $getCheckExistInDataSetKey : '')
                                    . '`'
                                    . ' in the dataset '
                                    . '`' . explode('.', $functions['checkExistInDataSet'])[0] . '`'
                                )
                            );
                        }
                    }

                    /** Check if the value DOESN'T exist in a preset structure values */
                    $getConditionCheckNotExistInDataSet = !empty($functions['conditionCheckNotExistInDataSet']) ?
                        $this->arrayGetDot($item, $functions['conditionCheckNotExistInDataSet']['key']) : null;
                    if (
                        array_key_exists('checkNotExistInDataSet', $functions) &&
                        $functions['checkNotExistInDataSet'] &&
                        is_string($itemPropertyValue) &&
                        (
                            !array_key_exists('conditionCheckNotExistInDataSet', $functions) ||
                            preg_match(
                                $functions['conditionCheckNotExistInDataSet']['regex'],
                                is_string($getConditionCheckNotExistInDataSet) ?
                                    $getConditionCheckNotExistInDataSet : ''
                            )
                        )
                    ) {
                        $getCheckNotExistInDataSetKey = $this->arrayGetDot(
                            self::$geocodeDataStructure,
                            $functions['checkExistInDataSet'] . '.key'
                        );
                        $haystack =
                            $this->arrayGetDot(
                                self::$geocodeDataStructure,
                                $functions['checkNotExistInDataSet'] . '.values'
                            ) ?? [];
                        if (is_array($haystack)) {
                            $this->assertNotContains(
                                $itemPropertyValue,
                                $haystack,
                                $this->getErrorMessage(
                                    'The property ' . $name . ' `' . $itemPropertyValue . '` '
                                    . 'must not exist for the key '
                                    . '`'
                                    . (is_string($getCheckNotExistInDataSetKey) ? $getCheckNotExistInDataSetKey : '')
                                    . '`'
                                    . ' in the dataset '
                                    . '`' . explode('.', $functions['checkNotExistInDataSet'])[0] . '`'
                                )
                            );
                        }
                    }

                    /** Check for dynamic array */
                    if (
                        array_key_exists('isDynamic', $functions) &&
                        !empty($functions['isDynamic']) &&
                        is_array($itemPropertyValue) &&
                        !empty($itemPropertyValue)
                    ) {
                        switch ($functions['isDynamic']['typeOfArray']) {
                            case 'list':
                                $this->assertTrue(
                                    Utils::isList($itemPropertyValue),
                                    $this->getErrorMessage(
                                        'The property ' . $name . ' must be must be a `list` of items. '
                                        . '`Associative array` returned.'
                                    )
                                );
                                break;
                            case 'associative':
                                $this->assertFalse(
                                    Utils::isList($itemPropertyValue),
                                    $this->getErrorMessage(
                                        'The property ' . $name . ' must be must be an `Associative array`. '
                                        . '`List` of items returned.'
                                    )
                                );
                                break;
                            default:
                        }

                        /** Dynamic Build */
                        if (
                            array_key_exists('dynamicBuild', $functions['isDynamic']) &&
                            !empty($functions['isDynamic']['dynamicBuild'])
                        ) {
                            $dyn = $functions['isDynamic']['dynamicBuild'];
                            $dyn['noKeyExistsCheck'] = true;

                            // --- CASE: associative object with fixed keys (schema closed) ---
                            if (
                                $functions['isDynamic']['typeOfArray'] === 'associative' &&
                                array_key_exists('fixedKeys', $dyn) &&
                                is_array($dyn['fixedKeys']) &&
                                !empty($dyn['fixedKeys'])
                            ) {
                                $allowedKeys = array_keys($dyn['fixedKeys']);
                                $actualKeys  = array_keys($itemPropertyValue);

                                // Check for unknown keys
                                $unknown = array_values(array_diff($actualKeys, $allowedKeys));
                                $this->assertTrue(
                                    empty($unknown),
                                    $this->getErrorMessage(
                                        'Unexpected keys found: [' . implode(', ', $unknown) . ']. '
                                        . 'Allowed keys are: [' . implode(', ', $allowedKeys) . ']'
                                    )
                                );

                                // Build rules for ALL fixed keys
                                foreach ($dyn['fixedKeys'] as $fk => $fkRules) {
                                    $fkRules['requireParent'] = $property;
                                    $newProp = $property . '.' . $fk;
                                    self::$geocodeDataStructure[$dataSetName]['properties'][$newProp] = $fkRules;
                                    $queue[] = $newProp;
                                }
                                continue;
                            } else {
                                // --- DEFAULT (list or associative without fixedKeys): build on actual keys ---
                                foreach ($itemPropertyValue as $k => $v) {
                                    self::$geocodeDataStructure[$dataSetName]['properties'][$property . '.' . $k] =
                                        $dyn;
                                    $queue[] = $property . '.' . $k;
                                }
                            }
                        }
                    }

                    /** geoSets check for geographical zone */
                    $getInternalCode = $this->arrayGetDot($item, 'internalCode');
                    if (
                        array_key_exists('checkGeographicalZones', $functions) &&
                        !empty($functions['checkGeographicalZones']) &&
                        is_string($getInternalCode)
                    ) {
                        $this->checkGeographicalZones(
                            $getInternalCode,
                            (array) $itemPropertyValue
                        );
                    }

                    /** countries check for valid SVG */
                    if (
                        array_key_exists('isValidSVG', $functions) &&
                        !empty($functions['isValidSVG']) &&
                        is_string($itemPropertyValue)
                    ) {
                        $this->assertTrue(
                            DataSets::isValidSVG($itemPropertyValue),
                            $this->getErrorMessage(
                                'The property ' . $name . ' is not a valid SVG.'
                            )
                        );
                    }

                    /** countries check for time zone (https://www.iana.org/time-zones) */
                    if (
                        array_key_exists('checkTimeZone', $functions) &&
                        !empty($functions['checkTimeZone'])
                    ) {
                        if (
                            !in_array(
                                $itemPropertyValue,
                                DateTimeZone::listIdentifiers(DateTimeZone::ALL_WITH_BC),
                                true
                            )
                        ) {
                            $this->addWarning(
                                $this->getErrorMessage(
                                    'The property ' . $name . ' `'
                                    . (is_string($itemPropertyValue) ? $itemPropertyValue : '')
                                    . '` is not a valid time zone. (TimeZone Version: ' . self::$timeZoneVersion . ')'
                                )
                            );
                        }
                    }

                    /** Check for a valid BCP 47 locale */
                    if (
                        array_key_exists('validateBCP47', $functions) &&
                        !empty($functions['validateBCP47']) &&
                        is_string($itemPropertyValue)
                    ) {
                        $this->assertTrue(
                            $this->validateLocale($itemPropertyValue),
                            $this->getErrorMessage(
                                'The property ' . $name . ' `' . $itemPropertyValue . '` is not a valid BCP47 locale.'
                            )
                        );
                    }

                    /** Check for a valid country code IDN  */
                    if (
                        array_key_exists('validateUnicodeIdn', $functions) &&
                        !empty($functions['validateUnicodeIdn'])
                    ) {
                        $this->assertTrue(
                            $this->validateUnicodeIdn($itemPartWhereToCheck),
                            $this->getErrorMessage(
                                'The property ' . $name . ' `'
                                . (is_string($itemPropertyValue) ? $itemPropertyValue : '')
                                . '` '
                                . 'is not a valid correspondence with punycode.'
                            )
                        );
                    }

                    /** Check the Hexadecimal Continuity */
                    if (
                        array_key_exists('checkHexadecimalContinuity', $functions) &&
                        $functions['checkHexadecimalContinuity'] &&
                        is_string($itemPropertyValue) &&
                        !empty($itemPropertyValue)
                    ) {
                        $intEntry = (int) hexdec($itemPropertyValue);
                        $currentBlock = intval(substr((string) strrchr($parentProperty, '.'), 1));
                        $pos = strrpos($parentProperty, '.');
                        $paragonProperty = $pos !== false
                            ? substr($parentProperty, 0, $pos) . '.'
                            : $parentProperty;
                        $previousBlock = null;
                        $paragonValue = -1;
                        if ($currentBlock > 0) {
                            $previousBlock = $currentBlock - 1;
                        }
                        if (
                            !array_key_exists(
                                $currentBlock,
                                self::$duplicatesControl['scripts']['rangesDynBuilt']['checkOrder']
                            )
                        ) {
                            self::$duplicatesControl['scripts']['rangesDynBuilt']['checkOrder'][$currentBlock] = [];
                        }
                        self::$duplicatesControl['scripts']['rangesDynBuilt']['checkOrder']
                            [$currentBlock][$propertyToCheck] = $intEntry;
                        switch ($propertyToCheck) {
                            case 0:
                                if ($previousBlock !== null) {
                                    $xBlock =
                                        (array_key_exists(
                                            1,
                                            self::$duplicatesControl['scripts']['rangesDynBuilt']
                                                ['checkOrder'][$previousBlock]
                                        )) ? 1 : 0;
                                    $paragonValue =
                                        self::$duplicatesControl['scripts']['rangesDynBuilt']
                                            ['checkOrder'][$previousBlock][$xBlock];
                                    $paragonProperty .= $previousBlock . '.' . $xBlock;
                                }
                                self::$duplicatesControl['scripts']['rangesDynBuilt']['countPoints']++;
                                break;
                            case 1:
                                $paragonValue =
                                    self::$duplicatesControl['scripts']['rangesDynBuilt']['checkOrder']
                                        [$currentBlock][0];
                                $paragonProperty .= $currentBlock . '.0';

                                self::$duplicatesControl['scripts']['rangesDynBuilt']['countPoints'] +=
                                    (
                                        $intEntry -
                                        $paragonValue
                                    );
                                break;
                            default:
                                $paragonProperty = '';
                        }
                        $this->assertGreaterThan(
                            $paragonValue,
                            $intEntry,
                            $this->getErrorMessage(
                                'The property ' . $name . ' must be greater than the previous one. '
                                . 'Paragon property: `'
                                . $paragonProperty
                                . '`'
                            )
                        );
                    }
                }
            }
            /** Test to execute after all the properties checks are already passed */
            if (
                array_key_exists('afterRecordTest', self::$geocodeDataStructure[$dataSetName]) &&
                !empty(self::$geocodeDataStructure[$dataSetName]['afterRecordTest'])
            ) {

                /** Check the integrity for the count of the ranges points (for scripts) */
                if (
                    array_key_exists(
                        'checkIntegrityOfRangesPoints',
                        self::$geocodeDataStructure[$dataSetName]['afterRecordTest']
                    ) &&
                    self::$geocodeDataStructure[$dataSetName]['afterRecordTest']['checkIntegrityOfRangesPoints']
                ) {
                    self::$currentStructure['propertyName'] = 'unicode.totalCodePoints';
                    $this->assertEquals(
                        self::$duplicatesControl['scripts']['rangesDynBuilt']['countPoints'],
                        $this->arrayGetDot($item, self::$currentStructure['propertyName']),
                        $this->getErrorMessage(
                            'The property value has no integrity with the related ranges points.'
                        )
                    );
                }
            }
            self::$currentStructure['itemPosition']++;
        }
    }

    /**
     * @param string $dataSetName
     * @param string $dataSetPath
     * @param array<string, mixed> $structure
     * @return void
     */
    private function parseDataSet(string $dataSetName, string $dataSetPath, array $structure): void
    {
        $this->assertFileExists($dataSetPath, 'The `' . $dataSetPath . '` file is missing');
        $dataSet = require_once $dataSetPath;
        $this->assertIsArray($dataSet, 'The dataset `' . $dataSetName . '` is not an array');
        $this->assertNotEmpty($dataSet, 'The dataset `' . $dataSetName . '` is empty');
        foreach ($structure['indexes'] as $indexName => $indexParam) {
            if (
                !array_key_exists('doNotSetOnInit', $indexParam) ||
                empty($indexParam['doNotSetOnInit'])
            ) {
                self::$geocodeDataStructure[$dataSetName]['indexes'][$indexName]['values'] =
                    $indexParam['key'] === 'translationIndex'
                        ? array_keys($dataSet)
                        : (!empty($indexParam['useKeys'])
                        ? array_keys($dataSet[$indexParam['key']] ?? [])
                        : array_values(
                            array_filter(
                                array_column($dataSet, $indexParam['key']),
                                static fn ($v) => $v !== null
                            )
                        )
                    );
            }
        }
        self::$geocodeDataSet[$dataSetName] = $dataSet;
    }

    /**
     * @param array<string, string|array<string>> $ccIdnBlock
     * @return bool
     */
    private function validateUnicodeIdn(array $ccIdnBlock): bool
    {
        if (
            !isset($ccIdnBlock['unicode']) ||
            !isset($ccIdnBlock['punycode']) ||
            !is_string($ccIdnBlock['unicode']) ||
            !is_string($ccIdnBlock['punycode'])
        ) {
            return false;
        }

        $unicodeDomain = substr($ccIdnBlock['unicode'], 1);
        $punycodeDomain = substr($ccIdnBlock['punycode'], 1);

        // Convert unicode → punycode
        $convertedAscii = idn_to_ascii(
            $unicodeDomain,
            IDNA_DEFAULT,
            INTL_IDNA_VARIANT_UTS46
        );

        if ($convertedAscii === false || $convertedAscii !== $punycodeDomain) {
            return false;
        }

        // Convert punycode → unicode
        $convertedUnicode = idn_to_utf8(
            $punycodeDomain,
            IDNA_DEFAULT,
            INTL_IDNA_VARIANT_UTS46
        );

        if ($convertedUnicode === false || $convertedUnicode !== $unicodeDomain) {
            return false;
        }

        return true;
    }

    /**
     * @param string $locale
     * @return bool
     */
    private function validateLocale(string $locale): bool
    {
        // 1) Regex validation first: language[-Script][-Region]
        // - language: 2-3 lowercase letters
        // - script:   4 letters TitleCase
        // - region:   ISO3166 alpha2 (2 uppercase) OR UN M49 (3 digits)
        if (!preg_match('/^[a-z]{2,3}(?:-[A-Z][a-z]{3})?(?:-(?:[A-Z]{2}|\d{3}))?$/', $locale)) {
            return false;
        }

        $loc = explode('-', $locale);
        $count = count($loc);

        $language = $loc[0];

        $script = null;
        $region = null;

        if ($count === 2) {
            if (strlen($loc[1]) === 4) {
                $script = $loc[1];
            } else {
                $region = $loc[1];
            }
        } elseif ($count === 3) {
            $script = $loc[1];
            $region = $loc[2];
        }

        // --- LANGUAGE ---
        $lang = strlen($language);
        if ($lang === 3) {
            //(ISO 639-3)
            $path = 'languages.indexes.main.values';
        } elseif ($lang === 2) {
            //(ISO 639-1)
            $path = 'languages.indexes.secondary.values';
        } else {
            return false;
        }
        if (!in_array($language, (array) $this->arrayGetDot(self::$geocodeDataStructure, $path))) {
            return false;
        }

        // --- SCRIPT ([TODO]) ---
        if ($script !== null) {
//            if (!in_array($script, $this->arrayGetDot(self::$geocodeDataStructure, 'scripts.main.values'))) {
//                return false;
//            }
        }

        // --- REGION ---
        if ($region !== null) {
            if (ctype_digit($region) && strlen($region) === 3) {
                if (
                    !in_array(
                        $region,
                        (array)
                          $this->arrayGetDot(self::$geocodeDataStructure, 'countries.indexes.secondary.values')
                    )
                ) {
                    if (
                        !in_array(
                            $region,
                            (array)
                              $this->arrayGetDot(self::$geocodeDataStructure, 'geoSets.indexes.secondary.values')
                        )
                    ) {
                        return false;
                    }
                }
            } elseif (ctype_alpha($region) && strlen($region) === 2) {
                if (
                    !in_array(
                        $region,
                        (array) $this->arrayGetDot(self::$geocodeDataStructure, 'countries.indexes.main.values')
                    )
                ) {
                    return false;
                }
            } else {
                return false;
            }
        }
        return true;
    }

    /**
     * @param string $geoZone
     * @param array<string> $countryCodes
     * @return void
     */
    private function checkGeographicalZones(string $geoZone, array $countryCodes): void
    {
        if (!preg_match('/^GEOG-/', $geoZone)) {
            return;
        }

        if (substr_count($geoZone, '-') == 1) {
            self::$geocodeDataStructure['geoSets']['indexes']['checkCountries']['values'] =
                array_merge(
                    self::$geocodeDataStructure['geoSets']['indexes']['checkCountries']['values'],
                    $countryCodes
                );
        }

        foreach (self::$geocodeDataSet['geoSets'] as $geoSet) {
            if (
                !preg_match('/^GEOG-/', $geoSet['internalCode']) ||
                $geoSet['internalCode'] == $geoZone ||
                preg_match('/^' . preg_quote($geoZone, '/') . '/', $geoSet['internalCode'])
            ) {
                continue;
            }

            if (preg_match('/^' . preg_quote($geoSet['internalCode'], '/') . '/', $geoZone)) {
                $diff = array_diff($countryCodes, $geoSet['countryCodes']);
                $this->assertTrue(
                    empty($diff),
                    $this->getErrorMessage(
                        'The property '
                        . 'has the following elements not contained in the ascendant zone '
                        . '`' . $geoSet['internalCode'] . '`:' . "\n"
                        . '[' . implode(', ', $diff) . ']'
                    )
                );
            } else {
                $intersect = array_intersect($countryCodes, $geoSet['countryCodes']);
                $this->assertTrue(
                    empty($intersect),
                    $this->getErrorMessage(
                        'The property '
                        . 'has the following elements contained in the not directly related zone '
                        . '`' . $geoSet['internalCode'] . '`:' . "\n"
                        . '[' . implode(', ', $intersect) . ']'
                    )
                );
            }
        }
    }

    /**
     * @param array<string, mixed> $array
     * @param string $path
     * @param string $value
     * @return void
     */
    private function addItemToList(array &$array, string $path, string $value): void
    {
        if ($path === '') {
            $array[] = $value;
            return;
        }

        $keys = explode('.', $path);

        foreach ($keys as $key) {
            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[] = $value;
    }

    /**
     * @param array<string, mixed> $array
     * @param string $path
     * @return string|integer|array<string, mixed>|bool|null
     */
    private function arrayGetDot(array $array, string $path)
    {
        if ($path === '') {
            return $array;
        }

        foreach (explode('.', $path) as $key) {
            if (!is_array($array) || !array_key_exists($key, $array)) {
                return null;
            }

            $array = $array[$key];
        }

        return $array;
    }

    /**
     * @param string $message
     * @return string
     */
    private function getErrorMessage(string $message)
    {
        $errMex = 'Index: `' . self::$currentStructure['itemPosition']  . '`' . "\n";
        if (self::$currentStructure['jsonItem']) {
            $errMex .=  'Item Data: ' . self::$currentStructure['jsonItem'] ;
        } else {
            $errMex .= 'Item with `' . self::$currentStructure['mainKey']
                . '` = `' . self::$currentStructure['mainKeyValue']  . '`';
        }
        return 'Dataset: `' . self::$currentStructure['dataSetName']  .  '`' . "\n"
            . $errMex . "\n"
            . 'Property: `' . self::$currentStructure['propertyName']  . '`' . "\n"
            . 'Error: ' . $message .  "\n";
    }
}
