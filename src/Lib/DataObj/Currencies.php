<?php

namespace Alibe\GeoCodes\Lib\DataObj;

use Alibe\GeoCodes\Lib\DataObj\Elements\Currency;

class Currencies extends BaseDataObj implements \JsonSerializable
{
    /**
     * @var string
     */
    protected string $xmlRootElement = "currencies";

    /**
     * @return array<string, array<string, array<string, array<string, array<string, string>|string>>|string>>
     */
    protected function getXmlMap(): array
    {
        return [
            'currencies' =>  array_merge(
                [
                    "@tag" => "currency",
                    "@attribute" => "index"
                ],
                (new Currency())->getXmlMap()
            )
        ];
    }

    /**
     * @return array<int, array<int, string>>
     */
    protected function getObjectStructureParser(): array
    {
        return [ [Currency::class] ];
    }


    /**
     * @return array<string>
     */
    public function jsonSerialize(): array
    {
        $result = [];

        foreach (get_object_vars($this) as $key => $currencies) {
            if ($key === 'xmlRootElement') {
                continue;
            }

            if ($currencies instanceof \JsonSerializable) {
                $result[$key] = $currencies->jsonSerialize();
            } elseif (is_array($currencies)) {
                $result[$key] = array_map(function ($item) {
                    if ($item instanceof \JsonSerializable) {
                        return $item->jsonSerialize();
                    }
                    return $item;
                }, $currencies);
            } else {
                $result[$key] = $currencies;
            }
        }

        return $result;
    }
}
