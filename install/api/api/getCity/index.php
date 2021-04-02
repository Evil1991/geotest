<?php
define("STATISTIC_SKIP_ACTIVITY_CHECK", "true");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

\Bitrix\Main\Loader::IncludeModule('geotest');

$geotest = new \Altopromo\GeoTest();

$ip = $_REQUEST['ip'];

echo($geotest->getCityApi($ip));
die();
?>