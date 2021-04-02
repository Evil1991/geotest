<?php

class geotest extends CModule
{
    var $MODULE_ID = "geotest";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;

    function geotest()
    {
        $arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path . "/version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }

        $this->MODULE_NAME = "Geotest – модуль определение города по IP адресу пользователя";
        $this->MODULE_DESCRIPTION = "модуль определение города по IP адресу пользователя";
    }

    function InstallFiles()
    {
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/local/modules/geotest/install/components",
            $_SERVER["DOCUMENT_ROOT"] . "/local/components", true, true);

        CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/local/modules/geotest/install/api",
            $_SERVER["DOCUMENT_ROOT"] . "/", true, true);
        return true;
    }

    function UnInstallFiles()
    {
        DeleteDirFilesEx("/local/components/altopromo");
        DeleteDirFilesEx("/local/components/api/getCity");
        return true;
    }

    function InstallModuleTable()
    {
        $geoid = $this->createTable('GeoUser');
        $this->addHBlockString($geoid, 'IP');
        $this->addHBlockString($geoid, 'CITY_ID');

        $cityid = $this->createTable('CityList');
        $this->addHBlockString($cityid, 'CITY');
    }

    private function createTable($tablename)
    {
        \Bitrix\Main\Loader::IncludeModule('highloadblock');

        $code = strtolower($tablename);

        $result = \Bitrix\Highloadblock\HighloadBlockTable::add(array(
            'NAME' => $tablename,
            'TABLE_NAME' => $code,
        ));

        if ($result->isSuccess()) {
            $tableid = $result->getId();
            COption::SetOptionInt("altopromo", $code . '_id', $tableid);
        } else {
            $errors = implode('/', $result->getErrorMessages());
            $e = new Exception($errors);
            throw $e;
        }

        return ($tableid);
    }


    private function addHBlockString($hblockID, $fieldName)
    {
        $entityID = 'HLBLOCK_' . $hblockID;

        $userTypeEntity = new CUserTypeEntity();
        $userTypeData = array(
            'ENTITY_ID' => $entityID,
            'FIELD_NAME' => 'UF_' . $fieldName,
            'USER_TYPE_ID' => 'string',
            'XML_ID' => 'XML_ID_' . $fieldName,
            'SORT' => 100,
            'MULTIPLE' => 'N',
            'MANDATORY' => 'Y',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => '',
            'EDIT_IN_LIST' => '',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' => array(
                'DEFAULT_VALUE' => '',
                'SIZE' => '20',
                'ROWS' => '1',
                'MIN_LENGTH' => '0',
                'MAX_LENGTH' => '0',
                'REGEXP' => ''
            )
        );

        $userTypeId = $userTypeEntity->Add($userTypeData);
    }


    function UnInstallModuleTable()
    {
        \Bitrix\Main\Loader::IncludeModule('highloadblock');

        $tableid = COption::GetOptionInt("altopromo", "geouser_id");
        \Bitrix\Highloadblock\HighloadBlockTable::delete($tableid);
        COption::RemoveOption("altopromo", "geouser_id");

        $tableid = COption::GetOptionInt("altopromo", "citylist_id");
        \Bitrix\Highloadblock\HighloadBlockTable::delete($tableid);
        COption::RemoveOption("altopromo", "citylist_id");
    }

    function DoInstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;
        $this->InstallFiles();
        $this->installModuleTable();
        RegisterModule("geotest");
        $APPLICATION->IncludeAdminFile("Установка модуля geotest", $DOCUMENT_ROOT . "/local/modules/geotest/install/step.php");
    }

    function DoUninstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;
        $this->UnInstallFiles();
        $this->UnInstallModuleTable();

        UnRegisterModule("geotest");
        $APPLICATION->IncludeAdminFile("Деинсталляция модуля geotest", $DOCUMENT_ROOT . "/local/modules/geotest/install/unstep.php");
    }
}

?>