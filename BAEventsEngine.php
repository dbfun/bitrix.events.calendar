<?php
class BAEventsEngine {

  private $arParams, $config;
  public function __construct($arParams)
  {
    CModule::IncludeModule("iblock");
    $this->arParams = $arParams;
    require_once('BAMonthCalendar.php');
    require_once('BACalendarDate.php');

    $configFile = BFactory::getDir().'/config/calendar.json';
    if(file_exists($configFile)) {
      $this->config = json_decode(file_get_contents($configFile));
    }
  }

  public function execute()
  {
    $isAjax = isset($_REQUEST['ajax']) && $_REQUEST['ajax'];
    $calendarDate = new BACalendarDate();
    if ($isAjax)
    {
      $calendarDate->setRequestDate($_REQUEST['requestyear'], $_REQUEST['requestmonth'], $_REQUEST['direction']);
      $calendarDate->setCurDate($_REQUEST['curyear'], $_REQUEST['curmonth'], $_REQUEST['curday']);
    }
    else
    {

      $isArchive = isset($_REQUEST['year'], $_REQUEST['month'], $_REQUEST['day']);
      if ($isArchive) {
        $calendarDate->setRequestDate($_REQUEST['year'], $_REQUEST['month'], 'current');
        $calendarDate->setCurDate($_REQUEST['year'], $_REQUEST['month'], $_REQUEST['day']);
      }
      else {
        // TODO: hack, in 2015 revert it
        // see commit 3af92ae9fab5175ec1906afb1502a8e8c1bdcdae
        $date = new DateTime('now');
        $curDate = clone $date;
        if($date->format('Y') == 2014) $date->add(new DateInterval('P2D'));
        $calendarDate->setRequestDate($date->format('Y'), $date->format('n'), 'current');
        $calendarDate->setCurDate($curDate->format('Y'), $curDate->format('n'), $curDate->format('j'));
      }
    }

    list($eventDates, $monthEvents) = $this->getDates($calendarDate->requestYear, $calendarDate->requestMonth);
    $hintDates = $this->getHintDates($calendarDate->requestYear, $calendarDate->requestMonth);

    $calendar = new BAMonthCalendar($calendarDate, $isAjax);
    $calendar->prepareCalendarTable($eventDates, $hintDates);
    $calendar->setMonthEvents($monthEvents);
    return $calendar;
  }

  private $dateBegin, $dateEnd;
  private function calcStartEndDate($year, $month) {
    $this->dateBegin = new DateTime(sprintf('%1$04d-%2$02d-%3$02d 00:00:00', $year, $month, 1), new DateTimeZone('UTC'));
    $this->dateEnd = clone($this->dateBegin);
    $this->dateEnd->modify("+1 month -1 second");
    }

  private function getHintDates($year, $month) {
    if (!isset($this->config->dates->{$year}->{$month})) return array();
    $month = (array)$this->config->dates->{$year}->{$month};
    if(count($month) == 0) return array();
    $ret = array();
    foreach($month as $day => $data) {
      $ret[$day] = $data;
    }
    return $ret;
  }

  const MYSQL_DATETIME_FORMAT = 'Y-m-d H:i:s';
  public function getDates($year, $month) {
    global $DB;
    $dates = array();
    $monthDates = array();
    $this->calcStartEndDate($year, $month);
    $query = "SELECT EXTRACT(DAY FROM `ACTIVE_FROM`) AS `day`, `PREVIEW_PICTURE` as `pic`, `NAME` as `name`, `ACTIVE_TO` as `act_to`
              FROM `b_iblock_element`
              WHERE IBLOCK_ID IN (".implode(', ', (array)$this->arParams['IBLOCK_ID']).")
              AND ACTIVE = 'Y'
              AND (ACTIVE_FROM BETWEEN '".$this->dateBegin->format(self::MYSQL_DATETIME_FORMAT)."' AND '".$this->dateEnd->format(self::MYSQL_DATETIME_FORMAT)."')
              ORDER BY SORT";
    $CDatabaseRes = $DB->Query($query, false, '');
    if ($CDatabaseRes->SelectedRowsCount() > 0) {
      while ($result = $CDatabaseRes->Fetch()) {
        $day =& $dates[$result['day']];
        if(!isset($day)) $day = array();

        if(!isset($day['pic'])) {
          $day['pic'] = $result['pic'];
        }

        if($result['act_to'] == null) {
          $monthDates[100][] = $result;
        } else {
          $monthDates[$result['day']][] = $result;
        }

      }
      unset($day);
      ksort($monthDates);
    }
    return array($dates, $monthDates);
    }

}