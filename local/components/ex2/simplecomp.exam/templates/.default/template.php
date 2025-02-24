<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(isset($arResult['FILTER_LINK'])){
    echo GetMessage('FILTER').'<a href ="'.$arResult['FILTER_LINK'].'">'.$arResult['FILTER_LINK'].'</a>'.'<br/>';
}
echo '---'.'<br/><br/>';
echo '<b>'.GetMessage('CATALOG').'</b><br/>';
$this->AddEditAction('iblock_'.$arResult['IBLOCK_ID'], $arResult['ADD_ELEMENT_LINK'], CIBlock::GetArrayByID($arResult["IBLOCK_ID"], "ELEMENT_ADD"));
?>
<ul id="<?=$this->GetEditAreaId('iblock_'.$arResult['IBLOCK_ID']);?>">

<?
foreach($arResult['ITEMS'] as $newsId => $item){

$sect = '';
foreach($item['SECTIONS'] as $sectionId){
$sect .= ', '.$arResult['ALL_SECTIONS'][$sectionId]['NAME'];
}
?>

    
    <li><b><?=$item['NAME'];?></b><?echo ' - '. $item['ACTIVE_FROM'].'('.substr($sect, 2).')' ;?>
    <ul>
        <?foreach($item['PRODUCTS'] as $productId){

            $ermitageId = $newsId.'_'.$productId;
            $arProduct = $arResult['ALL_PRODUCTS'][$productId];
            $this->AddEditAction($ermitageId, $arProduct['EDIT_LINK'], CIBlock::GetArrayByID($arResult["IBLOCK_ID"], "ELEMENT_EDIT"));
            $this->AddDeleteAction($ermitageId, $arProduct['DELETE_LINK'], CIBlock::GetArrayByID($arResult["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));            
        
        
        ?>
        <li id="<?=$this->GetEditAreaId($ermitageId);?>"><?echo $arProduct['NAME'].' - '.$arProduct['PRICE'].' - '.$arProduct['MATERIAL'].' - '.$arProduct['ARTNUMBER'];?></li>
<? }?>

    </li>

</ul>
<?

}?>
</ul>