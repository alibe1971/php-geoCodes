<?php

namespace Alibe\GeoCodes\Lib\DataObj;

use Alibe\GeoCodes\Lib\DataObj\Elements\GeoSet;

class GeoSets extends BaseDataObj implements \JsonSerializable
{
    /**
     * @var string
     */
    protected string $xmlRootElement = "geoSets";

    /**
     * @return  array<string, array<string, array<string, array<string, array<string, string>|string>>|string>>
     */
    protected function getXmlMap(): array
    {
        return [
            'geoSets' =>  array_merge(
                [
                    "@tag" => "geoSet",
                    "@attribute" => "index"
                ],
                (new GeoSet())->getXmlMap()
            )
        ];
    }
    /**
     * @return array<int, array<int, string>>
     */
    protected function getObjectStructureParser(): array
    {
        return [ [GeoSet::class] ];
    }

    /**
     * @return array<string>
     */
    public function jsonSerialize(): array
    {
        $result = [];

        foreach (get_object_vars($this) as $key => $geoSet) {
            if ($key === 'xmlRootElement') {
                continue;
            }

            if ($geoSet instanceof \JsonSerializable) {
                $result[$key] = $geoSet->jsonSerialize();
            } elseif (is_array($geoSet)) {
                $result[$key] = array_map(function ($item) {
                    if ($item instanceof \JsonSerializable) {
                        return $item->jsonSerialize();
                    }
                    return $item;
                }, $geoSet);
            } else {
                $result[$key] = $geoSet;
            }
        }

        return $result;
    }
}
