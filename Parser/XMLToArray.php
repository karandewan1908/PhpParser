<?php
/*
* Converts xml file format to array.
* @return array of data as key => value.
* @author:karandewan
*
*/
class XMLToArray{
	
	private $file;
	
	private $mapOfSameNodeValues = array();
	
	private $mapOfAttributes = array();
	
	private $dummyKey = ArrayBuilder::dummykey; 
	
	public function __construct(){

	}
	
	public function setInputFile($file){
		$this->file = $file;
	}
	
	public function convertToArray(){
		try{
			$allData = array();
			
			$eachData = array();
			
			$con = file_get_contents($this->file);
			
			$con = trim($con);
			
			$xml = new SimpleXMLElement($con);
			
			if($xml->count() > 0){
				
				foreach($xml->children() as $element){
					
					if($element->count() > 0){

						$this->parseTheNestedXML($element,$eachData);
						
						$eachData = $eachData[$element->getName()];
						
					}else{

						$this->addValueToArray($key, $element,$eachData);
					
					}
					
					$eachData =	$this->cleanArrayKeys($eachData);
					
					$allData[] = $eachData;
					
					$eachData = array();
				}
				
				
			}else{
				
				$ret = $this->extractValue($xml, "//".$xml->getName());
				
				$eachData[$xml->getName()] = $ret;
				
				$allData[] = $eachData;
			}
			
			return $allData;
		
		}catch(Exception $e){
			
			print "Something went wrong..\n";
			
			echo $e->getMessage();
			
			exit;
		}
		
	}
	
	private function extractValue($node,$xpathQuery){
		
		$ret = $node->xpath($xpathQuery);
		
		return $ret;
	}
	
	private function createAttributesArray($obj){
		$ret = array();
		
		foreach($obj as $o){

			if(isset($o["value"])){
				
				$ret[] = $o["value"][0];
			}
		}
		
		return $ret;
	}
	
	private function createDummyKey($attributesObj,&$arr,$key){
		
		if(array_key_exists($key, $arr)){
		
			if(!is_array($arr[$key])){
		
				$arr[$key] = array();
		
			}
		
		}else{
		
			$arr[$key] = array();
		}
		
		
		if(array_key_exists($key, $this->mapOfAttributes)){
			
			$arr[$key][$this->dummyKey] = $this->mapOfAttributes[$key][0];
			
			array_splice($this->mapOfAttributes[$key], 0,1);
		}else{
			
			$attributes = $this->createAttributesArray($attributesObj);
			
			if(count($attributes) > 0){
				$arr[$key][$this->dummyKey] = $attributes[0];
				
				array_splice($attributes, 0,1);

				if(isset($attributes) && is_array($attributes) && count($attributes) > 0){
					$this->mapOfAttributes[$key] = $attributes;
				}
			}
		}
	}
	
	private function parseTheNestedXML($node,&$arr){
		
		$key = $node->getName();
		
		$xpathQuery = "//".$key."/@value";
		
		$attributes = $this->extractValue($node, $xpathQuery);
		
		if(isset($attributes) && $attributes){
			$this->createDummyKey($attributes,$arr,$key);
		}
		
		if($node->count() > 0){
		
			if(array_key_exists($key, $arr)){

				if(!is_array($arr[$key])){

					$arr[$key] = array();
				
				}
				
			}else{
				
				$arr[$key] = array();
			}
			
			
			foreach($node->children() as $element){
				
				$this->parseTheNestedXML($element,$arr[$key]);
				
			}
		
		}else{

			$this->addValueToArray($key, $node,$arr);
		}
		
	}
	//this will remove any key we added to the xml to remove numer or punctation in the xml tags..
	private function removeKeyPrefix($key){
		$str = ArrayBuilder::xmlPrefix;
		
		if(substr($key,0,strlen($str)) == $str){
			
			$key = substr($key,strlen($str));
		}
		return $key;
	}
	
	private function cleanArrayKeys($arr){
		$ret = array();
		foreach($arr as $k => $v){
			$k = $this->removeKeyPrefix($k);
			if(is_array($v)){
				$v = $this->cleanArrayKeys($v);
			}
			$ret[$k] = $v;
		}
		return $ret;
	}
	
	private function addValueToArray($key,$node,&$arr){

		if(!array_key_exists($key,$this->mapOfSameNodeValues)){
		
			$xpathQuery = "//".$key."/text()"; //relative query for single child..
		
			$ret = $this->extractValue($node, $xpathQuery);
				
			$arr[$key] = $ret[0];
		
			array_splice($ret, 0,1);
		
			if($ret && is_array($ret) &&count($ret) > 0){
		
				$this->mapOfSameNodeValues[$key] = $ret;
			}
		}else{
		
			$arr[$key] = $this->mapOfSameNodeValues[$key][0];
		
			array_splice($this->mapOfSameNodeValues[$key], 0,1);
		
		}
	}
}

?>