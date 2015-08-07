<?php
/*
 * Converts csv file format to array.
* @return array of data as key => value.
* @author:karandewan
*
*/
class CSVToArray{
	
	private $file;
	
	
	public function __construct(){
		
	}
	
	public function setInputFile($file){
		$this->file = $file;
	}
	
	public function convertCSVToArray(){
			
		$handler = fopen($this->file,"r");
	
		$count = 0;
	
		$headers = array();
	
		$eachDataArray  = array();
	
		$allData = array();
	
		while ($data = fgetcsv($handler,0,Parser::$CSVDeliminter)){
				
			if($count==0){
	
				$headers = $data;
	
				$eachDataArray = $this->createArrayFromNames($headers);
	
				$count++;
					
			}else{
	
				$this->placeDataInArray($eachDataArray,$data,$headers);
	
				array_push($allData,$eachDataArray );
	
				$count++;
	
	
			}
		}
	
	
		return $allData;
	}
	
	private function placeDataInArray(&$arr,$items,$headersName){

		$totalHeading = count($headersName);
	
		$totalItem = count($items);
	
		for($i=0;$i< $totalHeading;$i++){
	
			$col = $headersName[$i];
				
			if(strpos($col,"_")){
	
				$bits = explode("_",$col);
	
				$v = "";
	
				if(isset($items[$i]) && $items[$i]){
	
					$v = $items[$i];
	
				}
	
				$this->recursivelyFindThePosition($arr,$bits,$v);
					
	
			}else{
				$val = "";
	
				if(isset($items[$i]) && $items[$i]){
	
					$val = $items[$i];
	
				}
	
				if($this->checkNeedForDummyKey($arr, $col)){
	
					$arr[$col][ArrayBuilder::dummykey] = $val;
	
				}else{
	
					$arr[$col] = $val;
	
				}
	
			}
		}
		if($totalItem > $totalHeading){
			$str = implode(",",$items);
				
			echo "Keeping only $totalHeading items as data is more. Line has a data: $str which is ". ($totalItem - $totalHeading) ." items extra\n";
		}
	
	}
	
	private function recursivelyFindThePosition(&$arr,$bits,$val){
		
	
		if(count($bits) == 1){
	
		
			if($this->checkNeedForDummyKey($arr, $bits[0])){
	
				$arr[$bits[0]][ArrayBuilder::dummykey] = $val;
			
			}else{
	
				$arr[$bits[0]] = $val;
			}
		}
		else{
				
			$prevBit = $bits[0];
				
			array_splice($bits,0,1);
				
			$this->recursivelyFindThePosition($arr[$prevBit],$bits,$val);
			}
	}
	
	private function createArrayFromNames(&$nameArray){
	
		$ret = array();
	
		$count =0;
	
		foreach($nameArray as $val){
			if(empty($val)){
	
				array_splice($nameArray,$count,1);
	
				continue;
			}
	
			if(strpos($val,"_")){
	
				$splitBits = explode("_",$val);
	
				$pk = $splitBits[0];
	
				array_splice($splitBits,0,1);
	
				$this->recurssiveAddKeyToArray($ret,$pk,$splitBits);
	
			}else{
	
				if($this->checkNeedForDummyKey($ret, $val)){
						
					$ret[$val][ArrayBuilder::dummykey] = "";
	
				}else{
						
					$ret[$val] = "";
				}
	
			}
			$count++;
		}
	
		return $ret;
	}
	
	private function checkNeedForDummyKey($items,$key){
		$ret = false;
	
		if(array_key_exists($key, $items)){
			$check = $items[$key];
			if(is_array($check)){
				$ret = true;
			}
		}
		return $ret;
	}
	

	private function recurssiveAddKeyToArray(&$item,$parentKey,$childKeys){
	
		if(count($childKeys) == 1){
				
			if(!array_key_exists($parentKey, $item)){
					
				$item[$parentKey] = array();
	
				$item[$parentKey][$childKeys[0]] = "";
					
			}else{
	
				if($this->checkNeedForDummyKey($item[$parentKey],$childKeys[0])){
	
					$item[$parentKey][$childKeys[0]][ArrayBuilder::dummykey] = "";
	
				}else{
	
					$item[$parentKey][$childKeys[0]] = "";
	
				}
			}
		}else{
				
			if(!array_key_exists($parentKey, $item)){
					
				$item[$parentKey] = array();
					
			}else if(!is_array($item[$parentKey])){
				$item[$parentKey] = array();
				$item[$parentKey][ArrayBuilder::dummykey] = "";
			}
				
			$pk =$childKeys[0];
				
			array_splice($childKeys,0,1);
				
			$this->recurssiveAddKeyToArray($item[$parentKey],$pk,$childKeys);
		}
	
	}
	
}