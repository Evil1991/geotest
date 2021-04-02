<?php

namespace Altopromo;

class GeoTest
{
    private $citylistTable;
    private $geouserTable;

    public function __construct()
    {
        \Bitrix\Main\Loader::IncludeModule('highloadblock');

        $geouserTableID = \COption::GetOptionInt("altopromo", "geouser_id");
        $arHLBlock = \Bitrix\Highloadblock\HighloadBlockTable::getById($geouserTableID)->fetch();
        $obEntity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
        $this->geouserTable = $obEntity->getDataClass();


        $citylistTableID = \COption::GetOptionInt("altopromo", "citylist_id");
        $arHLBlock = \Bitrix\Highloadblock\HighloadBlockTable::getById($citylistTableID)->fetch();
        $obEntity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
        $this->citylistTable = $obEntity->getDataClass();

    }

    public function getCity($ip)
    {
        // Вызываем getList для Highload блока, установленного в __construct
        $rsData = $this->geouserTable::getList(
            array(
                'select' => array('ID', 'UF_CITY_ID'),
                'where' => Array('IP' => $ip)
            )
        );

        if ($arItem = $rsData->Fetch()) {
            $name = $this->getCityName($arItem['UF_CITY_ID']);
            $arItem['NAME'] = $name;
            return $arItem;
        }

        return [];
    }

    public function getCityApi($ip)
    {
        $result = ['success' => false];
        $city = $this->getCityApi($ip);

        if (count($city)) {
            $cityName = $this->getCityName($city['UF_CITY_ID']);
            if ($cityName) {
                $result = ['success' => true, 'city' => $cityName]
			}
        }

        return json_encode($result, true);
    }

    public function findCity()
    {
        $ip = $_SERVER['REMOTE_ADDR'];

        $city = $this->getCity($ip);
        if (count($city) == 0) {
            $rsData = $this->geouserTable::getList(
                array(
                    'select' => array('ID', 'UF_CITY'),
                    'order' => array('ID' => 'ASC'),
                    'limit' => 1
                )
            );

            if ($city = $rsData->Fetch()) {
                $name = $this->getCityName($city['UF_CITY_ID']);
                $city['NAME'] = $name;
                return $city;
            } else {
                return [];
            }
        }

        return $city;
    }

    public function setCity($ip, $cityID)
    {
        $data = array(
            "UF_IP" => $ip,
            "UF_CITY_ID" => $cityID,
        );

        $city = $this->getCity($ip);
        if (count($city) == 0) {
            $this->geouserTable::add($data);
        } else {
            $this->geouserTable::update($city['ID'], $data);
        }
    }

    private function getCityName($cityID)
    {
        $rsData = $this->citylistTable::getList(
            array(
                'select' => array('ID', 'UF_CITY'),
                'where' => Array('ID' => $cityID)
            )
        );

        if ($item = $rsData->Fetch()) {
            return $item['UF_CITY'];
        }

        return '';
    }

    public function getCityList()
    {
        $cityList = [];

        $rsData = $this->geouserTable::getList(
            array(
                'select' => array('ID', 'UF_CITY')
            )
        );

        while ($arItem = $rsData->Fetch()) {
            $cityList[] = ['ID' => $arItem['ID'], 'NAME' => $arItem['UF_CITY']];
        }

        return $cityList;
    }
}

?>