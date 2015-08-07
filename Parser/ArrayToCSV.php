<?php
/*
 * Converts array to csv file format.
 * @return array of csv rows.
 * @author:karandewan
 * 
 */
class ArrayToCSV{
	
	private $arrayToConvert = array();
	
	public function __construct(){
		
	
	}
	
	public function setInputArray($array){
		
		$this->arrayToConvert = $array;
	}
	
	public function convertToCSV(){

		$resultArray = array(); // this is the final array with first line heading and next lines data.. everything comma seperated..
		
		$headingRow = array(); //This will be the comma seperated heading row.. 
		
		$dataValue = array();
		
		$dataRow = "";
		
		$dataRows = array(); // this will contain comma seperated data rows..
		
		foreach($this->arrayToConvert as $key => $val){

			$eachArray = $val;
			
			foreach($eachArray as $k => $v){

				if(is_array($v)){

					$this->serialiseTheArrayKey($v,$k,$headingRow,$dataValue);
				
				}else{

					$headingRow[$k] = true;
					
					$dataValue[$k] = $v;
				}
			}
			
			$this->checkMissingValues($dataValue,$headingRow);
			
			$dataRow = implode(",",$dataValue);
			
			$dataRows[] = $dataValue;
			
			$dataValue = array();
			
		}
		
		$headingKeys = array_keys($headingRow);
		
		array_push($resultArray, $headingKeys);
		
		foreach($dataRows as $d){
			
			$r = $this->buildDataRow($d,$headingKeys);
			
			array_push($resultArray, $r);
		}
		
		return $resultArray;
	}
	
	private function buildDataRow($row,$headingOrder){
		
		$ret = "";
		
		foreach($headingOrder as $v){

			$ret .= $row[$v].",";
		
		}
		
		if(substr($ret, strlen($ret)-1) == ","){

			$ret = substr($ret,0,strlen($ret)-2);
		
		}
		
		return explode(",",$ret);
	
	}
	
	private function checkMissingValues(&$data,$headings){
		
		$allHEadingKeys = array_keys($headings);
		
		foreach($headings as $k => $v){

			if(!array_key_exists($k, $data)){

				$data[$k] = "";
			
			}
		
		}
	}
	private function serialiseTheArrayKey($arr,$myName,&$headingRow,&$dataRow){

		$ret = "";
		
		foreach($arr as $k => $val){

			$ret =$myName;
			
			$dk = ArrayBuilder::getDummyKey();
			
			if($k != $dk){

				$ret .="_".$k;
			
			}
			
			if(is_array($val)){

				$this->serialiseTheArrayKey($val,$ret,$headingRow,$dataRow);
			
			}else{

				$headingRow[$ret] = true;
				
				$dataRow[$ret] = $val;
			
			}
		
		}
	
	}
	
}