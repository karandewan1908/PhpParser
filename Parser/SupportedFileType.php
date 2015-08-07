<?php
//This class contains a static array which will tell whether we support particular file type or not..

final class SupportedFileType{
	//make it private no instance is possible..
	private function __construct(){
		
	}
	
	private static $supportedFileType = array("csv"=>true,"js"=>true,"xml"=>true);
	
	
	public static function isFileTypeSupported($fileType){

		$ret = false;
		if(array_key_exists($fileType,SupportedFileType::$supportedFileType)){
			$ret = SupportedFileType::$supportedFileType[$fileType];		
		}
		return $ret;
	} 

	public static function getAllSupportedType(){
		$ret = array();
		foreach(SupportedFileType::$supportedFileType as $key => $val){
			if($val){
				$ret[] = $key;
			}
		}
		return $ret;
	}
}
?>