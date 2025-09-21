<?php

namespace Alibe\GeoCodes\Tests;

use Alibe\GeoCodes\Lib\DataObj\Languages;
use Alibe\GeoCodes\Lib\DataObj\Elements\Language;
use Alibe\GeoCodes\Lib\Exceptions\GeneralException;
use Alibe\GeoCodes\Lib\Exceptions\QueryException;
use DOMDocument;
use PHPUnit\Framework\TestCase;
use Alibe\GeoCodes\GeoCodes;
use SimpleXMLElement;
use Symfony\Component\Yaml\Yaml;

/**
 * @testdox Languages
 */
final class IsoLanguagesTest extends TestCase
{
    /**
     * @var int
     */
    private static int $languagesTotalCount = 7923;

    /**
     * @var array<int|array<string>> $constants
     */
    private static array $constants = [
        'indexes' => [
            'isoCode',
        ],
        'selectables' => [
            'isoCode',
            'part2b',
            'part2t',
            'part1',
            'glottoCode',
            'scope',
            'type',
            'macroLanguageRef',
            'scripts'
        ]
    ];

    /**
     * @var array<int, string> $expectedLimitTest
     */
    private static array $expectedLimitTest = [
        'aba',
        'abb'
    ];

    /**
     * @var array<string, array<string, string>> $expectedOrderByTest
     */
    private static array $expectedOrderByTest = [
        'isoCode' => [
            'ASC' => 'aaa',
            'DESC' => 'zzj',
        ],
        'name' => [
            'ASC' => 'ADB Unit of Account',
            'DESC' => 'Zloty',
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
     * @var Languages
     */
    private static Languages $languagesSetsList;


    /**
     * @var Language
     */
    private static Language $language;

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
     * @testdox Test `->get()` the list of languages is object as instance of Languages.
     * @return void
     * @throws QueryException
     */
    public function testToGetListOfLanguages(): void
    {
        /** @phpstan-ignore-next-line   The unique object type is needed for php 7.4 */
        self::$languagesSetsList = self::$geoCodes->languages()->get();
        $this->assertIsObject(self::$languagesSetsList);
        $this->assertInstanceOf(Languages::class, self::$languagesSetsList);
    }

    /**
     * @test
     * @testdox Test the elements of the list of languages are an instance of Language.
     * @depends testToGetListOfLanguages
     * @return void
     */
    public function testToGetElementListOfLanguages(): void
    {
        $this->assertIsObject(self::$languagesSetsList->{0});
        $this->assertInstanceOf(Language::class, self::$languagesSetsList->{0});
    }

    /**
     * @test
     * @testdox Test the `->get()->toJson()` feature.
     * @depends testToGetListOfLanguages
     * @return void
     * @throws QueryException
     */
    public function testGetToJsonFeature(): void
    {
        $languages = self::$geoCodes->languages();
        foreach (
            [
            // Whole list
            self::$languagesSetsList,
            // Whole list with index
            $languages->withIndex('isoCode')->get(),
            // Single element in list
            $languages->take(1)->get(),
            // Empty
            $languages->take(0)->get(),
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
     * @testdox  ==>  Test the json serializing.
     * @depends testToGetListOfLanguages
     * @return void
     * @throws QueryException
     */
    public function testJsonSerializing(): void
    {
        $languages = self::$geoCodes->languages();

        $withoutIndex = $languages->get()->toJson();
        $decodedWithoutIndex = json_decode($withoutIndex, true);
        $this->assertTrue(Utils::isList($decodedWithoutIndex));

        $withIndex = $languages->withIndex('isoCode')->get()->toJson();
        $decodedWithIndex = json_decode($withIndex, true);
        $this->assertFalse(Utils::isList($decodedWithIndex));
        $this->assertArrayHasKey('ita', $decodedWithIndex);
    }

    /**
     * @test
     * @testdox Test the `->get()->toYaml()` feature.
     * @depends testToGetListOfLanguages
     * @return void
     * @throws QueryException
     */
    public function testGetToYamlFeature(): void
    {
        $languages = self::$geoCodes->languages();
        foreach (
            [
            // Whole list
            self::$languagesSetsList,
            // Whole list with index
            $languages->withIndex('isoCode')->get(),
            // Single element in list
            $languages->take(1)->get(),
            // Empty
            $languages->take(0)->get(),
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
     * @depends testToGetListOfLanguages
     * @return void
     * @throws GeneralException|QueryException
     */
    public function testGetXsdFeatures(): void
    {
        self::$xsdList = self::$geoCodes->languages()->getXsd();
        $this->assertIsString(self::$xsdList);

        self::$xsdSingle = self::$geoCodes->languages()->getXsdSingle();
        $this->assertIsString(self::$xsdSingle);
    }

    /**
     * @test
     * @testdox Test the `->get()->toXml()` feature and validate it with external xsd.
     * @depends testToGetListOfLanguages
     * @depends testGetXsdFeatures
     * @return void
     * @throws QueryException|GeneralException
     */
    public function testGetToXmlFeatureWithExternalValidation(): void
    {
        $languages = self::$geoCodes->languages();
        $i = 0;
        foreach (
            [
            // Whole list
            self::$languagesSetsList,
            // Whole list with index
            $languages->withIndex('isoCode')->get(),
            // Single element in list
            $languages->take(1)->get(),
            // Empty
            $languages->take(0)->get(),
            ] as $testCase
        ) {
            $xml = $testCase->toXml();
            $this->assertIsString($xml);
            $decodedXml = simplexml_load_string($xml);
            $this->assertInstanceOf(SimpleXMLElement::class, $decodedXml, 'Not a valid XML');
            $dom = new DOMDocument();
            $dom->loadXML($xml);
            $this->assertTrue($dom->schemaValidateSource(self::$xsdList), 'Not a valid XML Schema'); // alibe
            $i++;
        }
    }

    /**
     * @test
     * @testdox Test the `->get()->toXmlAndValidate()` feature.
     * @depends testToGetListOfLanguages
     * @return void
     * @throws QueryException|GeneralException
     */
    public function testGetToXmlAndValidateFeature(): void
    {
        $languages = self::$geoCodes->languages();
        foreach (
            [
            // Whole list
            self::$languagesSetsList,
            // Whole list with index
            $languages->withIndex('isoCode')->get(),
            // Single element in list
            $languages->take(1)->get(),
            // Empty
            $languages->take(0)->get(),
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
     * @depends testToGetListOfLanguages
     * @return void
     */
    public function testGetToArrayFeature(): void
    {
        $array = self::$languagesSetsList->toArray();
        $this->assertIsArray($array, 'Not a valid Array');

        $array = self::$languagesSetsList->{0}->toArray();
        $this->assertIsArray($array, 'Not a valid Array');
    }

    /**
     * @test
     * @testdox Test the `->get()->toFlatten()` feature (default separator `.`).
     * @depends testToGetListOfLanguages
     * @return void
     */
    public function testGetToFlattenFeature(): void
    {
        $flatten = self::$languagesSetsList->toFlatten();
        $this->assertIsArray($flatten, 'Not a valid Array');
        foreach (
            [
            mt_rand(0, (self::$languagesTotalCount - 1)),
            mt_rand(0, (self::$languagesTotalCount - 1)),
            mt_rand(0, (self::$languagesTotalCount - 1)),
            mt_rand(0, (self::$languagesTotalCount - 1)),
            mt_rand(0, (self::$languagesTotalCount - 1))
            ] as $key
        ) {
            $this->assertEquals(self::$languagesSetsList->$key->isoCode, $flatten[$key . '.isoCode']);
            $this->assertEquals(self::$languagesSetsList->$key->glottoCode, $flatten[$key . '.glottoCode']);
//            $this->assertEquals(self::$languagesSetsList->$key->name, $flatten[$key . '.name']); // alibe
        };
    }

    /**
     * @test
     * @testdox Test the `->get()->toFlatten('_')` feature, using custom separator.
     * @depends testToGetListOfLanguages
     * @return void
     */
    public function testGetToFlattenFeatureCustomSeparator(): void
    {
        $flatten = self::$languagesSetsList->toFlatten('_');
        $this->assertIsArray($flatten, 'Not a valid Array');
        foreach (
            [
                     mt_rand(0, (self::$languagesTotalCount - 1)),
                     mt_rand(0, (self::$languagesTotalCount - 1)),
                     mt_rand(0, (self::$languagesTotalCount - 1)),
                     mt_rand(0, (self::$languagesTotalCount - 1)),
                     mt_rand(0, (self::$languagesTotalCount - 1))
                 ] as $key
        ) {
            $this->assertEquals(self::$languagesSetsList->$key->isoCode, $flatten[$key . '_isoCode']);
            $this->assertEquals(self::$languagesSetsList->$key->glottoCode, $flatten[$key . '_glottoCode']);
//            $this->assertEquals(self::$languagesSetsList->$key->name, $flatten[$key . '_name']); // alibe
        };
    }

    /**
     * @test
     * @testdox Test the `->first()` feature as instance of Language.
     * @return void
     * @throws QueryException
     */
    public function testFirstFeature(): void
    {
        /** @phpstan-ignore-next-line   The unique object type is needed for php 7.4 */
        self::$language = self::$geoCodes->languages()->first();

        $this->assertIsObject(self::$language);
        $this->assertInstanceOf(Language::class, self::$language);
    }

    /**
     * @test
     * @testdox Test the `->first()` feature when result is empty as instance of Language.
     * @return void
     * @throws QueryException
     */
    public function testFirstFeatureOnEmpty(): void
    {
        $languages = self::$geoCodes->languages();
        $languages->offset(0)->limit(0);
        $language = $languages->first();

        $this->assertIsObject($language);
        $this->assertInstanceOf(Language::class, $language);
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
                // Single Existent Language
                self::$language,
                // Empty
                self::$geoCodes->languages()->take(0)->first(),
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
                // Single Existent Language
                self::$language,
                // Empty
                self::$geoCodes->languages()->take(0)->first(),
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
                // Single Existent Language
                self::$language,
                // Empty
                self::$geoCodes->languages()->take(0)->first(),
            ] as $testCase
        ) {
            $xml = $testCase->toXml();
            $this->assertIsString($xml);
            $decodedXml = simplexml_load_string($xml);
            $this->assertInstanceOf(SimpleXMLElement::class, $decodedXml, 'Not a valid XML');
            $dom = new DOMDocument();
            $dom->loadXML($xml);
            $this->assertTrue($dom->schemaValidateSource(self::$xsdSingle), 'Not a valid XML Schema'); // alibe
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
            // Single Existent Language
            self::$language,
            // Empty
            self::$geoCodes->languages()->take(0)->first(),
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
        $array = self::$language->toArray();
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
        $flatten = self::$language->toFlatten();
        $this->assertIsArray($flatten, 'Not a valid Array');
    }

    /**
     * @test
     * @testdox Test the `->first()->toFlatten('_')` feature, using custom separator.
     * @depends testFirstFeature
     * @return void
     */
    public function testFirstToFlattenFeatureCustomSeparator(): void
    {
        $flatten = self::$language->toFlatten('_');
        $this->assertIsArray($flatten, 'Not a valid Array');
    }

    /**
     * @test
     * @testdox Test the `->count()` feature on the list of Languages.
     * @return void
     * @throws QueryException
     */
    public function testCountOfLanguages(): void
    {
        $languages = self::$geoCodes->languages();
        $count = $languages->count();
        $this->assertEquals(
            self::$languagesTotalCount,
            $count,
            "The TOTAL number of the language doesn't match with " . self::$languagesTotalCount
        );

        foreach ([(self::$languagesTotalCount - 21), 27, 5, 32, 0] as $numberOfItems) {
            $languages->offset(21)->limit($numberOfItems);
            $count = $languages->count();
            $this->assertEquals(
                $numberOfItems,
                $count,
                "The number of the language doesn't match with " . $numberOfItems
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
        $languages = self::$geoCodes->languages();

        // Invalid - `from` less than 0
        try {
            $languages->offset(-5)->limit(20);
            $this->fail('An invalid limit from has been accepted');
        } catch (QueryException $e) {
            $this->assertInstanceOf(QueryException::class, $e);
            $this->assertEquals(11004, $e->getCode());
        }

        // Invalid - `numberOfItems` less than 0
        try {
            $languages->offset(20)->limit(-5);
            $this->fail('An invalid limit numberOfItems has been accepted');
        } catch (QueryException $e) {
            $this->assertInstanceOf(QueryException::class, $e);
            $this->assertEquals(11003, $e->getCode());
        }

        // Valid input
        $languages->offset(22)->limit(2);
        $this->assertEquals(2, $languages->count());
        $get = $languages->get();
        $this->assertEquals(self::$expectedLimitTest[0], $get->{0}->isoCode);
        $this->assertEquals(self::$expectedLimitTest[1], $get->{1}->isoCode);

        // Alias input
        $languages->skip(22)->take(2);
        $this->assertEquals(2, $languages->count());
        $get = $languages->get();
        $this->assertEquals(self::$expectedLimitTest[0], $get->{0}->isoCode);
        $this->assertEquals(self::$expectedLimitTest[1], $get->{1}->isoCode);
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
        $languages = self::$geoCodes->useLanguage('en')->languages();
        $languages->orderBy('isoCode');
        $language = $languages->first();
        $this->assertEquals(self::$expectedOrderByTest['isoCode']['ASC'], $language->isoCode);
        $languages->orderBy('isoCode', 'desc');
        $language = $languages->first();
        $this->assertEquals(self::$expectedOrderByTest['isoCode']['DESC'], $language->isoCode);
    }
    /**
     * @dataProvider dataProviderIndexes
     * @testdox ==>  using $index as property
     * @throws QueryException
     */
    public function testOrderByWithDataProvider(string $index): void
    {
        $languages = self::$geoCodes->useLanguage('en')->languages();
        $asc = $languages->orderBy($index)->first();
        $this->assertEquals(self::$expectedOrderByTest[$index]['ASC'], $asc->{$index});
        $desc = $languages->orderBy($index, 'desc')->first();
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
        $languages = self::$geoCodes->languages();

        // Invalid - `property` not indexable
        try {
            $languages->orderBy('notIndexable');
            $this->fail('An invalid orderBy property has been accepted');
        } catch (QueryException $e) {
            $this->assertInstanceOf(QueryException::class, $e);
            $this->assertEquals(11005, $e->getCode());
            $this->assertEquals(1, preg_match('/"notIndexable"/', $e->getMessage()));
        }

        // Invalid - `orderType` invalid
        try {
            $languages->orderBy('isoCode', 'invalid');
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
        $indexes = self::$geoCodes->languages()->getIndexes();
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
            self::$geoCodes->languages()->withIndex($index)->offset(0)->limit(1)->get()->toArray() as $key => $language
        ) {
            $this->assertEquals($key, $language[$index]);
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
            self::$geoCodes->languages()->withIndex('invalidField');
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
        $selectFields = self::$geoCodes->languages()->selectableFields();
        $this->assertIsArray($selectFields);
        $languages = self::$geoCodes->languages()->get();
        foreach ($languages->collect() as $language) {
            foreach ($selectFields as $key => $description) {
                $prop = $key;
                $object = $language;
                if (preg_match('/\./', $prop)) {
                    list($prop0, $prop) = explode('.', $prop);
                    $object = $language->{$prop0};
                }

                // check the existence of the field
                $this->assertTrue(
                    property_exists($object, $prop),
                    'Key `' . $key . '` not present in the language object'
                );

                // check the type of the key
                if (preg_match('/\[(.*?)\]/', $description, $matches) === 1) {
                    $type = $matches[1];
                } else {
                    $this->fail('Description for key `' . $key . '` must contain a [type]');
                }
                if (strpos($type, '?') === 0) {
                    $type = substr($type, 1);
                    $assert = gettype($object->{$prop}) === $type || gettype($object->{$prop}) === 'NULL';
                } else {
                    $assert = gettype($object->{$prop}) === $type;
                }
                $this->assertTrue(
                    $assert,
                    'Key type `' . $key . '` for the language `' . $language->name .
                        '`does not match with the declared type'
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
        $language = self::$geoCodes->languages()->first();
        foreach ($selectFields as $key) {
            $prop = $key;
            $object = $language;
            if (preg_match('/\./', $prop)) {
                list($prop0, $prop) = explode('.', $prop);
                $object = $language->{$prop0};
            }
            // check the existence of the field
            $this->assertTrue(
                property_exists($object, $prop),
                'Key `' . $key . '` not present in the language object'
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
        $languages = self::$geoCodes->languages();
        $selectFields = array_keys($languages->selectableFields());
        $languages->select(...$selectFields);
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
        $languages = self::$geoCodes->languages();
        $selectFields = array_keys($languages->selectableFields());
        foreach ($selectFields as $key) {
            $languages->select($key);
            $languages->select($key); // test also the redundancy
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
        $languages = self::$geoCodes->languages();
        $languages->select($select);
        $language = $languages->first();
        if (preg_match('/\./', $select)) {
            list($prop0, $prop) = explode('.', $select);
            $language = $language->{$prop0};
        }
        $count = count(get_object_vars($language));
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
            self::$geoCodes->languages()->select('invalidField');
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
     * @testdox ==> using - as valid -  in input integer, string, array.
     * @return void
     * @throws QueryException
     */
    public function testFetchFeatureValidInput(): void
    {
        $cfr = [
            'ita' => [ 'isoCode' => 'ita' ],
            'gle' => [ 'isoCode' => 'gle' ],
            'deu' => [ 'isoCode' => 'deu' ],
            'fra' => [ 'isoCode' => 'fra' ]
        ];
        $languages = self::$geoCodes->languages();
        $languages->fetch('ita', 'gle', ['deu', 'fra']);
        $result = $languages->withIndex('isoCode')->select('isoCode')->get()->toArray();

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
        $languages = self::$geoCodes->languages();
        try {
            $languages->fetch('ita', 'gle', [['deu'], ['fra']]);
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
            'ita' => [ 'isoCode' => 'ita' ],
            'gle' => [ 'isoCode' => 'gle' ],
            'deu' => [ 'isoCode' => 'deu' ],
            'fra' => [ 'isoCode' => 'fra' ]
        ];
        $languages = self::$geoCodes->languages();
        $languages->fetch('ita', 'gle')->fetch(['deu', 'fra']);
        $result = $languages->withIndex('isoCode')->select('isoCode')->get()->toArray();
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
        $languages = self::$geoCodes->languages();
        $fetchAll = $languages->fetchAll()->get();
        $fetchStar = $languages->fetch('*')->get();
        $fetchWithStar = $languages->fetch('ita', 'gle', ['deu', '*'])->get();
        $this->assertEquals($fetchAll, self::$languagesSetsList);
        $this->assertEquals($fetchStar, self::$languagesSetsList);
        $this->assertEquals($fetchWithStar, self::$languagesSetsList);
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
            'ita' => [ 'isoCode' => 'ita' ],
            'gle' => [ 'isoCode' => 'gle' ],
            'deu' => [ 'isoCode' => 'deu' ],
            'fra' => [ 'isoCode' => 'fra' ]
        ];
        $languages = self::$geoCodes->languages();
        $languages->fetch('ITA', 'gle')->fetch(['deu', 'fra']);
        $languages->merge();
        $result = $languages->withIndex('isoCode')->select('isoCode')->get()->toArray();
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
            'ita' => [ 'isoCode' => 'ita' ],
            'gle' => [ 'isoCode' => 'gle' ],
            'deu' => [ 'isoCode' => 'deu' ],
            'fra' => [ 'isoCode' => 'fra' ]
        ];
        $languages = self::$geoCodes->languages();
        $languages->fetch('ita');
        $languages->fetch('GLE');
        $languages->merge();
        $languages->fetch(['deu', 'fra']);
        $languages->merge();
        $languages->merge();
        $result = $languages->withIndex('isoCode')->select('isoCode')->get()->toArray();
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
            'ita' => [ 'isoCode' => 'ita' ]
        ];
        $languages = self::$geoCodes->languages();
        $languages->fetch('ita', 'gle', 'deu')->fetch(['ita', 'fra']);
        $languages->intersect();
        $result = $languages->withIndex('isoCode')->select('isoCode')->get()->toArray();
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
            'ita' => [ 'isoCode' => 'ita' ]
        ];
        $languages = self::$geoCodes->languages();
        $languages->fetch('ita', 'gle', 'esp')->fetch(['ITA', 'deu']);
        $languages->intersect();
        $languages->fetch(['ita', 'fra']);
        $languages->fetch(['ita']);
        $languages->intersect();
        $languages->intersect();
        $result = $languages->withIndex('isoCode')->select('isoCode')->get()->toArray();
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
        $languages = self::$geoCodes->languages();
        $languages->fetch('ita', 'esp', 'deu');
        try {
            $languages->intersect();
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
            'gle' => [ 'isoCode' => 'gle' ],
            'deu' => [ 'isoCode' => 'deu' ],
            'fra' => [ 'isoCode' => 'fra' ]
        ];
        $languages = self::$geoCodes->languages();
        $languages->fetch('ita', 'gle', 'deu')->fetch(['ita', 'fra']);
        $languages->complement();
        $result = $languages->withIndex('isoCode')->select('isoCode')->get()->toArray();
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
            'ita' => [ 'isoCode' => 'ita' ],
            'gle' => [ 'isoCode' => 'gle' ]
        ];
        $languages = self::$geoCodes->languages();
        $languages->fetch('gle', 'ita')->fetch(['gle']);
        $languages->complement();
        $languages->fetch('gle', 'ita')->fetch(['ita']);
        $languages->complement();
        $languages->complement();
        $result = $languages->withIndex('isoCode')->select('isoCode')->get()->toArray();
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
        $languages = self::$geoCodes->languages();
        $languages->fetch('ita', 'deu', 'gle');
        try {
            $languages->complement();
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
        $languages = self::$geoCodes->languages();
        try {
            $languages->where(...$args);
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
            $languages->orWhere(...$args);
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
                "'zzz'",
                ['zzz'],
                11011
            ],
            [
                "'zzz', '5', 'aam', 'aam'",
                ['zzz', '5', 'aam', 'aam'],
                11010
            ],
            [
                "['field', 'operator', 'term'], ['field', 'operator', 'term']",
                [['field', 'operator', 'term'], ['field', 'operator', 'term']],
                11010
            ],
            [
                "5, '5', 'aam'",
                [5, '5', 'aam'],
                11010
            ],
            [
                "['5'], '5', 'aam'",
                [['5'], '5', 'aam'],
                11010
            ],
            [
                "[5], ['5'], ['aam']",
                [[5], ['5'], ['aam']],
                11010
            ],
            [
                "['5'], ['5'], ['aam']",
                [['5'], ['5'], ['aam']],
                11010
            ],
            [
                "true, '5', 'aam'",
                [true, '5', 'aam'],
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
                "'isocOde', 'ita'",
                ['isocOde', 'ita'],
                11015,
                ['isocOde']
            ],
            [
                "'isoCode.inexistent', 'ita'",
                ['isoCode.inexistent', 'ita'],
                11015,
                ['isoCode.inexistent']
            ],
            [
                "['isoCode.inexistent', 'ita']",
                [['isoCode.inexistent', 'ita']],
                11015,
                ['isoCode.inexistent']
            ],
            [
                "[['isoCode.inexistent', 'ita']]",
                [[['isoCode.inexistent', 'ita']]],
                11015,
                ['isoCode.inexistent']
            ],
            [
                "['isoCode', '=', ['ita', 'gle']]]",
                [['isoCode', '=', ['ita', 'gle']]],
                11014,
                ['=']
            ],
            [
                "[['isoCode', 'IN', 'ita']]",
                [[['isoCode', 'IN', 'ita']]],
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
        $languages = self::$geoCodes->languages();
        $languages->$method(...$args);
        $result = $languages->withIndex('isoCode')->select('isoCode')->get()->toArray();
        $this->assertEquals($matches, $result);
    }
    /**
     * @return array<int,
     *     array<int, array<int|string, array<int|string, array<int, int|string>|string>|int|string>|string>>
     */
    public function dataProviderValidConditions(): array
    {
        return [
            [
                "'isoCode', 'ita'",
                ['isoCode', 'ita'],
                'where',
                ['ita' => [ 'isoCode' => 'ita' ]]
            ],
            [
                "'part1', 'it'",
                ['part1', 'it'],
                'where',
                ['ita' => [ 'isoCode' => 'ita' ]]
            ],
            [
                "'glottoCode', 'ital1282'",
                ['glottoCode', 'ital1282'],
                'where',
                ['ita' => [ 'isoCode' => 'ita' ]]
            ],
            [
                "'isoCode', '=', 'ita'",
                ['isoCode', '=', 'ita'],
                'where',
                ['ita' => [ 'isoCode' => 'ita' ]]
            ],
            [
                "['isoCode', '=', 'ita']",
                [['isoCode', '=', 'ita']],
                'where',
                ['ita' => [ 'isoCode' => 'ita' ]]
            ],
            [
                "[['isoCode', '=', 'ita']]",
                [[['isoCode', '=', 'ita']]],
                'where',
                ['ita' => [ 'isoCode' => 'ita' ]]
            ],
            [
                "[['isoCode', '=', 'ita'], ['isoCode', '=', 'gle']]",
                [[['isoCode', '=', 'ita'], ['isoCode', '=', 'gle']]],
                'where',
                []
            ],
            [
                "[['isoCode', '=', 'ita'], ['glottoCode', '=', 'ital1282']]",
                [[['isoCode', '=', 'ita'], ['glottoCode', '=', 'ital1282']]],
                'where',
                ['ita' => [ 'isoCode' => 'ita' ]]
            ],
//            [
//                "'name', 'like', 'Netherlands%'",
//                ['name', 'like', 'Netherlands%'],
//                'where',
//                ['ANG' => [ 'isoCode' => 'ANG' ]]
//            ],
//            [
//                "'name', 'like', '%Netherlands%'",
//                ['name', 'like', '%Netherlands%'],
//                'where',
//                ['ANG' => [ 'isoCode' => 'ANG' ]]
//            ],
//            [
//                "'name', 'like', '%Guilder'",
//                ['name', 'like', '%Guilder'],
//                'where',
//                ['ANG' => [ 'isoCode' => 'ANG' ], 'AWG' => [ 'isoCode' => 'AWG' ]]
//            ],
//            [
//                "'name', 'like', '%Guilder%'",
//                ['name', 'like', '%Guilder%'],
//                'where',
//                ['ANG' => [ 'isoCode' => 'ANG' ], 'AWG' => [ 'isoCode' => 'AWG' ]]
//            ],
//            [
//                "'name', 'like', '%ntillea%'",
//                ['name', 'like', '%ntillea%'],
//                'where',
//                ['ANG' => [ 'isoCode' => 'ANG' ]]
//            ],
//            [
//                "[['name', 'like', '%Euro'], ['name', 'not like', '%WIR%']]",
//                [[['name', 'like', '%Euro'], ['name', 'not like', '%WIR%']]],
//                'where',
//                ['ita' => [ 'isoCode' => 'ita' ]]
//            ],
//            [
//                "[['decimal', '<=', 2], ['isoNumber', '978']]",
//                [[['decimal', '<=', 2], ['isoNumber', '978']]],
//                'where',
//                ['EUR' => [ 'isoCode' => 'EUR' ]]
//            ],
//            [
//                "[['decimal', '>=', '2'], ['decimal', '<', '3'], ['isoNumber', '978']]",
//                [[['decimal', '>=', '2'], ['decimal', '<', '3'], ['isoNumber', '978']]],
//                'where',
//                ['EUR' => [ 'isoCode' => 'EUR' ]]
//            ],
//            [
//                "[['decimal', '<', 3], ['isoNumber', '978']]",
//                [[['decimal', '<', 3], ['isoNumber', '978']]],
//                'where',
//                ['EUR' => [ 'isoCode' => 'EUR' ]]
//            ],
//            [
//                "[['decimal', '>', '1'], ['decimal', '<', '3'], ['isoNumber', '978']]",
//                [[['decimal', '>', '1'], ['decimal', '<', '3'], ['isoNumber', '978']]],
//                'where',
//                ['EUR' => [ 'isoCode' => 'EUR' ]]
//            ],
//            [
//                "[['symbol', 'is NOT null'], ['isoNumber', 978]]",
//                [[['symbol', 'is NOT null'], ['isoNumber', 978]]],
//                'where',
//                ['EUR' => [ 'isoCode' => 'EUR' ]]
//            ],
//            [
//                "[['symbol', 'is null'], ['isoNumber', 646]]",
//                [[['symbol', 'is null'], ['isoNumber', 646]]],
//                'where',
//                ['RWF' => [ 'isoCode' => 'RWF' ]]
//            ],
        ];
    }
}
