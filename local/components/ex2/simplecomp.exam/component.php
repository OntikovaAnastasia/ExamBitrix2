<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader,
    Bitrix\Iblock;

if(!Loader::includeModule("iblock"))
{
    ShowError(GetMessage("SIMPLECOMP_EXAM2_IBLOCK_MODULE_NONE"));
    return;
}

if($this->StartResultCache(false, [isset($_GET['F'])])){

    if(isset($_GET['F'])){
        $this->AbortResultCache();
    }

    if(!$iblockProd = (int)$arParams['PRODUCTS_IBLOCK_ID']){
        return false;
    }
    if(!$iblockNews = (int)$arParams['NEWS_IBLOCK_ID']){
        return false;
    }
    if(!$propCode = trim ($arParams['PROPERTY_CODE'])){
        return false;
    }
    $arResult['IBLOCK_ID'] = $iblockProd;
    $arAllSection = [];
    $arIdNews = [];
    $resSection = CIBlockSection::GetList(
        false,
        ['IBLOCK_ID' => $iblockProd, 'ACTIVE' => 'Y', '!'.$propCode => false],
        true,
        ['ID', 'NAME', $propCode]
    );
    while($arSection = $resSection->GetNext()){
        if($arSection['ELEMENT_CNT'] > 0){
            $arAllSection[$arSection['ID']] = [
            'NAME' => $arSection['NAME'],
            'NEWS' => $arSection[$propCode],
            ];
        }
        foreach($arSection[$propCode] as $newsId){
            if(!in_array($newsId, $arIdNews)){
                $arIdNews[] = $newsId;
            }
        }
    }
    $arAllNews = [];
    
    $resNews = CIBlockElement::GetList(
        false,
        ['IBLOCK_ID' => $iblockNews, 'ACTIVE' => 'Y', 'ID' => $arIdNews],
        false,
        false,
        ['ID', 'NAME', 'ACTIVE_FROM']
    );

    while($arNew = $resNews->GetNext()){
        $arAllNews[$arNew['ID']] = [
            'NAME' => $arNew['NAME'],
            'ACTIVE_FROM' => $arNew['ACTIVE_FROM'],
            'SECTIONS' => [],
            'PRODUCTS' => [],
        ];
    }
    
    $filter = ['IBLOCK_ID' => $iblockProd, 'ACTIVE' => 'Y', 'SECTION_ID' => array_keys($arAllSection)];
    if(isset($_GET['F'])){
        $filter[] = [
            'LOGIC' => 'OR',
            ['<=PROPERTY_PRICE' => 1700, 'PROPERTY_MATERIAL' => 'Дерево, ткань'],
            ['<PROPERTY_PRICE' => 1500, 'PROPERTY_MATERIAL' => 'Металл, пластик'],
        ];
    }

    $minPrice = 9999999999;
    $maxPrice = 0;
    $arAllProducts= [];
    $resProducts = CIBlockElement::GetList(
        false,
        $filter,
        false,
        false,
        ['ID', 'NAME', 'IBLOCK_SECTION_ID', 'PROPERTY_PRICE', 'PROPERTY_MATERIAL', 'PROPERTY_ARTNUMBER'],
    );
    
    while($arProduct = $resProducts->GetNext()){
        $prodId = $arProduct['ID'];

        $arButtons = CIBlock::GetPanelButtons(
            $iblockProd,
            $prodId,
            0,
            array("SECTION_BUTTONS" => false, "SESSID" => false)
        );
        
        $price = $arProduct['PROPERTY_PRICE_VALUE'];
        if($price < $minPrice){
            $minPrice = $price;
        }
        if($price > $maxPrice){
            $maxPrice = $price;
        }
        $arAllProducts[$prodId] = [
            'NAME' => $arProduct['NAME'],
            'PRICE' => $price,
            'MATERIAL' => $arProduct['PROPERTY_MATERIAL_VALUE'],
            'ARTNUMBER' => $arProduct['PROPERTY_ARTNUMBER_VALUE'],
            'EDIT_LINK' => $arButtons["edit"]["edit_element"]["ACTION_URL"] ?? '',
            'DELETE_LINK' => $arButtons["edit"]["delete_element"]["ACTION_URL"] ?? '',
        ];
        
        
        $IBLOCK_SECTION_ID = $arProduct['IBLOCK_SECTION_ID'];
        foreach($arAllSection[$IBLOCK_SECTION_ID]['NEWS'] as $newsId){
            /*$arAllNews[$newsId]['PRODUCTS'][] = $prodId;
        
            if(!in_array($IBLOCK_SECTION_ID, $arAllNews[$newsId]['SECTIONS'])){
                $arAllNews[$newsId]['SECTIONS'][] = $IBLOCK_SECTION_ID;
            }*/

            if(isset($arAllNews[$newsId])){
                $arAllNews[$newsId]['PRODUCTS'][] = $prodId;
                if(!in_array($IBLOCK_SECTION_ID, $arAllNews[$newsId]['SECTIONS'])){
                    $arAllNews[$newsId]['SECTIONS'][] = $IBLOCK_SECTION_ID;
                }
            }
        }
    };

    $arResult['ITEMS'] = $arAllNews;
    $arResult['ALL_PRODUCTS'] = $arAllProducts;
    $arResult['ALL_SECTIONS'] = $arAllSection;
    $arResult['COUNT_PRODUCTS'] = count($arAllProducts);


    if(!isset($_GET['F'])){
        $arResult['FILTER_LINK'] = $APPLICATION->GetCurPage().'?F=Y';
        
    }


    $arButtons = CIBlock::GetPanelButtons(
        $iblockProd,
        0,
        0,
        array("SECTION_BUTTONS"=>false, "SESSID"=>false)
    );
    $arResult["ADD_ELEMENT_LINK"] = $arButtons["edit"]["add_element"]["ACTION_URL"] ?? '';

    $res = CIBlock::GetByID($iblockProd);
    $ar_res = $res->GetNext();
    $type = $ar_res['IBLOCK_TYPE_ID'];

    $this->AddIncludeAreaIcon(
        array(
            'URL' => "/bitrix/admin/iblock_element_admin.php?IBLOCK_ID=$iblockProd&type=$type&lang=ru&find_el_y=Y&clear_filter=Y&apply_filter=Y",
            'TITLE' => GetMessage('IBLOCK_BUTTON_ADMIN'),
            "IN_PARAMS_MENU" => true,
        )
        
    );

    $arResult['MIN_PRICE'] = $minPrice;
    $arResult['MAX_PRICE'] = $maxPrice;

}

    $this->setResultCacheKeys(['COUNT_PRODUCTS', 'MIN_PRICE', 'MAX_PRICE']);
    $this->includeComponentTemplate();

$APPLICATION->SetTitle(GetMessage('SET_TITLE').$arResult['COUNT_PRODUCTS']);

?>