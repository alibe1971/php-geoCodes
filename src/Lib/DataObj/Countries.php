<?php

namespace Alibe\GeoCodes\Lib\DataObj;

use Alibe\GeoCodes\Lib\DataObj\Elements\Country;

class Countries extends BaseDataObj implements \JsonSerializable
{
    /**
     * @var string
     */
    protected string $xmlRootElement = "countries";

    /**
     * @phpstan-return array<
     *     string,
     *     array<
     *         string,
     *         array<
     *             string,
     *             array<
     *                 string,
     *                 array<
     *                     string,
     *                     array<string, string> | string
     *                 > | string
     *             >
     *         > | string
     *     >
     * >
     */

    protected function getXmlMap(): array
    {
        return [
            'countries' =>  array_merge(
                [
                    "@tag" => "country",
                    "@attribute" => "index"
                ],
                (new Country())->getXmlMap()
            )
        ];
    }

    /**
     * @return array<int, array<int, string>>
     */
    protected function getObjectStructureParser(): array
    {
        return [ [Country::class] ];
    }

    /**
     * @return array<string>
     */
    public function jsonSerialize(): array
    {
        $result = [];

        foreach (get_object_vars($this) as $key => $country) {
            if ($key === 'xmlRootElement') {
                continue;
            }

            if ($country instanceof \JsonSerializable) {
                $result[$key] = $country->jsonSerialize();
            } elseif (is_array($country)) {
                $result[$key] = array_map(function ($item) {
                    if ($item instanceof \JsonSerializable) {
                        return $item->jsonSerialize();
                    }
                    return $item;
                }, $country);
            } else {
                $result[$key] = $country;
            }
        }

        return $result;
    }
}
