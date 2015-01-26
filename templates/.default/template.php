<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php if(!$arResult->isAjax) { ?>
  <div class="js-calendar-wrapper app-calendar-wrapper">
<?php } ?>

  <?php require('template-content.php'); ?>
  
<?php if(!$arResult->isAjax) { ?>
  </div>
<?php } ?>