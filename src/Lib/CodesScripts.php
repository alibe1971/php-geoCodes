<?php

namespace Alibe\GeoCodes\Lib;

use Alibe\GeoCodes\Lib\DataObj\Elements\Script;
use Alibe\GeoCodes\Lib\DataObj\Scripts;
use Alibe\GeoCodes\Lib\Enums\DataSets\Access;
use Alibe\GeoCodes\Lib\Enums\DataSets\Index;
use Alibe\GeoCodes\Lib\Enums\DataSets\Source;
use Alibe\GeoCodes\Lib\Enums\DataSets\Type;

class CodesScripts extends Enquiries
{
    /**
     * @var string
     */
    protected string $dataSetName = 'scripts';

    /**
     * @var string
     */
    protected string $instanceName = Scripts::class;

    /**
     * @var string
     */
    protected string $singleItemInstanceName = Script::class;


    protected array $dataSetsStructure = [

        'code' => [
            'source' => Source::DATA,
            'type' => Type::STRING,
            'nullable' => false,
            'index' => Index::PRIMARY,
            'access' => Access::PUBLIC,
            'search' => true,
            'description' => 'The alphabetic iso code of the script set',
        ],
        'numeric' => [
            'source' => Source::DATA,
            'type' => Type::STRING,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => true,
            'description' => 'The numeric iso code of the script set'
        ],
        'name' => [
            'source' => Source::TRANSLATIONS,
            'type' => Type::STRING,
            'nullable' => false,
            'index' => Index::INDEXABLE,
            'access' => Access::PUBLIC,
            'search' => true,
            'description' => 'The name of the script set'
        ],
        'writingDirection' => [
            'source' => Source::DATA,
            'type' => Type::OBJECT,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'The direction of the writing of the script set'
        ],
        'writingDirection.code' => [
            'source' => Source::DATA,
            'type' => Type::STRING,
            'nullable' => false,
            'isCategory' => true,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'The code of direction of the writing of the script set'
        ],
        'writingDirection.description' => [
            'source' => Source::TRANSLATIONSCATEGORIES,
            'type' => Type::STRING,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'The description of direction of the writing of the script set'
        ],
        'unicode' => [
            'source' => Source::DATA,
            'type' => Type::OBJECT,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'The unicode group of the script set'
        ],
        'unicode.version' => [
            'source' => Source::DATA,
            'type' => Type::STRING,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => true,
            'description' => 'The version of the unicode group of the script set'
        ],
        'unicode.ranges' => [
            'source' => Source::DATA,
            'type' => Type::OBJECT,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'The ranges of the unicode group of the script set'
        ],
        'unicode.totalCodePoints' => [
            'source' => Source::DATA,
            'type' => Type::INTEGER,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => true,
            'description' => 'The total of the points of the ranges in the unicode group of the script set'
        ]
    ];
}
