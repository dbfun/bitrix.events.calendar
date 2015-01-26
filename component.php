<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
require_once('BAEventsEngine.php');
$engine = new BAEventsEngine($arParams);
$arResult = $engine->execute();
$this->IncludeComponentTemplate();