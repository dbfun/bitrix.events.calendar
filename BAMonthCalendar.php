<?php
class BAMonthCalendar {

  private $calendarDate, $firstDay, $numDaysInMonth, $lastDay, $previousMonth, $numDaysInPreviousMonth, 
    $firstDayOfTheWeekInMonth, $lastDayOfTheWeekInMonth, $calendarTable;
  public function __construct(BACalendarDate $calendarDate, $isAjax) {
    $this->calendarDate = $calendarDate; 
    $this->isAjax = $isAjax;
    
    $this->firstDay = mktime(0, 0, 0, $this->calendarDate->requestMonth, 1, $this->calendarDate->requestYear); // первый день UNIX time
    $this->numDaysInMonth = date("t", $this->firstDay); // число дней в месяце
    $this->lastDay = mktime(0, 0, 0, $this->numDaysInMonth, 1, $this->calendarDate->requestYear); // последний день UNIX time
    $this->previousMonth = $this->calendarDate->requestMonth - 1 == 0 ? 12 : $this->calendarDate->requestMonth - 1; // номер предыдущего месяца
    $this->numDaysInPreviousMonth = date("t", mktime(0, 0, 0, $this->previousMonth, 1, $this->calendarDate->requestYear)); // число дней в предудущем месяце
    $this->firstDayOfTheWeekInMonth = date('N', mktime(0, 0, 0, $this->calendarDate->requestMonth, 1, $this->calendarDate->requestYear)); // первый день недели текущего месяца
    $this->lastDayOfTheWeekInMonth = date('N', mktime(0, 0, 0, $this->calendarDate->requestMonth, $this->numDaysInMonth, $this->calendarDate->requestYear)); // последний день недели текущего месяца
  }
    
  public function __get($name)
  {
    return isset($this->{$name}) ? $this->{$name} : null;
  }  
  
  public function __isset($name)
  {
    return isset($this->{$name});
  }
  
  public function getDayUrl($format, $year, $month, $day)
  {
    $ret = preg_replace('/#year#/', $year, $format);
    $ret = preg_replace('/#month#/', $month, $ret);
    $ret = preg_replace('/#day#/', $day, $ret);
    return $ret;
  }
    
  private static $monthNames = array(1 => 'Январь', 2 => 'Февраль', 3=> 'Март', 4=> 'Апрель', 5=> 'Май', 6=> 'Июнь', 
    7=> 'Июль', 8=> 'Август', 9=> 'Сентябрь', 10=> 'Октябрь', 11=> 'Ноябрь', 12=> 'Декабрь');
  private $currentmonthName;
  public function prepareCalendarTable(array $calendarDates, array $hintDates) {
    // создаем массив-таблицу с датами
    // 1. Вставляем дни предыдущего месяца, если они есть
      if ($this->firstDayOfTheWeekInMonth !=1 ) {
        $firstDayInCalendar = $this->numDaysInPreviousMonth - $this->firstDayOfTheWeekInMonth + 2; // первый день в календаре (понедельник)
        for ($i = 1; $i < $this->firstDayOfTheWeekInMonth; $i++)
          $this->calendarTable[] = array('day' => $firstDayInCalendar++, 'type' => 'previous');
        }
    // 2. Вставляем дни текущего месяца  
      for ($i = 1; $i <= $this->numDaysInMonth; $i++) {
        $this->calendarTable[] = array('day' => $i, 'type' => 'current', 
          'is_event' => array_key_exists($i, $calendarDates),
          'href' => $calendarDates[$i]['href'],
          'is_today' => ($this->calendarDate->curDay == $i && $this->calendarDate->curMonth == $this->calendarDate->requestMonth && $this->calendarDate->curYear == $this->calendarDate->requestYear),
          'hints' => array_key_exists($i, $hintDates) ? $hintDates[$i] : null,
          'pic' => array_key_exists($i, $calendarDates) ? $calendarDates[$i]['pic'] : null,
          );
        }
    // 3. Вставляем дни следующего месяца, если они есть
      if ($this->lastDayOfTheWeekInMonth !=7 ) {
        $lastDayInCalendar = 7 - $this->lastDayOfTheWeekInMonth; // последний день в календаре (вс)
        for ($i = 1; $i <= $lastDayInCalendar; $i++)
          $this->calendarTable[] = array('day' => $i, 'type' => 'next');
        }
        
    $this->currentmonthName = self::$monthNames[$this->calendarDate->requestMonth];
    return $this;
    }
  
  private $monthEvents;
  public function setMonthEvents(array $monthEvents) {
    ksort($monthEvents);
    $this->monthEvents = $monthEvents;
  }
  
  }



?>