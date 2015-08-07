<?php
class FileToArrayFactory{
	
	public static function Factory($inputFormat){
		$ret;
		switch($inputFormat){
			case "csv":
				$ret = new CSVToArray();
				break;
			case "xml":
				$ret = new XMLToArray();
				break;	
		}
		return $ret;
	}
	
}