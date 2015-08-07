<?php
class ConfigParser{
	public static function parseConfigArray($configArray){
		$ret = array();
		for($i=0;$i<count($configArray);$i++){
			$option = explode(":",trim($configArray[$i]),2);
			if(count($option) > 1){
				$ret[strtolower(trim($option[0]))] = (trim($option[1]));
			}
		}
		return $ret;
	}
}
?>