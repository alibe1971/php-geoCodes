<?php

namespace Alibe\GeoCodes\Tests;

use Alibe\GeoCodes\Lib\DataObj\Elements\Script;
use Alibe\GeoCodes\Lib\DataObj\Scripts;
use Alibe\GeoCodes\Lib\Exceptions\GeneralException;
use Alibe\GeoCodes\Lib\Exceptions\QueryException;
use DOMDocument;
use PHPUnit\Framework\TestCase;
use Alibe\GeoCodes\GeoCodes;
use SimpleXMLElement;
use Symfony\Component\Yaml\Yaml;

/**
 * @testdox Scripts
 */
final class IsoScriptsTest extends TestCase
{
    /**
     * @var int
     */
    private static int $scriptsTotalCount = 226;

    /**
     * @var array<int|array<string>> $constants
     */
    private static array $constants = [
        'indexes' => [
            'code',
            'numeric',
            'name'
        ],
        'selectables' => [
            'code',
            'numeric',
            'name',
            'writingDirection',
            'writingDirection.code',
            'writingDirection.description',
            'unicode',
            'unicode.version',
            'unicode.ranges',
            'unicode.totalCodePoints'
        ]
    ];

    /**
     * @var array<int, string> $expectedLimitTest
     */
    private static array $expectedLimitTest = [
        'Cakm',
        'Cans'
    ];

    /**
     * @var array<string, array<string, string>> $expectedOrderByTest
     */
    private static array $expectedOrderByTest = [
        'code' => [
            'ASC' => 'Adlm',
            'DESC' => 'Zzzz',
        ],
        'numeric' => [
            'ASC' => '015',
            'DESC' => '999',
        ],
        'name' => [
            'ASC' => '(Small) Seal',
            'DESC' => 'Zanabazar Square (Zanabazarin Dörböljin Useg, Xewtee Dörböljin Bicig, Horizontal Square Script)',
        ]
    ];

    /**
     * @var GeoCodes $geoCodes
     */
    private static GeoCodes $geoCodes;

    /**
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        self::$geoCodes = new GeoCodes();
    }

    /**
     * @var Scripts
     */
    private static Scripts $scriptsList;


    /**
     * @var Script
     */
    private static Script $script;

    /**
     * @var string
     */
    private static string $xsdList;

    /**
     * @var string
     */
    private static string $xsdSingle;

    /**
     * @test
     * @testdox Test `->get()` the list of scripts is object as instance of Scripts.
     * @return void
     * @throws QueryException
     */
    public function testToGetListOfScripts(): void
    {
        /** @phpstan-ignore-next-line   The unique object type is needed for php 7.4 */
        self::$scriptsList = self::$geoCodes->scripts()->get();
        $this->assertIsObject(self::$scriptsList);
        $this->assertInstanceOf(Scripts::class, self::$scriptsList);
    }

    /**
     * @test
     * @testdox Test the elements of the list of scripts are an instance of Script.
     * @depends testToGetListOfScripts
     * @return void
     */
    public function testToGetElementListOfScripts(): void
    {
        $this->assertIsObject(self::$scriptsList->{0});
        $this->assertInstanceOf(Script::class, self::$scriptsList->{0});
    }

    /**
     * @test
     * @testdox Test the `->get()->toJson()` feature.
     * @depends testToGetListOfScripts
     * @return void
     * @throws QueryException
     */
    public function testGetToJsonFeature(): void
    {
        $scripts = self::$geoCodes->scripts();
        foreach (
            [
            // Whole list
            self::$scriptsList,
            // Whole list with index
            $scripts->withIndex('name')->get(),
            // Single element in list
            $scripts->take(1)->get(),
            // Empty
            $scripts->take(0)->get(),
            ] as $testCase
        ) {
            $json = $testCase->toJson();
            $expectedData = $testCase->toArray();
            $this->assertIsString($json);
            $decodedJson = json_decode($json, true);
            $this->assertNotNull($decodedJson, 'Not a valid JSON');
            $this->assertIsArray($decodedJson, 'Not a valid JSON');
            $this->assertEquals($expectedData, $decodedJson, 'Converted JSON does not match expected data');
        }
    }

    /**
     * @test
     * @testdox  ==>  Test the JSON serializing.
     * @depends testToGetListOfScripts
     * @return void
     * @throws QueryException
     */
    public function testJsonSerializing(): void
    {
        $scripts = self::$geoCodes->scripts();

        $withoutIndex = $scripts->get()->toJson();
        $decodedWithoutIndex = json_decode($withoutIndex, true);
        $this->assertTrue(Utils::isList($decodedWithoutIndex));

        $withIndex = $scripts->withIndex('name')->get()->toJson();
        $decodedWithIndex = json_decode($withIndex, true);
        $this->assertFalse(Utils::isList($decodedWithIndex));
        $this->assertArrayHasKey('Latin', $decodedWithIndex);
    }

    /**
     * @test
     * @testdox Test the `->get()->toYaml()` feature.
     * @depends testToGetListOfScripts
     * @return void
     * @throws QueryException
     */
    public function testGetToYamlFeature(): void
    {
        $scripts = self::$geoCodes->scripts();
        foreach (
            [
            // Whole list
            self::$scriptsList,
            // Whole list with index
            $scripts->withIndex('name')->get(),
            // Single element in list
            $scripts->take(1)->get(),
            // Empty
            $scripts->take(0)->get(),
            ] as $testCase
        ) {
            $yaml = $testCase->toYaml();
            $expectedData = $testCase->toArray();
            $this->assertIsString($yaml);
            $decodedYaml = Yaml::parse($yaml);
            $this->assertNotNull($decodedYaml, 'Not a valid YAML');
            $this->assertIsArray($decodedYaml, 'Not a valid YAML');
            $this->assertEquals($expectedData, $decodedYaml, 'Converted YAML does not match expected data');
        }
    }

    /**
     * @test
     * @testdox Test the `->getXsd()` and the `->getXsdSingle()` features.
     * @depends testToGetListOfScripts
     * @return void
     * @throws GeneralException|QueryException
     */
    public function testGetXsdFeatures(): void
    {
        self::$xsdList = self::$geoCodes->scripts()->getXsd();
        $this->assertIsString(self::$xsdList);

        self::$xsdSingle = self::$geoCodes->scripts()->getXsdSingle();
        $this->assertIsString(self::$xsdSingle);
    }

    /**
     * @test
     * @testdox Test the `->get()->toXml()` feature and validate it with external xsd.
     * @depends testToGetListOfScripts
     * @depends testGetXsdFeatures
     * @return void
     * @throws QueryException|GeneralException
     */
    public function testGetToXmlFeatureWithExternalValidation(): void
    {
        $scripts = self::$geoCodes->scripts();
        foreach (
            [
            // Whole list
            self::$scriptsList,
            // Whole list with index
            $scripts->withIndex('name')->get(),
            // Single element in list
            $scripts->take(1)->get(),
            // Empty
            $scripts->take(0)->get(),
            ] as $testCase
        ) {
            $xml = $testCase->toXml();
            $this->assertIsString($xml);
            $decodedXml = simplexml_load_string($xml);
            $this->assertInstanceOf(SimpleXMLElement::class, $decodedXml, 'Not a valid XML');
            $dom = new DOMDocument();
            $dom->loadXML($xml);
            $this->assertTrue($dom->schemaValidateSource(self::$xsdList), 'Not a valid XML');
        }
    }


    /**
     * @test
     * @testdox Test the `->get()->toXmlAndValidate()` feature.
     * @depends testToGetListOfScripts
     * @return void
     * @throws QueryException|GeneralException
     */
    public function testGetToXmlAndValidateFeature(): void
    {
        $scripts = self::$geoCodes->scripts();
        foreach (
            [
            // Whole list
            self::$scriptsList,
            // Whole list with index
            $scripts->withIndex('name')->get(),
            // Single element in list
            $scripts->take(1)->get(),
            // Empty
            $scripts->take(0)->get(),
            ] as $testCase
        ) {
            $xml = $testCase->toXmlAndValidate();
            $this->assertIsString($xml);
            $decodedXml = simplexml_load_string($xml);
            $this->assertInstanceOf(SimpleXMLElement::class, $decodedXml, 'Not a valid XML');
        }
    }

    /**
     * @test
     * @testdox Test the `->get()->toArray()` feature.
     * @depends testToGetListOfScripts
     * @return void
     */
    public function testGetToArrayFeature(): void
    {
        $array = self::$scriptsList->toArray();
        $this->assertIsArray($array, 'Not a valid Array');

        $array = self::$scriptsList->{0}->toArray();
        $this->assertIsArray($array, 'Not a valid Array');
    }

    /**
     * @test
     * @testdox Test the `->get()->toFlatten()` feature (default separator `.`).
     * @depends testToGetListOfScripts
     * @return void
     */
    public function testGetToFlattenFeature(): void
    {
        $flatten = self::$scriptsList->toFlatten();
        $this->assertIsArray($flatten, 'Not a valid Array');
        foreach (
            [
            mt_rand(0, (self::$scriptsTotalCount - 1)),
            mt_rand(0, (self::$scriptsTotalCount - 1)),
            mt_rand(0, (self::$scriptsTotalCount - 1)),
            mt_rand(0, (self::$scriptsTotalCount - 1)),
            mt_rand(0, (self::$scriptsTotalCount - 1))
            ] as $key
        ) {
            $this->assertEquals(self::$scriptsList->$key->code, $flatten[$key . '.code']);
            $this->assertEquals(self::$scriptsList->$key->numeric, $flatten[$key . '.numeric']);
            $this->assertEquals(self::$scriptsList->$key->name, $flatten[$key . '.name']);
        };
    }

    /**
     * @test
     * @testdox Test the `->get()->toFlatten('_')` feature, using custom separator.
     * @depends testToGetListOfScripts
     * @return void
     */
    public function testGetToFlattenFeatureCustomSeparator(): void
    {
        $flatten = self::$scriptsList->toFlatten('_');
        $this->assertIsArray($flatten, 'Not a valid Array');
        foreach (
            [
                     mt_rand(0, (self::$scriptsTotalCount - 1)),
                     mt_rand(0, (self::$scriptsTotalCount - 1)),
                     mt_rand(0, (self::$scriptsTotalCount - 1)),
                     mt_rand(0, (self::$scriptsTotalCount - 1)),
                     mt_rand(0, (self::$scriptsTotalCount - 1))
                 ] as $key
        ) {
            $this->assertEquals(self::$scriptsList->$key->code, $flatten[$key . '_code']);
            $this->assertEquals(self::$scriptsList->$key->numeric, $flatten[$key . '_numeric']);
            $this->assertEquals(self::$scriptsList->$key->name, $flatten[$key . '_name']);
        };
    }

    /**
     * @test
     * @testdox Test the `->first()` feature as instance of Script.
     * @return void
     * @throws QueryException
     */
    public function testFirstFeature(): void
    {
        /** @phpstan-ignore-next-line   The unique object type is needed for php 7.4 */
        self::$script = self::$geoCodes->scripts()->first();

        $this->assertIsObject(self::$script);
        $this->assertInstanceOf(Script::class, self::$script);
    }

    /**
     * @test
     * @testdox Test the `->first()` feature when result is empty as instance of Script.
     * @return void
     * @throws QueryException
     */
    public function testFirstFeatureOnEmpty(): void
    {
        $scripts = self::$geoCodes->scripts();
        $scripts->offset(0)->limit(0);
        $script = $scripts->first();

        $this->assertIsObject($script);
        $this->assertInstanceOf(Script::class, $script);
    }

    /**
     * @test
     * @testdox Test the `->first()->toJson()` feature.
     * @depends testFirstFeature
     * @return void
     * @throws QueryException
     */
    public function testFirstToJsonFeature(): void
    {
        foreach (
            [
                // Single Existent Script
                self::$script,
                // Empty
                self::$geoCodes->scripts()->take(0)->first(),
            ] as $testCase
        ) {
            $json = $testCase->toJson();
            $expectedData = $testCase->toArray();
            $this->assertIsString($json);
            $decodedJson = json_decode($json, true);
            $this->assertNotNull($decodedJson, 'Not a valid JSON');
            $this->assertIsArray($decodedJson, 'Not a valid JSON');
            $this->assertEquals($expectedData, $decodedJson, 'Converted JSON does not match expected data');
        }
    }

    /**
     * @test
     * @testdox Test the `->first()->toYaml()` feature.
     * @depends testFirstFeature
     * @return void
     * @throws QueryException
     */
    public function testFirstToYamlFeature(): void
    {
        foreach (
            [
                // Single Existent Script
                self::$script,
                // Empty
                self::$geoCodes->scripts()->take(0)->first(),
            ] as $testCase
        ) {
            $yaml = $testCase->toYaml();
            $expectedData = $testCase->toArray();
            $this->assertIsString($yaml);
            $decodedYaml = Yaml::parse($yaml);
            $this->assertNotNull($decodedYaml, 'Not a valid YAML');
            $this->assertIsArray($decodedYaml, 'Not a valid YAML');
            $this->assertEquals($expectedData, $decodedYaml, 'Converted YAML does not match expected data');
        }
    }

    /**
     * @test
     * @testdox Test the `->first()->toXml()` feature and validate it with external xsd.
     * @depends testFirstFeature
     * @depends testGetXsdFeatures
     * @return void
     * @throws QueryException|GeneralException
     */
    public function testFirstToXmlFeatureWithExternalValidation(): void
    {
        foreach (
            [
                // Single Existent Script
                self::$script,
                // Empty
                self::$geoCodes->scripts()->take(0)->first(),
            ] as $testCase
        ) {
            $xml = $testCase->toXml();
            $this->assertIsString($xml);
            $decodedXml = simplexml_load_string($xml);
            $this->assertInstanceOf(SimpleXMLElement::class, $decodedXml, 'Not a valid XML');
            $dom = new DOMDocument();
            $dom->loadXML($xml);
            $this->assertTrue($dom->schemaValidateSource(self::$xsdSingle), 'Not a valid XML');
        }
    }

    /**
     * @test
     * @testdox Test the `->first()->toXmlAndValidate()` feature.
     * @depends testFirstFeature
     * @return void
     * @throws QueryException
     */
    public function testFirstToXmlAndValidateFeature(): void
    {
        foreach (
            [
            // Single Existent Script
            self::$script,
            // Empty
            self::$geoCodes->scripts()->take(0)->first(),
            ] as $testCase
        ) {
            $xml = $testCase->toXmlAndValidate();
            $this->assertIsString($xml);
            $decodedXml = simplexml_load_string($xml);
            $this->assertInstanceOf(SimpleXMLElement::class, $decodedXml, 'Not a valid XML');
        }
    }

    /**
     * @test
     * @testdox Test the `->first()->toArray()` feature.
     * @depends testFirstFeature
     * @return void
     */
    public function testFirstToArrayFeature(): void
    {
        $array = self::$script->toArray();
        $this->assertIsArray($array, 'Not a valid Array');
    }

    /**
     * @test
     * @testdox Test the `->first()->toFlatten()` feature (default separator `.`).
     * @depends testFirstFeature
     * @return void
     */
    public function testFirstToFlattenFeature(): void
    {
        $flatten = self::$script->toFlatten();
        $this->assertIsArray($flatten, 'Not a valid Array');
        $regex = '/\./';
        foreach ($flatten as $key => $val) {
            if (preg_match('/^unicode/', $key)) {
                $this->assertTrue(preg_match($regex, $key) === 1);
            }
            if (preg_match('/^writingDirection/', $key)) {
                $this->assertTrue(preg_match($regex, $key) === 1);
            }
        }
    }

    /**
     * @test
     * @testdox Test the `->first()->toFlatten('_')` feature, using custom separator.
     * @depends testFirstFeature
     * @return void
     */
    public function testFirstToFlattenFeatureCustomSeparator(): void
    {
        $flatten = self::$script->toFlatten('_');
        $this->assertIsArray($flatten, 'Not a valid Array');
        $regex = '/_/';
        foreach ($flatten as $key => $val) {
            if (preg_match('/^unicode/', $key)) {
                $this->assertTrue(preg_match($regex, $key) === 1);
            }
            if (preg_match('/^writingDirection/', $key)) {
                $this->assertTrue(preg_match($regex, $key) === 1);
            }
        }
    }

    /**
     * @test
     * @testdox Test the `->count()` feature on the list of Scripts.
     * @return void
     * @throws QueryException
     */
    public function testCountOfScripts(): void
    {
        $scripts = self::$geoCodes->scripts();
        $count = $scripts->count();
        $this->assertEquals(
            self::$scriptsTotalCount,
            $count,
            "The TOTAL number of the scripts doesn't match with " . self::$scriptsTotalCount
        );

        foreach ([(self::$scriptsTotalCount - 21), 27, 5, 32, 0] as $numberOfItems) {
            $scripts->offset(21)->limit($numberOfItems);
            $count = $scripts->count();
            $this->assertEquals(
                $numberOfItems,
                $count,
                "The number of the scripts doesn't match with " . $numberOfItems
            );
        }
    }

    /**
     * @test
     * @testdox Test the interval `->offset()->limit()` or the aliases `->skip()->take()` features.
     * @return void
     * @throws QueryException
     */
    public function testLimit(): void
    {
        $scripts = self::$geoCodes->scripts();

        // Invalid - `offset` less than 0
        try {
            $scripts->offset(-2)->limit(20);
            $this->fail('An invalid limit from has been accepted');
        } catch (QueryException $e) {
            $this->assertInstanceOf(QueryException::class, $e);
            $this->assertEquals(11004, $e->getCode());
        }

        // Invalid - `limit` less than 0
        try {
            $scripts->offset(20)->limit(-5);
            $this->fail('An invalid limit numberOfItems has been accepted');
        } catch (QueryException $e) {
            $this->assertInstanceOf(QueryException::class, $e);
            $this->assertEquals(11003, $e->getCode());
        }

        // Valid input
        $scripts->offset(22)->limit(2);
        $this->assertEquals(2, $scripts->count());
        $get = $scripts->get();
        $this->assertEquals(self::$expectedLimitTest[0], $get->{0}->code);
        $this->assertEquals(self::$expectedLimitTest[1], $get->{1}->code);

        // Alias input
        $scripts->skip(22)->take(2);
        $this->assertEquals(2, $scripts->count());
        $get = $scripts->get();
        $this->assertEquals(self::$expectedLimitTest[0], $get->{0}->code);
        $this->assertEquals(self::$expectedLimitTest[1], $get->{1}->code);
    }

    /**
     * @test
     * @testdox Test the `->orderBy()` feature.
     * @return void
     * @throws QueryException
     */
    public function testOrderBy(): void
    {
        // Test multiple calls.
        $scripts = self::$geoCodes->useLanguage('en')->scripts();
        $scripts->orderBy('code');
        $script = $scripts->first();
        $this->assertEquals(self::$expectedOrderByTest['code']['ASC'], $script->code);
        $scripts->orderBy('code', 'desc');
        $script = $scripts->first();
        $this->assertEquals(self::$expectedOrderByTest['code']['DESC'], $script->code);
    }
    /**
     * @dataProvider dataProviderIndexes
     * @testdox ==>  using $index as property
     * @throws QueryException
     */
    public function testOrderByWithDataProvider(string $index): void
    {
        $scripts = self::$geoCodes->useLanguage('en')->scripts();
        $asc = $scripts->orderBy($index)->first();
        $this->assertEquals(self::$expectedOrderByTest[$index]['ASC'], $asc->{$index});
        $desc = $scripts->orderBy($index, 'desc')->first();
        $this->assertEquals(self::$expectedOrderByTest[$index]['DESC'], $desc->{$index});
    }

    /**
     * @test
     * @testdox  ==>  using an invalid properties
     * @return void
     * @throws QueryException
     */
    public function testOrderByWithException(): void
    {
        $scripts = self::$geoCodes->scripts();

        // Invalid - `property` not indexable
        try {
            $scripts->orderBy('notIndexable');
            $this->fail('An invalid orderBy property has been accepted');
        } catch (QueryException $e) {
            $this->assertInstanceOf(QueryException::class, $e);
            $this->assertEquals(11005, $e->getCode());
            $this->assertEquals(1, preg_match('/"notIndexable"/', $e->getMessage()));
        }

        // Invalid - `orderType` invalid
        try {
            $scripts->orderBy('code', 'invalid');
            $this->fail('An invalid orderBy type has been accepted');
        } catch (QueryException $e) {
            $this->assertInstanceOf(QueryException::class, $e);
            $this->assertEquals(11006, $e->getCode());
        }
    }

    /**
     * @test
     * @testdox Test the indexes
     * @return void
     * @throws QueryException
     */
    public function testIndexes(): void
    {
        $indexes = self::$geoCodes->scripts()->getIndexes();
        $this->assertIsArray($indexes);
        $indexes = array_keys($indexes);
        $this->assertEquals($indexes, self::$constants['indexes']);
    }

    /**
     * @dataProvider dataProviderIndexes
     * @testdox ==>  using $index as index
     * @throws QueryException
     */
    public function testIndexesWithDataProvider(string $index): void
    {
        foreach (
            self::$geoCodes->scripts()->withIndex($index)->offset(0)->limit(1)->get()->toArray() as $key => $script
        ) {
            $this->assertEquals($key, $script[$index]);
        }
    }
    /**
     * @return array<array<int, int|string>>
     */
    public function dataProviderIndexes(): array
    {
        return array_map(
            function ($index) {
                return [$index];
            },
            (array) self::$constants['indexes']
        );
    }
    /**
     * @test
     * @testdox ==>  using an invalid index
     * @return void
     */
    public function testIndexFeatureWithException(): void
    {
        try {
            self::$geoCodes->scripts()->withIndex('invalidField');
            $this->fail('The index is considered valid');
        } catch (QueryException $e) {
            $this->assertInstanceOf(QueryException::class, $e);
            $this->assertEquals(11001, $e->getCode());
            $this->assertEquals(1, preg_match('/"invalidField"/', $e->getMessage()));
        }
    }

    /**
     * @test
     * @testdox Tests on the selectable fields.
     * @return void
     * @throws QueryException
     */
    public function testSelectableFields(): void
    {
        $selectFields = self::$geoCodes->scripts()->selectableFields();
        $this->assertIsArray($selectFields);
        $scripts = self::$geoCodes->scripts()->get();
        foreach ($scripts->collect() as $script) {
            foreach ($selectFields as $key => $description) {
                $prop = $key;
                $object = $script;
                if (preg_match('/\./', $prop)) {
                    list($prop0, $prop) = explode('.', $prop);
                    $object = $script->{$prop0};
                }

                // check the existence of the field
                $this->assertTrue(
                    property_exists($object, $prop),
                    'Key `' . $key . '` not present in the script object'
                );

                // check the type of the key
                if (preg_match('/\[(.*?)\]/', $description, $matches) === 1) {
                    $type = $matches[1];
                } else {
                    $this->fail('Description for key `' . $key . '` must contain a [type]');
                }

                $getType = gettype($object->{$prop});
                if (strpos($type, '?') === 0) {
                    $type = substr($type, 1);
                    $assert = $getType === $type || $getType === 'NULL';
                } else {
                    $assert = $getType === $type;
                }
                $this->assertTrue(
                    $assert,
                    'Key type `' . $key . '` for the script `' . $script->name .
                    '` (`' . $getType . '`) does not match with the declared type (`' . $type . '`)'
                );
            }
        }
    }

    /**
     * @param array<string> $selectFields
     * @throws QueryException
     */
    private function checkThePropertyAfterSelect(array $selectFields): void
    {
        $script = self::$geoCodes->scripts()->first();
        foreach ($selectFields as $key) {
            $prop = $key;
            $object = $script;
            if (preg_match('/\./', $prop)) {
                list($prop0, $prop) = explode('.', $prop);
                $object = $script->{$prop0};
            }
            // check the existence of the field
            $this->assertTrue(
                property_exists($object, $prop),
                'Key `' . $key . '` not present in the script object'
            );
        }
    }
    /**
     * @test
     * @testdox ==>  all the selectable properties with a single ->select() call
     * @return void
     * @throws QueryException
     */
    public function testAllPropertiesWithSingleSelectCall(): void
    {
        $scripts = self::$geoCodes->scripts();
        $selectFields = array_keys($scripts->selectableFields());
        $scripts->select(...$selectFields);
        $this->checkThePropertyAfterSelect($selectFields);
    }
    /**
     * @test
     * @testdox ==>  all the selectable properties with multiple ->select() calls
     * @return void
     * @throws QueryException
     */
    public function testMultipleSelectCalls(): void
    {
        $scripts = self::$geoCodes->scripts();
        $selectFields = array_keys($scripts->selectableFields());
        foreach ($selectFields as $key) {
            $scripts->select($key);
            $scripts->select($key); // test also the redundancy
        }
        $this->checkThePropertyAfterSelect($selectFields);
    }
    /**
     * @test
     * @testdox ==>  the sigle property in a single ->select() call
     * @return void
     */
    public function testSingleSelect(): void
    {
        $this->assertTrue(true);
    }
    /**
     * @dataProvider dataProviderSelect
     * @testdox ====>  using ->select('$select')
     * @throws QueryException
     */
    public function testSelectWithDataProvider(string $select): void
    {
        $scripts = self::$geoCodes->scripts();
        $scripts->select($select);
        $script = $scripts->first();
        if (preg_match('/\./', $select)) {
            list($prop0, $prop) = explode('.', $select);
            $script = $script->{$prop0};
        }
        $count = count(get_object_vars($script));
        $this->assertEquals(1, $count);
    }
    /**
     * @return array<array<int, int|string>>
     */
    public function dataProviderSelect(): array
    {
        return array_map(
            function ($select) {
                return [$select];
            },
            (array) self::$constants['selectables']
        );
    }
    /**
     * @test
     * @testdox ====>  using an invalid property
     * @return void
     */
    public function testSelectFeatureWithException(): void
    {
        try {
            self::$geoCodes->scripts()->select('invalidField');
            $this->fail('The select is considered valid');
        } catch (QueryException $e) {
            $this->assertInstanceOf(QueryException::class, $e);
            $this->assertEquals(11002, $e->getCode());
            $this->assertEquals(1, preg_match('/"invalidField"/', $e->getMessage()));
        }
    }


    /**
     * @test
     * @testdox Tests on the fetching feature.
     * @return void
     */
    public function testFetchFeature(): void
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     * @testdox ==> using - as valid - in input integer, string, array.
     * @return void
     * @throws QueryException
     */
    public function testFetchFeatureValidInput(): void
    {
        $cfr = [
            'Latn'      => [ 'code' => 'Latn' ],
            'Jpan'      => [ 'code' => 'Jpan' ],
            'Cyrl'      => [ 'code' => 'Cyrl' ],
            'Arab'      => [ 'code' => 'Arab' ]
        ];
        $scripts = self::$geoCodes->scripts();
        $scripts->fetch('Latn', 413, ['Cyrl', 'Arab']);
        $result = $scripts->withIndex('code')->select('code')->get()->toArray();
        $this->assertEquals($cfr, $result);
    }

    /**
     * @test
     * @testdox ==> with Exception (using array or arrays in input).
     * @return void
     * @throws QueryException
     */
    public function testFetchFeatureWithException(): void
    {
        $scripts = self::$geoCodes->scripts();
        try {
            $scripts->fetch('Latn', 413, [['Cyrl'], ['Arab']]);
            $this->fail('The fetch is considered valid');
        } catch (QueryException $e) {
            $this->assertInstanceOf(QueryException::class, $e);
            $this->assertEquals(11007, $e->getCode());
        }
    }

    /**
     * @test
     * @testdox ==> with multiple calls ->fetch(...)->fetch(...)
     * @return void
     * @throws QueryException
     */
    public function testMultipleFetchFeature(): void
    {
        $cfr = [
            'Latn'      => [ 'code' => 'Latn' ],
            'Jpan'      => [ 'code' => 'Jpan' ],
            'Cyrl'      => [ 'code' => 'Cyrl' ],
            'Arab'      => [ 'code' => 'Arab' ]
        ];
        $scripts = self::$geoCodes->scripts();
        $scripts->fetch('Latn', 413)->fetch(['Cyrl', 'Arab']);
        $result = $scripts->withIndex('code')->select('code')->get()->toArray();
        $this->assertEquals($cfr, $result);
    }

    /**
     * @test
     * @testdox ==> with the ->fetchAll() or the ->fetch('*') or the ->fetch(..., '*') features
     * @return void
     * @throws QueryException
     */
    public function testFetchAllFeature(): void
    {
        $scripts = self::$geoCodes->scripts();
        $fetchAll = $scripts->fetchAll()->get();
        $fetchStar = $scripts->fetch('*')->get();
        $fetchWithStar = $scripts->fetch('Latn', 413, ['Cyrl', 'Arab'], '*')->get();
        $this->assertEquals($fetchAll, self::$scriptsList);
        $this->assertEquals($fetchStar, self::$scriptsList);
        $this->assertEquals($fetchWithStar, self::$scriptsList);
    }

    /**
     * @test
     * @testdox Test operations on the fetched groups
     * @return void
     */
    public function testOperationsOnFetchedGroup(): void
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     * @testdox ==> with the ->merge() command
     * @return void
     * @throws QueryException
     */
    public function testMerge(): void
    {
        $cfr = [
            'Latn'      => [ 'code' => 'Latn' ],
            'Jpan'      => [ 'code' => 'Jpan' ],
            'Cyrl'      => [ 'code' => 'Cyrl' ],
            'Arab'      => [ 'code' => 'Arab' ]
        ];
        $scripts = self::$geoCodes->scripts();
        $scripts->fetch('Latn', 413)->fetch(['Cyrl', 'Arab']);
        $scripts->merge();
        $result = $scripts->withIndex('code')->select('code')->get()->toArray();
        $this->assertEquals($cfr, $result);
    }

    /**
     * @test
     * @testdox ====> after multiple operations
     * @return void
     * @throws QueryException
     */
    public function testMergeMultiples(): void
    {
        $cfr = [
            'Latn'      => [ 'code' => 'Latn' ],
            'Jpan'      => [ 'code' => 'Jpan' ],
            'Cyrl'      => [ 'code' => 'Cyrl' ],
            'Arab'      => [ 'code' => 'Arab' ]
        ];
        $scripts = self::$geoCodes->scripts();
        $scripts->fetch(413)->fetch(['Cyrl']);
        $scripts->merge();
        $scripts->fetch('Arab')->fetch(['Latn']);
        $scripts->merge();
        $result = $scripts->withIndex('code')->select('code')->get()->toArray();
        $this->assertEquals($cfr, $result);
    }

    /**
     * @test
     * @testdox ==> with the ->intersect() command
     * @return void
     * @throws QueryException
     */
    public function testIntersect(): void
    {
        $cfr = [
            'Jpan'    => [ 'code' => 'Jpan']
        ];
        $scripts = self::$geoCodes->scripts();
        $scripts->fetch('Cyrl', 413)->fetch(['Latn', 'Arab', 'Jpan']);
        $scripts->intersect();
        $result = $scripts->withIndex('code')->select('code')->get()->toArray();
        $this->assertEquals($cfr, $result);
    }
    /**
     * @test
     * @testdox ====> after multiple operations
     * @return void
     * @throws QueryException
     */
    public function testIntersectMultiples(): void
    {
        $cfr = [
            'Jpan'    => [ 'code' => 'Jpan']
        ];
        $scripts = self::$geoCodes->scripts();
        $scripts->fetch('Latn', 413)->fetch(['Cyrl', 'Arab', 'Jpan']);
        $scripts->intersect();
        $scripts->fetch('Latn', 'Jpan')->fetch(['Cyrl', 'Arab', 413]);
        $scripts->intersect();
        $scripts->intersect();
        $result = $scripts->withIndex('code')->select('code')->get()->toArray();
        $this->assertEquals($cfr, $result);
    }


    /**
     * @test
     * @testdox ====> with thrown exception
     * @return void
     * @throws QueryException
     */
    public function testIntersectException(): void
    {
        $scripts = self::$geoCodes->scripts();
        $scripts->fetch('Latn', 413);
        try {
            $scripts->intersect();
            $this->fail('The intersect has been allowed');
        } catch (QueryException $e) {
            $this->assertInstanceOf(QueryException::class, $e);
            $this->assertEquals(11008, $e->getCode());
        }
    }

    /**
     * @test
     * @testdox ==> with the ->complement() (simmetric complement) command
     * @return void
     * @throws QueryException
     */
    public function testComplement(): void
    {
        $cfr = [
            'Latn'       => [ 'code' => 'Latn' ],
            'Cyrl'       => [ 'code' => 'Cyrl' ],
            'Arab'       => [ 'code' => 'Arab' ]
        ];
        $scripts = self::$geoCodes->scripts();
        $scripts->fetch('Latn', 413)->fetch(['Cyrl', 'Arab', 'Jpan']);
        $scripts->complement();
        $result = $scripts->withIndex('code')->select('code')->get()->toArray();
        $this->assertEquals($cfr, $result);
    }

    /**
     * @test
     * @testdox ====> after multiple operations
     * @return void
     * @throws QueryException
     */
    public function testComplementMultiples(): void
    {
        $cfr = [
            'Jpan'          => [ 'code' => 'Jpan' ],
            'Kali'          => [ 'code' => 'Kali' ]
        ];
        $scripts = self::$geoCodes->scripts();
        $scripts->fetch(413, 'Kali')->fetch(['Jpan']);
        $scripts->complement();
        $scripts->fetch(413, 357)->fetch(['Kali']);
        $scripts->complement();
        $scripts->complement();
        $result = $scripts->withIndex('code')->select('code')->get()->toArray();
        $this->assertEquals($cfr, $result);
    }

    /**
     * @test
     * @testdox ====> with thrown exception
     * @return void
     * @throws QueryException
     */
    public function testComplementException(): void
    {
        $scripts = self::$geoCodes->scripts();
        $scripts->fetch('Latn', 413);
        try {
            $scripts->complement();
            $this->fail('The symmetric complement has been allowed');
        } catch (QueryException $e) {
            $this->assertInstanceOf(QueryException::class, $e);
            $this->assertEquals(11009, $e->getCode());
        }
    }

    /**
     * @test
     * @testdox Test the conditions ->where() and ->orWhere()
     * @return void
     */
    public function testConditions(): void
    {
        $this->assertTrue(true);
    }
    /**
     * @test
     * @testdox ==> invalid conditions
     * @return void
     */
    public function testInvalidConditions(): void
    {
        $this->assertTrue(true);
    }

    /**
     * @dataProvider dataProviderInvalidConditions
     * @testdox ====>  is invalid using ->where($txt) or ->orWhere($txt)
     *
     * @param string $txt
     * @param array<array<int, int|string>> $args
     * @param int $errorCode
     * @param array<string> $matches
     * @throws QueryException
     */
    public function testConditionsWithDataProviderInvalid(
        string $txt,
        array $args,
        int $errorCode,
        array $matches = []
    ): void {
        $scripts = self::$geoCodes->scripts();
        try {
            $scripts->where(...$args);
            $this->fail('The condition is considered valid');
        } catch (QueryException $e) {
            $this->assertInstanceOf(QueryException::class, $e);
            $this->assertEquals($errorCode, $e->getCode());
            if (!empty($matches)) {
                foreach ($matches as $match) {
                    $this->assertEquals(1, preg_match('/"' . $match . '"/i', $e->getMessage()));
                }
            }
        }
        try {
            $scripts->orWhere(...$args);
            $this->fail('The condition is considered valid');
        } catch (QueryException $e) {
            $this->assertInstanceOf(QueryException::class, $e);
            $this->assertEquals($errorCode, $e->getCode());
            if (!empty($matches)) {
                foreach ($matches as $match) {
                    $this->assertEquals(1, preg_match('/"' . $match . '"/i', $e->getMessage()));
                }
            }
        }
    }
    /**
     * @return array<
     *     int,
     *     array<int,
     *      array<int, array<int, array<int, array<int, string>|string>|int|string>|bool|int|string>|int|string>>
     */
    public function dataProviderInvalidConditions(): array
    {
        return [
            [
                "'sss'",
                ['sss'],
                11011
            ],
            [
                "'sss', '5', 'aaa', 'aaa'",
                ['sss', '5', 'aaa', 'aaa'],
                11010
            ],
            [
                "['field', 'operator', 'term'], ['field', 'operator', 'term']",
                [['field', 'operator', 'term'], ['field', 'operator', 'term']],
                11010
            ],
            [
                "5, '5', 'aaa'",
                [5, '5', 'aaa'],
                11010
            ],
            [
                "['5'], '5', 'aaa'",
                [['5'], '5', 'aaa'],
                11010
            ],
            [
                "[5], ['5'], ['aaa']",
                [[5], ['5'], ['aaa']],
                11010
            ],
            [
                "['5'], ['5'], ['aaa']",
                [['5'], ['5'], ['aaa']],
                11010
            ],
            [
                "true, '5', 'aaa'",
                [true, '5', 'aaa'],
                11010
            ],
            [
                "['field', 'operator', 'term']",
                [['field', 'operator', 'term']],
                11012,
                ['operator']
            ],
            [
                "['field', '=', 'term'],['field', 'operator', 'term'],['field', 'operator', 'term']",
                [['field', '=', 'term'],['field', 'operator', 'term'],['field', 'operator', 'term']],
                11010
            ],
            [
                "[['field', '=', 'term'],['field', 'operator', 'term'],['field', 'operator', 'term']]",
                [[['field', '=', 'term'],['field', 'operator', 'term'],['field', 'operator', 'term']]],
                11015,
                ['field']
            ],
            [
                "[['field', '=', 'term'],['field', 'operator', 'term'],['field', 'operator', 'term']]",
                [[['field', '=', 'term'],['field', '=', 'term']], ['field', '=', 'term']],
                11010
            ],
            [
                "[[]]",
                [[[]]],
                11011
            ],
            [
                "[[['field'], 'operator', 'term']]",
                [[[['field'], 'operator', 'term']]],
                11011
            ],
            [
                "[['field', ['operator'], 'term']]",
                [[['field', ['operator'], 'term']]],
                11011
            ],
            [
                "'cOdE', 'Latn'",
                ['cOdE', 'Latn'],
                11015,
                ['cOdE']
            ],
            [
                "'code.inexistent', 'Latn'",
                ['code.inexistent', 'Latn'],
                11015,
                ['code.inexistent']
            ],
            [
                "['code.inexistent', 'Latn']",
                [['code.inexistent', 'Latn']],
                11015,
                ['code.inexistent']
            ],
            [
                "[['code.inexistent', 'Latn']]",
                [[['code.inexistent', 'Latn']]],
                11015,
                ['code.inexistent']
            ],
            [
                "['code', '=', ['Latn', 'Kana']]]",
                [['code', '=', ['Latn', 'Kana']]],
                11014,
                ['=']
            ],
            [
                "[['code', 'IN', 'Latn']]",
                [[['code', 'IN', 'Latn']]],
                11013,
                ['IN']
            ]
        ];
    }

    /**
     * @dataProvider dataProviderValidConditions
     * @testdox ====>  has valid result using ->$method($txt)
     *
     * @param string $txt
     * @param array<array<int, int|string>> $args
     * @param string $method
     * @param array<string> $matches
     * @throws QueryException
     */
    public function testConditionsWithDataProviderValid(
        string $txt,
        array $args,
        string $method,
        array $matches = []
    ): void {
        $scripts = self::$geoCodes->scripts();
        $scripts->$method(...$args);
        $result = $scripts->withIndex('code')->select('code')->get()->toArray();
        $this->assertEquals($matches, $result);
    }
    /**
     * @return array<int, array<int,
     * array<int|string, array<int|string, array<int, int|string>|string>|string>|string>>
     */
    public function dataProviderValidConditions(): array
    {
        return [
            [
                "'code', 'Hans'",
                ['code', 'Hans'],
                'where',
                ['Hans' => [ 'code' => 'Hans' ]]
            ],
            [
                "'code', '=', 'Hans'",
                ['code', '=', 'Hans'],
                'where',
                ['Hans' => [ 'code' => 'Hans' ]]
            ],
            [
                "['code', '=', 'Hans']",
                [['code', '=', 'Hans']],
                'where',
                ['Hans' => [ 'code' => 'Hans' ]]
            ],
            [
                "[['code', '=', 'Hans']]",
                [[['code', '=', 'Hans']]],
                'where',
                ['Hans' => [ 'code' => 'Hans' ]]
            ],
            [
                "[['code', '=', 'Hans'], ['code', '=', 'Hant']]",
                [[['code', '=', 'Hans'], ['code', '=', 'Hant']]],
                'where',
                []
            ],
            [
                "[['code', '=', 'Hans'], ['numeric', '=', '501']]",
                [[['code', '=', 'Hans'], ['numeric', '=', '501']]],
                'where',
                ['Hans' => [ 'code' => 'Hans' ]]
            ],
            [
                "[['code', '=', 'Hans'], ['numeric', '=', '501'], ['writingDirection.code', 'ltr']]",
                [[['code', '=', 'Hans'], ['numeric', '=', '501'], ['writingDirection.code', 'ltr']]],
                'where',
                ['Hans' => [ 'code' => 'Hans' ]]
            ],
            [
                "[['code', '=', 'Hans'],['numeric', '=', '501'],['writingDirection.code', 'ltr'],
['unicode.version', '1.1']]",
                [
                    [
                        ['code', '=', 'Hans'], ['numeric', '=', '501'], ['writingDirection.code', 'ltr'],
                        ['unicode.version', '1.1']
                    ]   ],
                'where',
                ['Hans' => [ 'code' => 'Hans' ]]
            ],
//            [
//                "'tags', 'zone'",
//                ['tags', 'zone'],
//                'where',
//                ['Arab' => [ 'code' => 'Arab' ]]
//            ],
            [
                "'name', 'like', 'Kayah%'",
                ['name', 'like', 'Kayah%'],
                'where',
                ['Kali' => [ 'code' => 'Kali' ]]
            ],
            [
                "'name', 'like', '%Etruscan%'",
                ['name', 'like', '%Etruscan%'],
                'where',
                ['Ital' => [ 'code' => 'Ital' ]]
            ],
            [
                "'name', 'like', '%Mandaean'",
                ['name', 'like', '%Mandaean'],
                'where',
                ['Mand' => [ 'code' => 'Mand' ]]
            ],
            [
                "'name', 'like', '%Oscan%'",
                ['name', 'like', '%Oscan%'],
                'where',
                ['Ital' => [ 'code' => 'Ital' ]]
            ],
            [
                "'name', 'like', '%alic%'",
                ['name', 'like', '%alic%'],
                'where',
                ['Ital' => [ 'code' => 'Ital' ]]
            ],
            [
                "[['name', 'like', '%Syriac%'], ['name', 'not like', '%variant%']]",
                [[['name', 'like', '%Syriac%'], ['name', 'not like', '%variant%']]],
                'where',
                ['Syrc' => [ 'code' => 'Syrc' ]]
            ],
            [
                "[['numeric', '<=', 15]]",
                [[['numeric', '<=', 15]]],
                'where',
                ['Pcun' => [ 'code' => 'Pcun' ]]
            ],
            [
                "[['numeric', '>', '160'], ['numeric', '<', '162']]",
                [[['numeric', '>', '160'], ['numeric', '<', '162']]],
                'where',
                ['Aran' => [ 'code' => 'Aran' ]]
            ],
            [
                "[['numeric', 124], ['code', 'aRmI']]",
                [[['numeric', 124], ['code', 'aRmI']]],
                'where',
                ['Armi' => [ 'code' => 'Armi' ]]
            ],
        ];
    }
}
