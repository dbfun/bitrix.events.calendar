<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php
$monthClasses = array('previous' => 'day--previous-month', 'current' => 'day--current-month', 'next' => 'day--next-month'); 
$isMonthEvents = count($arResult->monthEvents) > 0;
?>
<table class="js-calendar app-calendar" 
  data-iblocks="<?=implode(',', (array)$arParams['IBLOCK_ID']);?>" 
  data-showdow="<?php echo $arParams['SHOW_DOW']; ?>"
  data-urltemplate="<?php echo $arParams['URL_TEMPLATES_DETAIL']; ?>"
  data-requestyear="<?php echo $arResult->calendarDate->requestYear; ?>" 
  data-requestmonth="<?php echo $arResult->calendarDate->requestMonth; ?>"
  data-curyear="<?php echo $arResult->calendarDate->curYear; ?>"
  data-curmonth="<?php echo $arResult->calendarDate->curMonth; ?>"
  data-curday="<?php echo $arResult->calendarDate->curDay; ?>">

<tr>
  <th colspan="7">
    <div class="calendar-caption">
      <span class="js-calendar-arrow calendar-arrow-previous" data-direction="prev"><i></i></span>
      <span class="js-calendar-arrow calendar-arrow-next" data-direction="next"><i></i></span>
      <span<?php if($isMonthEvents) { ?> class="js-cal-month-tip"<?php } ?>><?php echo $arResult->currentmonthName; ?>, <?php echo $arResult->calendarDate->requestYear; ?></span>
    </div>
  </th>
</tr>

<?php if($arParams['SHOW_DOW'] == 'Y') { ?>
<tr>
  <td class="calendar-dow calendar-dow--1">Пн</td>
  <td class="calendar-dow calendar-dow--2">Вт</td>
  <td class="calendar-dow calendar-dow--3">Ср</td>
  <td class="calendar-dow calendar-dow--4">Чт</td>
  <td class="calendar-dow calendar-dow--5">Пт</td>
  <td class="calendar-dow calendar-dow--6">Сб</td>
  <td class="calendar-dow calendar-dow--7">Вс</td>
</tr>
<?php } ?>

<tr>
<td class="calendar-box" colspan="7">
  <table class="calendar-dates">
    <tr>
<?php 
foreach ($arResult->calendarTable as $id => $oneDay) {
  $classFfx = array();
  if ($id <= 6) $classFfx[] = 'first-week';
  elseif ($id >= count($arResult->calendarTable) - 7) $classFfx[] = 'last-week';
  if($oneDay['is_event']) $classFfx[] = 'day--event';
  $classFfx[] = 'day--'.($id%7);
  $classFfx[] = $monthClasses[$oneDay['type']];
  if($oneDay['is_today']) $classFfx[] = 'day--today';
  $isHint = isset($oneDay['hints']);
  if($isHint) {
    $classFfx[] = $oneDay['hints']->classSfx;
    $hintTitle = $oneDay['hints']->title;
  }
  
  $isEvent = $oneDay['is_event'];
  $isImage = $isEvent && isset($oneDay['pic']) && !empty($oneDay['pic']);
  
  if($isImage) {
    $renderImage = BitrixAdapterThumbnail::fit(CFile::GetFileArray($oneDay['pic']), 351, 228);
  }

  if ($isEvent) $link = $arResult->getDayUrl($arParams['URL_TEMPLATES_DETAIL'], $arResult->calendarDate->requestYear, $arResult->calendarDate->requestMonth, $oneDay['day']);
  ?>
  <td class="calendar-day <?=implode(' ', $classFfx);?>">
    <?php if($isEvent) { ?><a class="day-link<?=$isImage ? ' js-cal-tip' : null; ?>" href="<?php echo $link; ?>"<?php if ($isImage) { ?> data-dow="<?=($id%7);?>"data-image="<?=$renderImage;?>"<?php } ?>><?php } ?>
      <div class="day-cell"><?php echo $oneDay['day']; ?><?php if($isEvent) { ?><i class="day-info"></i><?php } ?><?php if($isHint) { ?><i class="day-hint" title="<?php echo $hintTitle; ?>"></i><?php } ?></div>
    <?php if($isEvent) { ?></a><?php } ?>
  </td>

  <?php
  if ($id!=0 && ($id + 1)!=count($arResult->calendarTable) && ($id+1) % 7 == 0) echo '</tr><tr>';
  if (($id + 1) == count($arResult->calendarTable)) echo '</tr>';
  
  } ?>
  </table>
</td>
</tr>
</table>

<?php if($isMonthEvents) { ?>
<div class="js-cal-m-events app-hidden">
  <table class="dates">
  <?php foreach($arResult->monthEvents as $day => $events) { ?>
    <?php foreach($events as $ev) {
      $evShort = BitrixAdapterLib::cutString($ev['name'], 80);
      if ($day != 100) {
        $dayText = '<div class="round">'.$day.'</div>';
      } else {
        $dayText = '&nbsp;';
      }
      ?>
      <tr><td class="day"><?=$dayText;?></td><td class="name"><?=$evShort;?></td></tr>
    <?php } ?>
  <?php } ?>
  </table>
</div>
<?php } ?>