<?php
// similar to supported file type but we can add several more support as the output but not take these types as input file or input format...

class SupportedParsing{
	private function __construct(){
		
	}
	
	private static $supportedParsing = array("csv"=>true,"json"=>true,"xml"=>true);
	
	public static function isTypeSupported($ParseTo){
	
		$ret = false;
		if(array_key_exists($ParseTo,SupportedParsing::$supportedParsing)){
			$ret = SupportedParsing::$supportedParsing[$ParseTo];
		}
		return $ret;
	}
	
	public static function getAllSupportedType(){
		$ret = array();
		foreach(SupportedParsing::$supportedParsing as $key => $val){
			if($val){
				$ret[] = $key;
			}
		}
		return $ret;
	}
}