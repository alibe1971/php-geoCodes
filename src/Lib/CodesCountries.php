<?php

namespace Alibe\GeoCodes\Lib;

use Alibe\GeoCodes\Lib\DataObj\Countries;
use Alibe\GeoCodes\Lib\DataObj\Elements\Country;
use Alibe\GeoCodes\Lib\Enums\DataSets\Access;
use Alibe\GeoCodes\Lib\Enums\DataSets\Index;
use Alibe\GeoCodes\Lib\Enums\DataSets\Source;
use Alibe\GeoCodes\Lib\Enums\DataSets\Type;

class CodesCountries extends Enquiries
{
    /**
     * @var string
     */
    protected string $dataSetName = 'countries';

    /**
     * @var string
     */
    protected string $instanceName = Countries::class;

    /**
     * @var string
     */
    protected string $singleItemInstanceName = Country::class;

    /**
     * @var array<string, array<string, bool|string|null>>
     */
    protected array $dataSetsStructure = [
        'alpha2' => [
            'source' => Source::DATA,
            'type' => Type::STRING,
            'nullable' => false,
            'index' => Index::PRIMARY,
            'access' => Access::PUBLIC,
            'search' => true,
            'description' => 'The ISO-3166-1 alpha-2 code (2 letters)'
        ],
        'alpha3' => [
            'source' => Source::DATA,
            'type' => Type::STRING,
            'nullable' => false,
            'index' => Index::INDEXABLE,
            'access' => Access::PUBLIC,
            'search' => true,
            'description' => 'The ISO-3166-1 alpha-3 code (3 letters)'
        ],
        'unM49' => [
            'source' => Source::DATA,
            'type' => Type::STRING,
            'nullable' => false,
            'index' => Index::INDEXABLE,
            'access' => Access::PUBLIC,
            'search' => true,
            'description' => 'The United Nations Statistics Division M49 code (numeric)'
        ],
        'name' => [
            'source' => Source::TRANSLATIONS,
            'type' => Type::STRING,
            'nullable' => false,
            'index' => Index::INDEXABLE,
            'access' => Access::PUBLIC,
            'search' => true,
            'description' => 'The common name of the country'
        ],
        'fullName' => [
            'source' => Source::TRANSLATIONS,
            'type' => Type::STRING,
            'nullable' => false,
            'index' => Index::INDEXABLE,
            'access' => Access::PUBLIC,
            'search' => true,
            'description' => 'The complete name of the country'
        ],
        'officialName' => [
            'source' => Source::DATA,
            'type' => Type::OBJECT,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => true,
            'description' => 'The country\'s official name(s) in its administrative language(s).'
        ],
        'flags' => [
            'source' => Source::DATA,
            'type' => Type::OBJECT,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'The flags in different format for the country'
        ],
        'flags.emoji' => [
            'source' => Source::DATA,
            'type' => Type::STRING,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'The flag in emoji (Regional_Indicator) format'
        ],
        'flags.svg' => [
            'source' => Source::DATA,
            'type' => Type::STRING,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'The flag in svg format'
        ],
        'dependency' => [
            'source' => Source::DATA,
            'type' => Type::STRING,
            'nullable' => true,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'Territorial dependence (if any)'
        ],
        'mottos' => [
            'source' => Source::DATA,
            'type' => Type::OBJECT,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'The mottos of the country'
        ],
        'mottos.official' => [
            'source' => Source::DATA,
            'type' => Type::OBJECT,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'The official mottos of the country'
        ],
        'mottos.popular' => [
            'source' => Source::DATA,
            'type' => Type::OBJECT,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'The popular mottos of the country'
        ],
        'mottos.founding' => [
            'source' => Source::DATA,
            'type' => Type::OBJECT,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'The founding mottos of the country'
        ],
        'mottos.military' => [
            'source' => Source::DATA,
            'type' => Type::OBJECT,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'The military mottos of the country'
        ],
        'mottos.historical' => [
            'source' => Source::DATA,
            'type' => Type::OBJECT,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'The historical mottos of the country'
        ],
        'mottos.royal' => [
            'source' => Source::DATA,
            'type' => Type::OBJECT,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'The royal mottos of the country'
        ],
        'mottos.presidential' => [
            'source' => Source::DATA,
            'type' => Type::OBJECT,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'The presidential mottos of the country'
        ],
        'currencies' => [
            'source' => Source::DATA,
            'type' => Type::OBJECT,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'The currencies used in the country'
        ],
        'currencies.legalTenders' => [
            'source' => Source::DATA,
            'type' => Type::OBJECT,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'The legalTenders currencies used in the country'
        ],
        'currencies.widelyAccepted' => [
            'source' => Source::DATA,
            'type' => Type::OBJECT,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'The widelyAccepted currencies used in the country'
        ],
        'dialCodes' => [
            'source' => Source::DATA,
            'type' => Type::OBJECT,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'The dial codes for phone call to the country'
        ],
        'dialCodes.deJure' => [
            'source' => Source::DATA,
            'type' => Type::OBJECT,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'The de jure dial codes for phone call to the country'
        ],
        'dialCodes.deFacto' => [
            'source' => Source::DATA,
            'type' => Type::OBJECT,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'The de facto dial codes for phone call to the country'
        ],
        'dialCodes.exceptions' => [
            'source' => Source::DATA,
            'type' => Type::OBJECT,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'The exceptions of dial codes for phone call to the country'
        ],
        'ccTld' => [
            'source' => Source::DATA,
            'type' => Type::STRING,
            'nullable' => true,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => true,
            'description' => 'The top level domain country code (if it exists)'
        ],
        'ccIdn' => [
            'source' => Source::DATA,
            'type' => Type::OBJECT,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'The top level domain country code IDN (if it exists)'
        ],
        'timeZones' => [
            'source' => Source::DATA,
            'type' => Type::OBJECT,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'The time zones present in the country'
        ],
        'languages' => [
            'source' => Source::DATA,
            'type' => Type::OBJECT,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'The languages used in the country'
        ],
        'languages.official' => [
            'source' => Source::DATA,
            'type' => Type::OBJECT,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'The Official languages used in the country'
        ],
        'languages.official.deJure' => [
            'source' => Source::DATA,
            'type' => Type::OBJECT,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'The Official de jure languages used in the country'
        ],
        'languages.official.deFacto' => [
            'source' => Source::DATA,
            'type' => Type::OBJECT,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'The Official de facto languages used in the country'
        ],
        'localesIcu' => [
            'source' => Source::DATA,
            'type' => Type::OBJECT,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'The PHP locales ICU standard used in the country'
        ],
        'demonyms' => [
            'source' => Source::TRANSLATIONS,
            'type' => Type::OBJECT,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'The demonysms; the names of the inhabitants'
        ],
        'otherAppsIds' => [
            'source' => Source::DATA,
            'type' => Type::OBJECT,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'Identification code to use in other applications'
        ],
        'otherAppsIds.geoNamesOrg' => [
            'source' => Source::DATA,
            'type' => Type::INTEGER,
            'nullable' => true,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'Ids for geonames.org'
        ],
        'otherAppsIds.wikiData' => [
            'source' => Source::DATA,
            'type' => Type::STRING,
            'nullable' => true,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'Ids for wiki data'
        ],
        'otherAppsIds.openStreetMap' => [
            'source' => Source::DATA,
            'type' => Type::OBJECT,
            'nullable' => true,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'Ids for Open Street Map'
        ],
        'otherAppsIds.openStreetMap.type' => [
            'source' => Source::DATA,
            'type' => Type::STRING,
            'nullable' => true,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'Type for Open Street Map'
        ],
        'otherAppsIds.openStreetMap.id' => [
            'source' => Source::DATA,
            'type' => Type::STRING,
            'nullable' => true,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'Id for Open Street Map'
        ],
        'keywords' => [
            'source' => Source::TRANSLATIONS,
            'type' => Type::ARRAY,
            'nullable' => true,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PRIVATE,
            'search' => true,
            'description' => null
        ]
    ];
}
