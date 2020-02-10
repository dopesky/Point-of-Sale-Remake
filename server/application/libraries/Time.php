<?php

class Time {
	public static $timezone;

	function __construct() {
		Time::$timezone = new DateTimeZone('Africa/Nairobi');
	}

	function get_now($format = 'Y-m-d H:i:s') {
		return $this->format_date('now', $format);
	}

	function diff_date($date1, $date2, $precision = 'seconds') {
		$precisions = array('seconds' => 1, 'days' => 86400, 'milliseconds' => 0.001);
		$sign = array('+', '-');
		if (!array_key_exists($precision, $precisions)) return false;
		$difference = date_diff(date_create($date2, Time::$timezone), date_create($date1, Time::$timezone));
		$days = $difference->format("%a") * 86400;
		$hours = $difference->h * 3600;
		$minutes = $difference->i * 60;
		$seconds = $difference->s;
		return $sign[$difference->invert] . (($days + $hours + $minutes + $seconds) / $precisions[$precision]);
	}

	function format_date($date = 'now', $format = 'Y-m-d H:i:s') {
		return date_create($date, Time::$timezone)->format($format);
	}

	private function modify_date($time, $precision, $modifier, $date) {
		$date = date_create($date, Time::$timezone);
		$precisions = array('seconds', 'minutes', 'hours', 'days', 'months', 'years');
		if (!in_array($precision, $precisions)) return false;
		switch ($modifier) {
			case 'sub':
				return date_sub($date, date_interval_create_from_date_string($time . ' ' . $precision))->format('Y-m-d H:i:s');
			case 'add':
				return date_add($date, date_interval_create_from_date_string($time . ' ' . $precision))->format('Y-m-d H:i:s');
			default:
				return false;
		}
	}

	function add_time($time_to_add, $date = 'now', $precision = 'seconds') {
		return $this->modify_date($time_to_add, $precision, 'add', $date);
	}

	function subtract_time($time_to_subtract, $date = 'now', $precision = 'seconds') {
		return $this->modify_date($time_to_subtract, $precision, 'sub', $date);
	}
}
