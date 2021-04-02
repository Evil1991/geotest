<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(count($arResult['CITY_LIST'])>0)
{
?>
<div id="geocity_wrap">
	<div id="geocity_text">
		Ваш город:<div id="geocity_text_value" data-id="<?=$arResult['ID']?>"><?=$arResult['CITY'] ?></div> 
	</div>
	<div id="geocity_popup">
		Ваш город: <?=$arResult['CITY'] ?>
		<div>
			<span id="geocity_yes">Да</span>
			<span id="geocity_no">Нет</span>
		</div>
	</div>
</div>

<div id="geocity_popup_window">
	<div id="geocity_popup_window_content">
		Выберите город
		<select id="realcity">
			<?php
				foreach($arResult['CITY_LIST'] as $city)
				{
					?><option value="<?=$city['ID']?>" id="realcity_<?=$city['ID']?>"><?=$city['NAME']?></option><?
				}
			?>
		</select>
		<button id="realcity_agree">Выбрать</button>
	</div>
</div>
<?
}
?>