<?php
class BACalendarDate {

  public function __construct() {}
  
  public function __get($name) {
    return isset($this->{$name}) ? $this->{$name} : null;
  }
  
  public function __isset($name)
  {
    return isset($this->{$name});
  }
  
  private $curYear, $curMonth, $curDay;
  public function setCurDate($year, $month, $day) {
    $this->curYear = $year;
    $this->curMonth = $month;
    $this->curDay = $day;
  }

  private $requestYear, $requestMonth;
  public function setRequestDate($year, $month, $direction) {
    $requestYear = $year;
    switch($direction) {
      case 'prev': 
        $requestMonth = $month - 1;
        if ($requestMonth == 0) {
          $requestMonth = 12;
          $requestYear--;
          }
        break;
      case 'next':
        $requestMonth = $month + 1;
        if ($requestMonth == 13) {
          $requestMonth = 1;
          $requestYear++;
          }
        break;
      default:
        $requestMonth = $month;
      }
    $this->requestYear = $requestYear;
    $this->requestMonth = $requestMonth;
  }
  
  

}