<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(isset($arResult['SPECIALDATE'])){
	$APPLICATION->SetPageProperty('specialdate', $arResult['SPECIALDATE']);
}
