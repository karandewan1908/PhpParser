<?php
class Utilities{
	
	public static function mkdirs($file){
		$dir = dirname($file);
		
		if(!file_exists($dir)){

			Utilities::mkdirs($dir);
			
			mkdir($dir);
		}
		
	}
	
}