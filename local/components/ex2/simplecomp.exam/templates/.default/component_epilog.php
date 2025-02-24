<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$APPLICATION->SetPageProperty('h1', GetMessage('H1').$arResult['COUNT_PRODUCTS']);

$APPLICATION->SetPageProperty('simplecomp_exam' ,
'<div style="color:red; margin: 34px 15px 35px 15px">'.GetMessage('MIN_PRICE').$arResult['MIN_PRICE'].'<br/>'.GetMessage('MAX_PRICE').$arResult['MAX_PRICE'].'</div>');
