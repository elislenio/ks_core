<?php
namespace Ks\CoreBundle\Classes;

abstract class DbAbs
{
	const ENG_ORCL = 'oracle';
	const ENG_MYSQL = 'mysql';
	
	public static function longDatetime($engine, $field)
	{
		switch ($engine)
		{
			case self::ENG_ORCL:
				return "to_char(" . $field . ", 'dd/mm/yyyy hh24:mi:ss')";
				break;
			case self::ENG_MYSQL:
				return "DATE_FORMAT(" . $field . ", '%d/%m/%Y %H:%i:%s')";
				break;
		}
		
		return $field;
	}
	
	// Converts from "05/03/2011 14:00:21"
	// to doctrine datetime format: "2011-03-05 14:00:21"
	public static function toDoctrineDT($value)
	{
		// Extracts date part
		$value = explode(' ', $value);
		// Split into array
		$date = explode('/', $value[0]);
		// Reverse the elements
		$date = array_reverse($date);
		// Glue with "-"
		$value[0] = implode('-', $date);
		// New string
		$value = implode(' ', $value);
		return $value;
	}
}