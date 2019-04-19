<?php
  class Time {
    public static $timezone;

    public function __construct(){
     Time::$timezone=new DateTimeZone('Africa/Nairobi');
    }

    function get_now($format='Y-m-d H:i:s'){
      return $this->format_date('now',$format);
    }

    function diff_date($date1,$date2,$precision='seconds'){
      $precisions=array('seconds'=>86400,'days'=>1,'milliseconds'=>86400000);
      if(!array_key_exists($precision, $precisions)) return false;
    	return date_diff(date_create($date2,Time::$timezone),date_create($date1,Time::$timezone),false)->format("%R%a")*$precisions[$precision];
    }

    function format_date($date='now',$format='Y-m-d H:i:s'){
      return date_create($date,Time::$timezone)->format($format);
    }
  }