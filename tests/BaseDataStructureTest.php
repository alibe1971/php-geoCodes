<?php

namespace Alibe\GeoCodes\Tests;

use Alibe\GeoCodes\Lib\DataSets;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;

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

    /**
     * @var array<string, mixed>
     */
    private static array $Config = [];


    /**
     * @var array<string, mixed>
     */
    private static array $geocodeDataCtrl = [
        'countries' => [
            'alpha2' => [],
            'alpha3' => [],
            'unM49' => [],
            'officialName' => [],
            'languages' => [],
        ],
        'currencies' => [
            'isoAlpha' => [],
            'isoNumber' => []
        ],
        'geoSets' => [
            'internalCode' => [],
            'unM49' => []
        ],
        'languages' => [
            'isoCode' => [],
            'part2b' => [],
            'part2t' => [],
            'part1' => [],
        ],
        'translations' => []
    ];

    private static array $geocodeTranslationsProperties = [
        'countries' => [
            'name' => [],
            'fullName' => [],
        ],
        'geosets' => [
            'name' => []
        ],
        'currencies' => [
            'name' => []
        ],
        'languages' => [
            'name' => []
        ]
    ];

    private static array $geocodeCategories = [
        'languages' => [
            'scope' => [ 'I', 'M', 'S' ],
            'type' => [ 'A', 'C', 'E', 'H', 'L', 'S' ]
        ]
    ];

    public static function setUpBeforeClass(): void
    {
        self::$dataDir = dirname(__DIR__) . '/src/Data';
    }


    /**
     * @test
     * @return void
     */
    public function testDataStructureExists(): void
    {
        $finder = new Finder();
        $finder->files()->in(self::$dataDir)->ignoreDotFiles(true);
        $dataFiles = [];
        foreach ($finder as $file) {
            $dataFiles[] = 1;
        }
        $this->assertNotEmpty($dataFiles);
    }

    /**
     * @test
     * @depends testDataStructureExists
     * @return void
     */
    public function testValidationConfigFile(): void
    {

        $config = self::$dataDir . '/config.php';

        $this->assertFileExists($config, 'The config file is missing');
        $config = include($config);

        $this->assertNotEmpty($config);

        $this->assertArrayHasKey(
            'settings',
            $config,
            'The section `settings` is not present in the config file'
        );
        $this->assertNotEmpty($config['settings'], 'The section `settings` is empty');

        $this->assertArrayHasKey(
            'languages',
            $config['settings'],
            'The section `languages` is not present inside the `settings`'
        );
        $this->assertNotEmpty($config['settings']['languages'], 'The section `languages` is empty');

        $this->assertArrayHasKey(
            'default',
            $config['settings']['languages'],
            'The property `default` is not present inside the `languages`'
        );
        $this->assertNotEmpty(
            $config['settings']['languages']['default'],
            'The property `default` is empty'
        );
        self::$defaultLanguage = $config['settings']['languages']['default'];


        $this->assertArrayHasKey(
            'inPackage',
            $config['settings']['languages'],
            'The property `inPackage` is not present inside the `languages`'
        );
        $this->assertIsArray(
            $config['settings']['languages']['inPackage'],
            'The property `inPackage` is not an array'
        );
        $this->assertNotEmpty(
            $config['settings']['languages']['inPackage'],
            'The property `inPackage` is empty'
        );
        $this->assertContains(
            self::$defaultLanguage,
            $config['settings']['languages']['inPackage'],
            'The default language `'
            . self::$defaultLanguage .
            '` is not present inside the configuration set of the `languages` packages'
        );
        self::$Config = $config;
    }

    public function testValidationLanguagesData(): void
    {
        $languages = self::$dataDir . '/languages.php';

        $this->assertFileExists($languages, 'The `languages` file is missing');
        $languages = require_once $languages;

        $this->assertNotEmpty($languages);

        $isoCode2Values = array_column($languages, 'isoCode');

        foreach ($languages as $idx => $lng) {
            /** isoCode */
            $this->assertArrayHasKey(
                'isoCode',
                $lng,
                'The property `isoCode` is not present inside the `languages` data ' .
                'for the index `' . $idx . '`'
            );
            $this->assertNotContains(
                $lng['isoCode'],
                self::$geocodeDataCtrl['languages']['isoCode'],
                'The language with `isoCode` `'
                . $lng['isoCode'] .
                '` is a duplicated key in the `languages` data'
            );
            $this->assertMatchesRegularExpression(
                '/^[a-z]{3}$/',
                $lng['isoCode'],
                'The language code `isoCode` `'
                . $lng['isoCode'] .
                '` must be three characters (lowercase) long.'
            );
            self::$geocodeDataCtrl['languages']['isoCode'][] = $lng['isoCode'];

            /** part2b */
            $this->assertArrayHasKey(
                'part2b',
                $lng,
                'The property `part2b` is not present inside the `languages` data ' .
                'for the isoCode `' . $lng['isoCode'] . '`'
            );
            $this->assertNotContains(
                $lng['part2b'],
                self::$geocodeDataCtrl['languages']['part2b'],
                'The language with `isoCode` `'
                . $lng['isoCode'] .
                '` has a duplicated key `part2b` for value `' . $lng['part2b'] . '` in the `languages` data'
            );
            $is_string = is_string($lng['part2b']);
            $this->assertTrue(
                $is_string || is_null($lng['part2b']),
                'The language property `part2b` ' .
                ' for the isoCode `' . $lng['isoCode'] . '` must be "string" or "null" ' .
                '(`' . gettype($lng['part2b']) . '` returned)'
            );
            if ($is_string) {
                $this->assertMatchesRegularExpression(
                    '/^[a-z]{3}$/',
                    $lng['part2b'],
                    'The language code `part2b` `'
                    . $lng['part2b'] .
                    '` must be three characters (lowercase) long.'
                );
                self::$geocodeDataCtrl['languages']['part2b'][] = $lng['part2b'];
            }

            /** part2t */
            $this->assertArrayHasKey(
                'part2t',
                $lng,
                'The property `part2t` is not present inside the `languages` data ' .
                'for the isoCode `' . $lng['isoCode'] . '`'
            );
            $this->assertNotContains(
                $lng['part2t'],
                self::$geocodeDataCtrl['languages']['part2t'],
                'The language with `isoCode` `'
                . $lng['isoCode'] .
                '` has a duplicated key `part2t` for value `' . $lng['part2t'] . '` in the `languages` data'
            );
            $is_string = is_string($lng['part2t']);
            $this->assertTrue(
                $is_string || is_null($lng['part2t']),
                'The language property `part2t` ' .
                ' for the isoCode `' . $lng['isoCode'] . '` must be "string" or "null" ' .
                '(`' . gettype($lng['part2t']) . '` returned)'
            );
            if ($is_string) {
                $this->assertMatchesRegularExpression(
                    '/^[a-z]{3}$/',
                    $lng['part2t'],
                    'The language code `part2t` `'
                    . $lng['part2t'] .
                    '` must be three characters (lowercase) long.'
                );
                self::$geocodeDataCtrl['languages']['part2t'][] = $lng['part2t'];
            }

            /** part1 */
            $this->assertArrayHasKey(
                'part1',
                $lng,
                'The property `part1` is not present inside the `languages` data ' .
                'for the isoCode `' . $lng['isoCode'] . '`'
            );
            $this->assertNotContains(
                $lng['part1'],
                self::$geocodeDataCtrl['languages']['part1'],
                'The language with `isoCode` `'
                . $lng['isoCode'] .
                '` has a duplicated key `part1` for value `' . $lng['part1'] . '` in the `languages` data'
            );
            $is_string = is_string($lng['part1']);
            $this->assertTrue(
                $is_string || is_null($lng['part1']),
                'The language property `part1` ' .
                ' for the isoCode `' . $lng['isoCode'] . '` must be "string" or "null" ' .
                '(`' . gettype($lng['part1']) . '` returned)'
            );
            if ($is_string) {
                $this->assertMatchesRegularExpression(
                    '/^[a-z]{2}$/',
                    $lng['part1'],
                    'The language code `part1` `'
                    . $lng['part1'] .
                    '` must be two characters (lowercase) long.'
                );
                self::$geocodeDataCtrl['languages']['part1'][] = $lng['part1'];
            }

            /** glottoCode */
            $this->assertArrayHasKey(
                'glottoCode',
                $lng,
                'The property `glottoCode` is not present inside the `languages` data ' .
                'for the isoCode `' . $lng['isoCode'] . '`'
            );
            $is_string = is_string($lng['glottoCode']);
            $this->assertTrue(
                $is_string || is_null($lng['glottoCode']),
                'The language property `glottoCode` ' .
                ' for the isoCode `' . $lng['isoCode'] . '` must be "string" or "null" ' .
                '(`' . gettype($lng['glottoCode']) . '` returned)'
            );
            if ($is_string) {
                $this->assertMatchesRegularExpression(
                    '/^[a-z]{4}\d{4}$/',
                    $lng['glottoCode'],
                    'The language code `glottoCode` `'
                    . $lng['glottoCode'] .
                    '` must have 4 characters (lowercase) length and 4 numbers length.'
                );
            }

            /** scope */
            $this->assertArrayHasKey(
                'scope',
                $lng,
                'The property `scope` is not present inside the `languages` data ' .
                'for the isoCode `' . $lng['isoCode'] . '`'
            );
            $this->assertContains(
                $lng['scope'],
                self::$geocodeCategories['languages']['scope'],
                'The language with `isoCode` `'
                . $lng['isoCode'] .
                '` must have as property `scope` with one of these values: [`' .
                implode('`, `', self::$geocodeCategories['languages']['scope']) .
                '`]. `' .
                $lng['scope'] .
                '` returned in the `languages` data'
            );

            /** type */
            $this->assertArrayHasKey(
                'type',
                $lng,
                'The property `type` is not present inside the `languages` data ' .
                'for the isoCode `' . $lng['isoCode'] . '`'
            );
            $this->assertContains(
                $lng['type'],
                self::$geocodeCategories['languages']['type'],
                'The language with `isoCode` `'
                . $lng['isoCode'] .
                '` must have as property `type` with one of these values: [`' .
                implode('`, `', self::$geocodeCategories['languages']['type']) .
                '`]. `' .
                $lng['type'] .
                '` returned in the `languages` data'
            );

            /** macroLanguageRef */
            $this->assertArrayHasKey(
                'macroLanguageRef',
                $lng,
                'The property `macroLanguageRef` is not present inside the `languages` data ' .
                'for the isoCode `' . $lng['isoCode'] . '`'
            );
            $is_string = is_string($lng['macroLanguageRef']);
            $this->assertTrue(
                $is_string || is_null($lng['macroLanguageRef']),
                'The language property `macroLanguageRef` ' .
                ' for the isoCode `' . $lng['isoCode'] . '` must be "string" or "null" ' .
                '(`' . gettype($lng['macroLanguageRef']) . '` returned)'
            );
            if ($is_string) {
                $this->assertNotEmpty(
                    trim(preg_replace('/\s+/u', '', $lng['macroLanguageRef'])),
                    'The language property `macroLanguageRef` cannot be an empty string ' .
                    'for the isoCode `' . $lng['isoCode'] . '` in `languages` data.'
                );
                $this->assertContains(
                    $lng['macroLanguageRef'],
                    $isoCode2Values,
                    'macroLanguageRef `' . $lng['macroLanguageRef'] . '` does not match any existing `isoCode` ' .
                    ' in `languages` data.'
                );
            }

            /** scripts [TODO]*/
        }
    }

    /**
     * @test
     * @depends testValidationConfigFile
     * @return void
     */
    public function testValidationCurrencyData(): void
    {
        $currencies = self::$dataDir . '/currencies.php';

        $this->assertFileExists($currencies, 'The `currencies` file is missing');
        $currencies = require_once $currencies;

        $this->assertNotEmpty($currencies);

        foreach ($currencies as $idx => $cur) {
            /** isoAlpha */
            $this->assertArrayHasKey(
                'isoAlpha',
                $cur,
                'The property `isoAlpha` is not present inside the `currencies` data ' .
                'for the index `' . $idx . '`'
            );
            $this->assertNotContains(
                $cur['isoAlpha'],
                self::$geocodeDataCtrl['currencies']['isoAlpha'],
                'The currency with `isoAlpha` `'
                . $cur['isoAlpha'] .
                '` is a duplicated key in the `currencies` data'
            );
            $this->assertMatchesRegularExpression(
                '/^[A-Z]{3}$/',
                $cur['isoAlpha'],
                'The currency code `isoAlpha` `'
                . $cur['isoAlpha'] .
                '` must be three characters long.'
            );
            self::$geocodeDataCtrl['currencies']['isoAlpha'][] = $cur['isoAlpha'];

            /** isoNumber */
            $this->assertArrayHasKey(
                'isoNumber',
                $cur,
                'The property `isoNumber` is not present inside the `currencies` data ' .
                'for the isoAlpha `' . $cur['isoAlpha'] . '`'
            );
            $this->assertNotContains(
                $cur['isoNumber'],
                self::$geocodeDataCtrl['currencies']['isoNumber'],
                'The currency with `isoNumber` `'
                . $cur['isoAlpha'] .
                '` is a duplicated key in the `currencies` data'
            );
            $this->assertMatchesRegularExpression(
                '/^\d\d\d$/',
                $cur['isoNumber'],
                'The currency `isoNumber` `'
                . $cur['isoNumber'] .
                '` must be three digits long.'
            );
            self::$geocodeDataCtrl['currencies']['isoNumber'][] = $cur['isoNumber'];

            /** symbol */
            $this->assertArrayHasKey(
                'symbol',
                $cur,
                'The property `symbol` is not present inside the `currencies` data ' .
                'for the isoAlpha `' . $cur['isoAlpha'] . '`'
            );
            $is_string = is_string($cur['symbol']);
            $this->assertTrue(
                $is_string || is_null($cur['symbol']),
                'The currency property `symbol` ' .
                ' for the isoAlpha `' . $cur['isoAlpha'] . '` must be "string" or "null" ' .
                '(`' . gettype($cur['symbol']) . '` returned)'
            );
            if ($is_string) {
                $this->assertNotEmpty(
                    trim(preg_replace('/\s+/u', '', $cur['symbol'])),
                    'The currency property `symbol` cannot be an empty string ' .
                    'for the isoAlpha `' . $cur['isoAlpha'] . '`'
                );
            }

            /** decimal */
            $this->assertArrayHasKey(
                'decimal',
                $cur,
                'The property `decimal` is not present inside the `currencies` data ' .
                'for the isoAlpha `' . $cur['isoAlpha'] . '`'
            );
            $is_int = is_int($cur['decimal']);
            $this->assertTrue(
                $is_int || is_null($cur['decimal']),
                'The currency property `decimal` ' .
                ' for the isoAlpha `' . $cur['isoAlpha'] . '` must be "integer" or "null" ' .
                '(`' . gettype($cur['decimal']) . '` returned)'
            );
        }
    }

    /**
     * @test
     * @depends testValidationConfigFile
     * @return void
     */
    public function testValidationGeoSetsData(): void
    {
        $geosets = self::$dataDir . '/geoSets.php';

        $this->assertFileExists($geosets, 'The `geoSets` file is missing');
        $geosets = require_once $geosets;

        $this->assertNotEmpty($geosets);

        $aq = false;
        $geo = [];
        $geoLv0 = [];
        $geoLv1 = [
            'AQ'        // Exception for Antartica (AQ - 010) that is also a continent
        ];

        foreach ($geosets as $idx => $gs) {

            /** internalCode */
            $this->assertArrayHasKey(
                'internalCode',
                $gs,
                'The property `internalCode` is not present inside the `geoSets` data ' .
                'for the index `' . $idx . '`'
            );
            $this->assertNotContains(
                $gs['internalCode'],
                self::$geocodeDataCtrl['geoSets']['internalCode'],
                'The geoSets with `internalCode` `'
                . $gs['internalCode'] .
                '` is a duplicated key in the `geoSets` data'
            );
            $this->assertMatchesRegularExpression(
                '/^[A-Z0-9-]+$/',
                $gs['internalCode'],
                'The geoSets code `internalCode` `'
                . $gs['internalCode'] .
                '` must contains only uppercase characters, numbers and hyphens'
            );
            self::$geocodeDataCtrl['geoSets']['internalCode'][] = $gs['internalCode'];

            /** unM49 */
            $this->assertArrayHasKey(
                'unM49',
                $gs,
                'The property `unM49` is not present inside the `geoSets` data ' .
                'for the internalCode `' . $gs['internalCode'] . '`'
            );

            /** tags */
            $this->assertArrayHasKey(
                'tags',
                $gs,
                'The property `tags` is not present inside the `geoSets` data ' .
                'for the internalCode `' . $gs['internalCode'] . '`'
            );
            $this->assertIsArray(
                $gs['tags'],
                'The property `tags` is not an array in the `geoSets` data ' .
                'for the internalCode `' . $gs['internalCode'] . '`'
            );
            $this->assertNotEmpty(
                $gs['tags'],
                'The property `tags` is an empty array in the `geoSets` data ' .
                'for the internalCode `' . $gs['internalCode'] . '`'
            );

            /** countryCodes */
            $this->assertArrayHasKey(
                'countryCodes',
                $gs,
                'The property `countryCodes` is not present inside the `geoSets` data ' .
                'for the internalCode `' . $gs['internalCode'] . '`'
            );
            $this->assertIsArray(
                $gs['countryCodes'],
                'The property `countryCodes` is not an array in the `geoSets` data ' .
                'for the internalCode `' . $gs['internalCode'] . '`'
            );
            $this->assertNotEmpty(
                $gs['countryCodes'],
                'The property `countryCodes` is an empty array in the `geoSets` data ' .
                'for the internalCode `' . $gs['internalCode'] . '`'
            );

            /**
             * Check for Country Codes inside the `GEOG-` internal code
             */
            if (preg_match('/^GEOG-/', $gs['internalCode'])) {
                $gArr = explode('-', $gs['internalCode']);
                $Lv = count($gArr) - 2;
                array_pop($gArr);
                $parent = implode('-', $gArr);
                if (!array_key_exists($parent, $geo)) {
                    $geo[$parent] = [];
                }
                foreach ($gs['countryCodes'] as $cc) {
                    if ($Lv != 0) {
                        $this->assertContains(
                            $cc,
                            $geo[$parent],
                            'Inside the country set in the geoSets data the value `'
                            . $cc .
                            '` in ' . $gs['internalCode'] .
                            ' has no correspondence in the parent group `' . $parent . '`'
                        );
                    }
                    if ($Lv < 2) {
                        $this->assertNotContains(
                            $cc,
                            ${'geoLv' . $Lv},
                            'Inside the country set in the geoSets data the value `'
                            . $cc .
                            '` in ' . $gs['internalCode'] . ' is a duplicated key, because already present ' .
                            'used in this or in another ' . $Lv . ' region'
                        );
                        $geo[$gs['internalCode']][] = $cc;
                        array_push(${'geoLv' . $Lv}, $cc);
                    }
                }
                $this->assertNotContains(
                    $gs['unM49'],
                    self::$geocodeDataCtrl['geoSets']['unM49'],
                    'The geoSets with `unM49` `'
                    . $gs['internalCode'] .
                    '` is a duplicated key in the `geoSets` data'
                );
                $this->assertMatchesRegularExpression(
                    '/^[0-9]+$/',
                    $gs['unM49'],
                    'The geoSets code `unM49` `'
                    . $gs['internalCode'] .
                    '` must be three digits long if it is a geographical item'
                );
                self::$geocodeDataCtrl['geoSets']['unM49'][] = $gs['unM49'];
                if ($gs['internalCode'] == 'GEOG-AQ') {  // Exception for Antarctica (AQ - 010) that is also a continent
                    $aq = true;
                }
            } else {
                $this->assertNull(
                    $gs['unM49'],
                    'The geoSets code `unM49` `'
                    . $gs['internalCode'] .
                    '` must be null if it is a not geographical item'
                );
            }
        }

        $this->assertTrue($aq, 'It seems someone destroyed the continent of Antarctica');

        $diff = array_diff($geoLv0, $geoLv1);
        $this->assertEmpty(
            $diff,
            'The following country codes are present in the grouped level 0 geo-region, ' .
            'but not in the grouped level 1 geo-region, ' .
            '[' . implode(', ', $diff) . ']'
        );

        $diff = array_diff($geoLv1, $geoLv0);
        $this->assertEmpty(
            $diff,
            'The following country codes are present in the grouped level 1 geo-region, ' .
            'but not in the grouped level 0 geo-region, ' .
            '[' . implode(', ', $diff) . ']'
        );
    }


    /**
     * @test
     * @depends testValidationCurrencyData
     * @depends testValidationGeoSetsData
     * @return void
     */
    public function testValidationCountryData(): void
    {
        $countries = self::$dataDir . '/countries.php';

        $this->assertFileExists($countries, 'The `countries` file is missing');
        $countries = require_once $countries;

        $this->assertNotEmpty($countries);

        $alpha2Values = array_column($countries, 'alpha2');

        foreach ($countries as $idx => $cc) {
            /** alpha2 */
            $this->assertArrayHasKey(
                'alpha2',
                $cc,
                'The property `alpha2` is not present inside the `countries` data ' .
                'for the index `' . $idx . '`'
            );
            $this->assertNotContains(
                $cc['alpha2'],
                self::$geocodeDataCtrl['countries']['alpha2'],
                'The country code with `alpha2` `'
                . $cc['alpha2'] .
                '` is a duplicated key in the `countries` data'
            );
            $this->assertMatchesRegularExpression(
                '/^[A-Z]{2}$/',
                $cc['alpha2'],
                'The country code `alpha2` `'
                . $cc['alpha2'] .
                '` must be two [UPPERCASE] characters long.'
            );
            self::$geocodeDataCtrl['countries']['alpha2'][] = $cc['alpha2'];

            /** alpha3 */
            $this->assertArrayHasKey(
                'alpha3',
                $cc,
                'The property `alpha3` is not present inside the `countries` data ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertNotContains(
                $cc['alpha3'],
                self::$geocodeDataCtrl['countries']['alpha3'],
                'The country code with `alpha3` `'
                . $cc['alpha3'] .
                '` is a duplicated key in the `countries` data'
            );
            $this->assertMatchesRegularExpression(
                '/^[A-Z]{3}$/',
                $cc['alpha3'],
                'The country code `alpha3` `'
                . $cc['alpha3'] .
                '` must be three [UPPERCASE] characters long.'
            );
            self::$geocodeDataCtrl['countries']['alpha3'][] = $cc['alpha3'];

            /** unM49 */
            $this->assertArrayHasKey(
                'unM49',
                $cc,
                'The property `unM49` is not present inside the `countries` data ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertNotContains(
                $cc['unM49'],
                self::$geocodeDataCtrl['countries']['unM49'],
                'The country code with `unM49` `'
                . $cc['unM49'] .
                '` is a duplicated key in the group of `countries` and `geoSets` data'
            );
            $this->assertMatchesRegularExpression(
                '/^\d\d\d$/',
                $cc['unM49'],
                'The country code `unM49` `'
                . $cc['unM49'] .
                '` must be three digits long.'
            );
            self::$geocodeDataCtrl['countries']['unM49'][] = $cc['unM49'];

            /** flags */
            $this->assertArrayHasKey(
                'flags',
                $cc,
                'The property `flags` is not present inside the `countries` data ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertIsArray(
                $cc['flags'],
                'The property `flags` is not an array ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertNotEmpty(
                $cc['flags'],
                'The property `flags` is empty ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            /** flags.emoji */
            $this->assertArrayHasKey(
                'emoji',
                $cc['flags'],
                'The property `flags.emoji` is not present inside the `countries` data ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertIsString(
                $cc['flags']['emoji'],
                'The property `flags.emoji` is not a string inside the `countries` data ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertMatchesRegularExpression(
                '/^\p{Regional_Indicator}{2}$/u',
                $cc['flags']['emoji'],
                'The property `flags.emoji` is not a Regional Indicator Symbols string inside the ' .
                '`countries` data  for the alpha2 `' . $cc['alpha2'] . '`'
            );
            /** flags.svg */
            $this->assertArrayHasKey(
                'svg',
                $cc['flags'],
                'The property `flags.svg` is not present inside the `countries` data ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertIsString(
                $cc['flags']['svg'],
                'The property `flags.svg` is not a string inside the `countries` data ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertTrue(
                DataSets::isValidSVG($cc['flags']['svg']),
                'The property `flags.svg` is not a valid SVG for the `countries` data ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );

            /** dependency */
            $this->assertArrayHasKey(
                'dependency',
                $cc,
                'The property `dependency` is not present inside the `countries` data ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $is_string = is_string($cc['dependency']);
            $this->assertTrue(
                $is_string || is_null($cc['dependency']),
                'The country property `dependency` ' .
                ' for the alpha2 `' . $cc['alpha2'] . '` must be "string" or "null" ' .
                '(`' . gettype($cc['dependency']) . '` returned)'
            );
            if ($is_string) {
                $this->assertNotEmpty(
                    trim(preg_replace('/\s+/u', '', $cc['dependency'])),
                    'The country property `dependency` cannot be an empty string ' .
                    'for the alpha2 `' . $cc['alpha2'] . '`'
                );
                $this->assertContains(
                    $cc['dependency'],
                    $alpha2Values,
                    'Dependency `' . $cc['dependency'] . '` does not match any existing `alpha2`'
                );
            }

            /** officialName */
            $this->assertArrayHasKey(
                'officialName',
                $cc,
                'The property `officialName` is not present inside the `countries` data ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertIsArray(
                $cc['officialName'],
                'The property `officialName` is not an array ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            foreach ($cc['officialName'] as $ln => $name) {
                if (!array_key_exists($ln, self::$geocodeDataCtrl['countries']['officialName'])) {
                    self::$geocodeDataCtrl['countries']['officialName'][$ln] = [];
                }
                $this->assertIsString(
                    $name,
                    'The country property `officialName` must have elements as string ' .
                    'for the alpha2 `' . $cc['alpha2'] . '`'
                );
                $this->assertNotEmpty(
                    trim(preg_replace('/\s+/u', '', $name)),
                    'The country property `officialName` cannot have elements as empty string ' .
                    'for the alpha2 `' . $cc['alpha2'] . '`'
                );
                $this->assertNotContains(
                    $name,
                    self::$geocodeDataCtrl['countries']['officialName'][$ln],
                    'The country code with `officialName` ' . $name . ' for the language `'
                    . $ln .
                    '` is a duplicated key in the `countries` data with the alpha2 `' . $cc['alpha2'] . '`'
                );
                self::$geocodeDataCtrl['countries']['officialName'][$ln][] = $name;
                self::$geocodeDataCtrl['countries']['languages'][] = $ln;
            }

            /** mottos */
            $this->assertArrayHasKey(
                'mottos',
                $cc,
                'The property `mottos` is not present inside the `countries` data ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertIsArray(
                $cc['mottos'],
                'The property `mottos` is not an array ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            if (!empty($cc['mottos'])) {
                foreach ($cc['mottos'] as $key => $mottoGr) {
                    $this->assertIsArray(
                        $mottoGr,
                        'The property `mottos.' . $key  . '` is not an array ' .
                        'for the alpha2 `' . $cc['alpha2'] . '`'
                    );
                    if (!empty($cc['mottos'][$key])) {
                        foreach ($cc['mottos'][$key] as $idx => $mottoKeyGr) {
                            $this->assertIsArray(
                                $mottoKeyGr,
                                'The property `mottos.' . $key  . '.' . $idx . '` is not an array ' .
                                'for the alpha2 `' . $cc['alpha2'] . '`'
                            );
                            $this->assertArrayHasKey(
                                'text',
                                $mottoKeyGr,
                                'The property `mottos.' . $key  . '.' . $idx . '` has not the `text` key ' .
                                'for the alpha2 `' . $cc['alpha2'] . '`'
                            );
                            $this->assertNotEmpty(
                                $mottoKeyGr['text'],
                                'The property `mottos.' . $key  . '.' . $idx . '` has the `text` array as ' .
                                'empty for the alpha2 `' . $cc['alpha2'] . '`'
                            );
                            foreach ($mottoKeyGr['text'] as $ln => $motto) {
                                $this->assertIsString(
                                    $motto,
                                    'The property `mottos.' . $key  . '.' . $idx . '.text.' . $ln . '` must ' .
                                    'be a string for the alpha2 `' . $cc['alpha2'] . '`'
                                );
                                $this->assertNotEmpty(
                                    trim(preg_replace('/\s+/u', '', $motto)),
                                    'The property `mottos.' . $key  . '.' . $idx . '.text.' . $ln . '` ' .
                                    'cannot be an empty string for the alpha2 `' . $cc['alpha2'] . '`'
                                );
                                self::$geocodeDataCtrl['countries']['languages'][] = $ln;
                            }
                        }
                    }
                }
            }

            /** currencies */
            $this->assertArrayHasKey(
                'currencies',
                $cc,
                'The property `currencies` is not present inside the `countries` data ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertIsArray(
                $cc['currencies'],
                'The property `currencies` is not an array ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertNotEmpty(
                $cc['currencies'],
                'The property `currencies` is empty ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            /** currencies.legalTenders */
            $this->assertArrayHasKey(
                'legalTenders',
                $cc['currencies'],
                'The property `currencies.legalTenders` is not present inside the `countries` data ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertIsArray(
                $cc['currencies']['legalTenders'],
                'The property `currencies.legalTenders` is not an array ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $curInTender = [];
            if (!empty($cc['currencies']['legalTenders'])) {
                foreach ($cc['currencies']['legalTenders'] as $cur) {
                    $curInTender[] = $cur;
                    $this->assertContains(
                        $cur,
                        self::$geocodeDataCtrl['currencies']['isoAlpha'],
                        'The property `currencies.legalTenders` with value `'
                        . $cur .
                        '` for the alpha2 `' . $cc['alpha2'] . '` ' .
                        'is not in the list of the ISO currencies'
                    );
                }
            }
            /** currencies.widelyAccepted */
            $this->assertArrayHasKey(
                'widelyAccepted',
                $cc['currencies'],
                'The property `currencies.widelyAccepted` is not present inside the `countries` data ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertIsArray(
                $cc['currencies']['widelyAccepted'],
                'The property `currencies.widelyAccepted` is not an array ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            if (!empty($cc['currencies']['widelyAccepted'])) {
                foreach ($cc['currencies']['widelyAccepted'] as $cur) {
                    $this->assertNotContains(
                        $cur,
                        $curInTender,
                        'The property `currencies.widelyAccepted` with value `'
                        . $cur .
                        '` already exits in `currencies.legalTenders` for the alpha2 `' . $cc['alpha2'] . '`'
                    );
                    $this->assertContains(
                        $cur,
                        self::$geocodeDataCtrl['currencies']['isoAlpha'],
                        'The property `currencies.widelyAccepted` with value `'
                        . $cur .
                        '` for the alpha2 `' . $cc['alpha2'] . '` ' .
                        'is not in the list of the ISO currencies'
                    );
                }
            }

            /** dialCodes */
            $this->assertArrayHasKey(
                'dialCodes',
                $cc,
                'The property `dialCodes` is not present inside the `countries` data ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertIsArray(
                $cc['dialCodes'],
                'The property `dialCodes` is not an array ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertNotEmpty(
                $cc['dialCodes'],
                'The property `dialCodes` is empty ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            /** dialCodes sub properties */
            foreach (['deJure', 'deFacto', 'exceptions'] as $dialCodesKey) {
                $this->assertArrayHasKey(
                    $dialCodesKey,
                    $cc['dialCodes'],
                    'The property `dialCodes.' . $dialCodesKey . '` is not present inside the `countries` ' .
                    'data for the alpha2 `' . $cc['alpha2'] . '`'
                );
                $this->assertIsArray(
                    $cc['dialCodes'][$dialCodesKey],
                    'The property `dialCodes.' . $dialCodesKey . '` is not an array ' .
                    'for the alpha2 `' . $cc['alpha2'] . '`'
                );
                if ($dialCodesKey == 'exceptions') {
                } else {
                    foreach ($cc['dialCodes'][$dialCodesKey] as $dial) {
                        $this->assertMatchesRegularExpression(
                            '/^\+\d+$/',
                            $dial,
                            'The property `dialCodes.main`=' . $dial . ' has wrong format ' .
                            'for the alpha2 `' . $cc['alpha2'] . '`'
                        );
                    }
                }
            }

            /** ccTld */
            $this->assertArrayHasKey(
                'ccTld',
                $cc,
                'The property `ccTld` is not present inside the `countries` data ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $is_string = is_string($cc['ccTld']);
            $this->assertTrue(
                $is_string || is_null($cc['ccTld']),
                'The country property `ccTld` ' .
                ' for the alpha2 `' . $cc['alpha2'] . '` must be "string" or "null" ' .
                '(`' . gettype($cc['ccTld']) . '` returned)'
            );
            if ($is_string) {
                $this->assertNotEmpty(
                    trim(preg_replace('/\s+/u', '', $cc['ccTld'])),
                    'The country property `ccTld` cannot be an empty string ' .
                    'for the alpha2 `' . $cc['alpha2'] . '`'
                );
            }

            /** timeZones */
            $this->assertArrayHasKey(
                'timeZones',
                $cc,
                'The property `timeZones` is not present inside the `countries` data ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertIsArray(
                $cc['timeZones'],
                'The property `timeZones` is not an array ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertNotEmpty(
                $cc['timeZones'],
                'The property `timeZones` is empty ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );

            /** localesIcu */
            $this->assertArrayHasKey(
                'localesIcu',
                $cc,
                'The property `localesIcu` is not present inside the `countries` data ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertIsArray(
                $cc['localesIcu'],
                'The property `localesIcu` is not an array ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $locs = [];
            foreach ($cc['localesIcu'] as $loc) {
                $this->assertNotContains(
                    $loc,
                    $locs,
                    'The property `localesIcu` with value `'
                    . $loc .
                    '` already exits (duplicated) in `localesIcu` for the alpha2 `' . $cc['alpha2'] . '`'
                );
                self::$geocodeDataCtrl['countries']['languages'][] = $loc;
                $locs[] = $loc;
            }

            /** otherAppsIds */
            $this->assertArrayHasKey(
                'otherAppsIds',
                $cc,
                'The property `otherAppsIds` is not present inside the `countries` data ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertIsArray(
                $cc['otherAppsIds'],
                'The property `otherAppsIds` is not an array ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertNotEmpty(
                $cc['otherAppsIds'],
                'The property `otherAppsIds` is empty ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            /** otherAppsIds.geoNamesOrg */
            $this->assertArrayHasKey(
                'geoNamesOrg',
                $cc['otherAppsIds'],
                'The property `otherAppsIds.geoNamesOrg` is not present inside the `countries` data ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $is_int = is_int($cc['otherAppsIds']['geoNamesOrg']);
            $this->assertTrue(
                $is_int || is_null($cc['otherAppsIds']['geoNamesOrg']),
                'The country property `otherAppsIds.geoNamesOrg` ' .
                ' for the alpha2 `' . $cc['alpha2'] . '` must be "string" or "null" ' .
                '(`' . gettype($cc['otherAppsIds']['geoNamesOrg']) . '` returned)'
            );
            if ($is_int) {
                $this->assertTrue(
                    $cc['otherAppsIds']['geoNamesOrg'] > 0,
                    'The country property `otherAppsIds.geoNamesOrg` cannot be zero ' .
                    'for the alpha2 `' . $cc['alpha2'] . '`'
                );
            }


            // [TODO] LANGUAGES
        }


        self::$geocodeDataCtrl['countries']['languages']
            = array_values(array_unique(self::$geocodeDataCtrl['countries']['languages']));
    }


    /** @test
     * @depends testValidationCountryData
     * @return void
     */
    public function testValidationTranslationsFiles(): void
    {
        $countries = [];
        $currencies = [];
        $geosets = [];
        $languages = [];
        foreach (self::$Config['settings']['languages']['inPackage'] as $lang) {
            $transDir = self::$dataDir . '/Translations/' . $lang . '/';
            self::$geocodeDataCtrl['translations']['countries'][$lang]
                = self::$geocodeTranslationsProperties['countries'];

            self::$geocodeDataCtrl['translations']['geosets'][$lang]
                = self::$geocodeTranslationsProperties['geosets'];

            self::$geocodeDataCtrl['translations']['currencies'][$lang]
                = self::$geocodeTranslationsProperties['currencies'];

            self::$geocodeDataCtrl['translations']['languages'][$lang]
                = self::$geocodeTranslationsProperties['languages'];

            foreach (array_keys(self::$geocodeDataCtrl['translations']) as $item) {
                $this->assertFileExists(
                    $transDir . $item . '.php',
                    'The translation ' . $item . ' file is missing'
                );
                ${$item} = require_once($transDir . $item . '.php');
            }

            if ($lang == self::$Config['settings']['languages']['default']) {
                foreach (self::$geocodeDataCtrl['countries']['alpha2'] as $cc) {
                    $this->assertArrayHasKey(
                        $cc,
                        $countries,
                        'The country code `' . $cc . '`does not exist in `translations.' .
                        $lang . '.countries` dataset'
                    );
                    $this->assertIsArray(
                        /** @phpstan-ignore-next-line */
                        $countries[$cc],
                        'The country code `' . $cc . '`is not an array in `translations.' .
                        $lang . '.countries` dataset'
                    );
                    $this->assertNotEmpty(
                        $countries[$cc],
                        'The country code `' . $cc . '`is empty in `translations.' .
                        $lang . '.countries` dataset'
                    );
                    $this->assertArrayHasKey(
                        'name',
                        $countries[$cc],
                        'The country code `' . $cc . '`has not the property `name` in `translations.' .
                        $lang . '.countries` dataset'
                    );
                    $this->assertNotContains(
                        $countries[$cc]['name'],
                        self::$geocodeDataCtrl['translations']['countries'][$lang]['name'],
                        'The country property `name` as "' . $countries[$cc]['name'] .
                        '" for the translation `' . $lang .
                        '` is a duplicated key in `alpha2` "' . $cc . '"'
                    );
                    self::$geocodeDataCtrl['translations']['countries'][$lang]['name'][] = $countries[$cc]['name'];
                    $this->assertArrayHasKey(
                        'fullName',
                        $countries[$cc],
                        'The country code `' . $cc . '` has the missing property ' .
                        '`fullName` in `translations.' . $lang . '.countries` dataset'
                    );
                    $this->assertNotContains(
                        $countries[$cc]['fullName'],
                        self::$geocodeDataCtrl['translations']['countries'][$lang]['fullName'],
                        'The country property `fullName` as "' . $countries[$cc]['fullName'] .
                        '" for the translation `' . $lang .
                        '` is a duplicated key in `alpha2` "' . $cc . '"'
                    );
                    self::$geocodeDataCtrl['translations']['countries'][$lang]['fullName'][] =
                        $countries[$cc]['fullName'];
                    $this->assertArrayHasKey(
                        'demonyms',
                        $countries[$cc],
                        'The country code `' . $cc . '` has the missing property `demonyms` in `translations.' .
                        $lang . '.countries` dataset'
                    );
                    $this->assertArrayHasKey(
                        'keywords',
                        $countries[$cc],
                        'The country code `' . $cc . '` has the missing property `keywords` in `translations.' .
                        $lang . '.countries` dataset'
                    );


                    $this->assertIsString(
                        $countries[$cc]['name'],
                        'The country code `' . $cc . '` property `name` must be string in `translations.' .
                        $lang . '.countries` dataset'
                    );
                    $this->assertNotEmpty(
                        trim(preg_replace('/\s+/u', '', $countries[$cc]['name'])),
                        'The country code `' . $cc . '` has the property `name` as empty in `translations.' .
                        $lang . '.countries` dataset'
                    );

                    $this->assertIsString(
                        $countries[$cc]['fullName'],
                        'The country code `' . $cc . '` property `fullName` must be string ' .
                        'in `translations.' . $lang . '.countries` dataset'
                    );
                    $this->assertNotEmpty(
                        trim(preg_replace('/\s+/u', '', $countries[$cc]['fullName'])),
                        'The country code `' . $cc . '` has the property `fullName` as ' .
                        'empty in `translations.' . $lang . '.countries` dataset'
                    );

                    $this->assertIsArray(
                        $countries[$cc]['demonyms'],
                        'The country code `' . $cc . '` property `demonyms` must be array ' .
                        'in `translations.' . $lang . '.countries` dataset'
                    );

                    $this->assertIsArray(
                        $countries[$cc]['keywords'],
                        'The country code `' . $cc . '` property `keywords` must be array ' .
                        'in `translations.' . $lang . '.countries` dataset'
                    );
                }

                foreach (self::$geocodeDataCtrl['currencies']['isoAlpha'] as $cur) {
                    $this->assertArrayHasKey(
                        $cur,
                        $currencies,
                        'The currency code `' . $cur . '`does not exist in `translations.' .
                        $lang . '.currencies` dataset'
                    );
                    /** @phpstan-ignore-next-line */
                    $currencyName = $currencies[$cur]['name'];
                    $this->assertIsString(
                        $currencyName,
                        'The currency code `' . $cur . '` must be string in `translations.' .
                        $lang . '.currencies` dataset'
                    );
                    $this->assertNotEmpty(
                        trim(preg_replace('/\s+/u', '', $currencyName)),
                        'The currency code `' . $cur . '` is empty in `translations.' .
                        $lang . '.currencies` dataset'
                    );
                    $this->assertNotContains(
                        $currencyName,
                        self::$geocodeDataCtrl['translations']['currencies'][$lang]['name'],
                        'The currencies property `name` as "' . $currencyName .
                        '" for the translation `' . $lang .
                        '` is a duplicated key in `isoAlpha` "' . $cur . '"'
                    );
                    self::$geocodeDataCtrl['translations']['currencies'][$lang]['name'][] = $currencyName;
                }


                foreach (self::$geocodeDataCtrl['geoSets']['internalCode'] as $gs) {
                    $this->assertArrayHasKey(
                        $gs,
                        $geosets,
                        'The geoSets internal code `' . $gs . '`does not exist in `translations.' .
                        $lang . '.geoSets` dataset'
                    );
                    /** @phpstan-ignore-next-line */
                    $geoSetName = $geosets[$gs]['name'];
                    $this->assertIsString(
                        $geoSetName,
                        'The geoSets internal code `' . $gs . '` must be string in `translations.' .
                        $lang . '.geoSets` dataset'
                    );
                    $this->assertNotEmpty(
                        trim(preg_replace('/\s+/u', '', $geoSetName)),
                        'The geoSets internal code `' . $gs . '` is empty in `translations.' .
                        $lang . '.geoSets` dataset'
                    );
                    $this->assertNotContains(
                        $geoSetName,
                        self::$geocodeDataCtrl['translations']['geosets'][$lang]['name'],
                        'The geoSets property `name` as "' . $geoSetName .
                        '" for the translation `' . $lang .
                        '` is a duplicated key in `internalCode` "' . $gs . '"'
                    );
                    self::$geocodeDataCtrl['translations']['geosets'][$lang]['name'][] = $geoSetName;
                }


//                foreach (self::$geocodeDataCtrl['ln'] as $ln) {
//                    $ln = explode('-', $ln)[0];
//                    $this->assertArrayHasKey(
//                        $ln,
//                        $languages,
//                        'The language internal code `' . $ln . '`does not exist in `translations.' .
//                        $lang . '.languages` dataset'
//                    );
//                    $this->assertIsString(
//                        /** @NOphpstan-ignore-next-line */
//                        $languages[$ln],
//                        'The language internal code `' . $ln . '` must be string in `translations.' .
//                        $lang . '.languages` dataset'
//                    );
//                    $this->assertNotEmpty(
//                        trim(preg_replace('/\s+/u', '', $languages[$ln])),
//                        'The language internal code `' . $ln . '` is empty in `translations.' .
//                        $lang . '.languages` dataset'
//                    );
//                }
            }
        }
    }
}
