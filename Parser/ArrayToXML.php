<?php
/*
 * Converts array to xml file format.
* @return simpleElement object..
* @author:karandewan
*
*/
class ArrayToXML{
	private $arrayToConvert = array();
	
	private $dummyKey ;
	
	public function __construct(){
	
		
		$this->dummyKey = ArrayBuilder::getDummyKey();
	}
	
	public function setInputArray($array){
		$this->arrayToConvert = $array;
	}
	
	public function convertToXML(){
		$xml = new SimpleXMLElement('<root/>');
		
		foreach($this->arrayToConvert as $val){
			 $section = $xml->addChild("section");
			foreach($val as $key => $v){
				if(is_array($v)){
					$this->convertNestedArrayToXML($section,$v,$key);
				}else{
					$this->createXMLTag($myName);
					$section->addChild($key,$v);
				}
			}
		}
		return $xml;
	}
	
	private function convertNestedArrayToXML(&$xml,$arr,$myName){
		
		$this->createXMLTag($myName);
		
		$child = $xml->addChild($myName);
		
		foreach($arr as $k => $v){
			
			if($k == $this->dummyKey){
				$child->addAttribute("value",$v);
			}else{
				if(is_array($v)){
					$this->convertNestedArrayToXML($child,$v,$k);
				}else{
					$this->createXMLTag($k);
					
					$child->addChild($k,$v);
				}
			}
			
			
		}
	}
	
	private function createXMLTag(&$keyName){
		$check = preg_match('/^[\p{P}\d].*/', $keyName);
		
		if($check){
			$keyName = ArrayBuilder::xmlPrefix.$keyName;
		}
	}
	
	
}

?>
