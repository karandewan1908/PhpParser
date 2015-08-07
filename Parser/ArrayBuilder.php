<?php
/*
 * This will convert input file to array... we create a dummy key also which will be added as attribute value to xml..
 * Reason being we could have input x_y = value but then we can also have x = value....
 * Since xml doesn't allow number or punctation as starting character of a tag we add clean_ for such cases...
 * @author: karan dewan
 */
class ArrayBuilder{
	
	const  dummykey = "_self";
	
	const xmlPrefix = "clean_";
	
	public static function getDummyKey()
	{
		return self::dummykey;
	}
	public static function convertFileToArray($file,$inputFormat){
		
		$ret = array();  //always return array no undefine notices will be generated then..
		
		if(ArrayBuilder::verifyFileAndExtension($file)){
			$fileExtension = "";
			if(isset($inputFormat) && !empty($inputFormat)){
				$fileExtension = $inputFormat;
			}else{
				$fileName = basename($file);
				$fileExtension = substr($fileName,strrpos($fileName,".")+1);
			}
			switch(strtolower($fileExtension)){
				case "csv":
					$ret = ArrayBuilder::convertCSVToArray($file);
					break;
				case "js":
					$ret = ArrayBuilder::convertJSONToArray($file);
					break;
				case "xml":
					$ret = ArrayBuilder::convertXMLToArray($file);
					break;
				default:
					print "We don't support yet $fileExtension.";
					exit;
					
			}
		}else{
			$allSuppFile = SupportedFileType::getAllSupportedType();
			if(count($allSuppFile) > 0){
				$str = implode(",",$allSuppFile);
				print "Supported files are: $str";
				exit;
			}
		}
		
		return $ret;
		
	}
	
	private static function convertCSVToArray($file){
		
		$allData = array();
		
		$csv = FileToArrayFactory::Factory("csv");
		
		$csv->setInputFile($file);
		
		$allData = $csv->convertCSVToArray();
		
		return $allData;
	}
	
	private static function convertJSONToArray($file){

		$ret = array();
		
		$data = file_get_contents($file);
		
		$ret = json_decode($data,true);
		
		return $ret;
		
	}
	
	private static function convertXMLToArray($file){
		
		$ret = array();
		
		$xml = FileToArrayFactory::Factory("xml");
		
		$xml->setInputFile($file);
		
		$allData = $xml->convertToArray();
		
		return $allData;
		
		return $ret;
		
	}
	
	
	
	private static function verifyFileAndExtension($file){
		$ret = false;

		if(file_exists($file)){

			$fileName = basename($file);
			
			if(!empty($fileName) && strpos($fileName,".")){
				
				$fileExtension = substr($fileName,strrpos($fileName,".")+1);
				
				
				
				if(isset($fileExtension) && !empty($fileExtension) && SupportedFileType::isFileTypeSupported($fileExtension)){
					$ret = true;
						
				}
			}

		
		}else{
			print "Input file $file doesn't exists.";
			exit;
		}
		
		return $ret;
	}
	
}


?>