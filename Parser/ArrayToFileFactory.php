<?php
class ArrayToFileFactory{
	
	public static function factory($format){
		$ret;
		switch($format){
			case "csv":
				$ret = new ArrayToCSV();
				break;
			case "xml":
				$ret = new ArrayToXML();
				break;
		}
		return $ret;
	}
}