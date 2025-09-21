<?php

namespace Alibe\GeoCodes\Lib\DataObj;

use Alibe\GeoCodes\Lib\DataObj\Elements\Language;

class Languages extends BaseDataObj implements \JsonSerializable
{
    /**
     * @var string
     */
    protected string $xmlRootElement = "languages";

    /**
     * @return array<string, array<string, array<string, array<string, array<string, string>|string>>|string>>
     */
    protected function getXmlMap(): array
    {
        return [
            'languages' =>  array_merge(
                [
                    "@tag" => "language",
                    "@attribute" => "index"
                ],
                (new Language())->getXmlMap()
            )
        ];
    }

    /**
     * @return array<int, array<int, string>>
     */
    protected function getObjectStructureParser(): array
    {
        return [ [Language::class] ];
    }


    /**
     * @return array<string>
     */
    public function jsonSerialize(): array
    {
        $result = [];

        foreach (get_object_vars($this) as $key => $languages) {
            if ($key === 'xmlRootElement') {
                continue;
            }

            if ($languages instanceof \JsonSerializable) {
                $result[$key] = $languages->jsonSerialize();
            } elseif (is_array($languages)) {
                $result[$key] = array_map(function ($item) {
                    if ($item instanceof \JsonSerializable) {
                        return $item->jsonSerialize();
                    }
                    return $item;
                }, $languages);
            } else {
                $result[$key] = $languages;
            }
        }

        return $result;
    }
}
