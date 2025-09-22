<?php

namespace Alibe\GeoCodes\Lib;

use Alibe\GeoCodes\Lib\DataObj\Languages;
use Alibe\GeoCodes\Lib\DataObj\Elements\Language;
use Alibe\GeoCodes\Lib\Enums\DataSets\Access;
use Alibe\GeoCodes\Lib\Enums\DataSets\Index;
use Alibe\GeoCodes\Lib\Enums\DataSets\Source;
use Alibe\GeoCodes\Lib\Enums\DataSets\Type;

class CodesLanguages extends Enquiries
{
    /**
     * @var string
     */
    protected string $dataSetName = 'languages';

    /**
     * @var string
     */
    protected string $instanceName = Languages::class;

    /**
     * @var string
     */
    protected string $singleItemInstanceName = Language::class;

    protected array $dataSetsStructure = [
        'isoCode' => [
            'source' => Source::DATA,
            'type' => Type::STRING,
            'nullable' => false,
            'index' => Index::PRIMARY,
            'access' => Access::PUBLIC,
            'search' => true,
            'description' => 'The ISO-639 three letters code identifier'
        ],
        'name' => [
            'source' => Source::TRANSLATIONS,
            'type' => Type::STRING,
            'nullable' => false,
            'index' => Index::INDEXABLE,
            'access' => Access::PUBLIC,
            'search' => true,
            'description' => 'The common name of the language'
        ],
        'part2b' => [
            'source' => Source::DATA,
            'type' => Type::STRING,
            'nullable' => true,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => true,
            'description' => 'Equivalent 639-2 identifier of the bibliographic applications code set, if there is one'
        ],
        'part2t' => [
            'source' => Source::DATA,
            'type' => Type::STRING,
            'nullable' => true,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => true,
            'description' => 'Equivalent 639-2 identifier of the terminology applications code set, if there is one'
        ],
        'part1' => [
            'source' => Source::DATA,
            'type' => Type::STRING,
            'nullable' => true,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => true,
            'description' => 'Equivalent 639-1 identifier, if there is one'
        ],
        'glottoCode' => [
            'source' => Source::DATA,
            'type' => Type::STRING,
            'nullable' => true,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => true,
            'description' => 'The GlottoLog code'
        ],
        'scope' => [
            'source' => Source::DATA,
            'type' => Type::STRING,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => true,
            'description' => 'The scope of the language: I(ndividual), M(acrolanguage), S(pecial) '
        ],
        'type' => [
            'source' => Source::DATA,
            'type' => Type::STRING,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => true,
            'description' =>
                'The type of the language: A(ncient), C(onstructed), E(xtinct), H(istorical), L(iving), S(pecial)'
        ],
        'macroLanguageRef' => [
            'source' => Source::DATA,
            'type' => Type::STRING,
            'nullable' => true,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => true,
            'description' => 'The reference of the Macrolanguage, if it exists'
        ],
        'scripts' => [
            'source' => Source::DATA,
            'type' => Type::OBJECT,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => true,
            'description' => 'The available scripts for the language.'
        ],
    ];
}
