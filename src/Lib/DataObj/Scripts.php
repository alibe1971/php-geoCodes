<?php

namespace Alibe\GeoCodes\Lib\DataObj;

use Alibe\GeoCodes\Lib\DataObj\Elements\GeoSet;
use Alibe\GeoCodes\Lib\DataObj\Elements\Script;

class Scripts extends BaseDataObj implements \JsonSerializable
{
    /**
     * @var string
     */
    protected string $xmlRootElement = "scripts";

    /**
     * @return  array<string, array<string, array<string, array<string, array<string, string>|string>>|string>>
     */
    protected function getXmlMap(): array
    {
        return [
            'scripts' =>  array_merge(
                [
                    "@tag" => "script",
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
        return [ [Script::class] ];
    }

    /**
     * @return array<string>
     */
    public function jsonSerialize(): array
    {
        $result = [];

        foreach (get_object_vars($this) as $key => $script) {
            if ($key === 'xmlRootElement') {
                continue;
            }

            if ($script instanceof \JsonSerializable) {
                $result[$key] = $script->jsonSerialize();
            } elseif (is_array($script)) {
                $result[$key] = array_map(function ($item) {
                    if ($item instanceof \JsonSerializable) {
                        return $item->jsonSerialize();
                    }
                    return $item;
                }, $script);
            } else {
                $result[$key] = $script;
            }
        }

        return $result;
    }
}
