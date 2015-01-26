<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arCurrentValues */

if(!CModule::IncludeModule("iblock"))
	return;
  
$arIBlocks=Array();
$db_iblock = CIBlock::GetList(Array("SORT"=>"ASC"), Array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));
while($arRes = $db_iblock->Fetch())
	$arIBlocks[$arRes["ID"]] = $arRes["NAME"];


$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		"IBLOCK_ID" => Array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("EVENTS_BLOCK_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"DEFAULT" => '={$_REQUEST["ID"]}',
			"ADDITIONAL_VALUES" => "Y",
			"REFRESH" => "N",
		),		
    "SHOW_DOW" => Array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("EVENTS_SHOW_DOW"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => 'N',
			"ADDITIONAL_VALUES" => "N",
			"REFRESH" => "N",
		),
    "URL_TEMPLATES_DETAIL" => Array(
			"PARENT" => "URL_TEMPLATES",
			"NAME" => GetMessage("EVENTS_URL_TEMPLATES_DETAIL"),
			"TYPE" => "STRING",
			"DEFAULT" => "detail.php?year=#year#&month=#month#&day=#day#"),
	),
);
?>
