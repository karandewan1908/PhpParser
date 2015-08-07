<?php
/*
 * this is the main class entry point..
 * @author : karan dewan
 */
require_once("ParserInterface.php");

class Parser  implements ParserInterface{
	
	public static $CSVDeliminter = ',';
	
	private $file;
	
	private $toJson;
	
	private $toCSV;
	
	private $toXML;
	
	private $defaultOutFile = "result";
	
	private $outFile;
	// this is an array which has key => value pair.. value can be an array
	
	private $arrayOfElements = array();
	
	private $convertTo = "json";
	
	public $interactivemode = false;
	
	private $inputFormat; // by default input file extension is been used to detect the input file format..
	
	function  __construct(){
		
		
	}
	
	function setInputFileAndParseIT($file){
		
		if(isset($file) && $file){
			$this->file = $file;
		
			$this->convertFileToArray();
		}
	}
	
	
	
	
	private function convertFileToArray(){
		
		$this->arrayOfElements = ArrayBuilder::convertFileToArray($this->file,$this->inputFormat);
			
	}
	
	//This parser support only three type csv, xml and json.. 
	//If needed more parsing option just add parser that will handle it and also update SupportedParser class.
	
	public function parserHandler(){
		
		$format = strtolower($this->convertTo);
		
		$result = "";
		
		if(SupportedParsing::isTypeSupported($this->convertTo)){

			switch($this->convertTo){

				case "csv":

					$result = $this->parseToCSV();
					
					break;
				
				case "json":

					$result = $this->parseToJson();
					
					break;
				
				case "xml":

					$result = $this->parseToXML();
					
					break;

				default:

					print "We don't support yet this parsing.";

					exit;
			
			}
		
		}
		
		$msg = "\nFlushing the output to the file: ";
		
		$file = "";
		if(isset($this->outFile) && !empty($this->outFile)){

			$msg .= " $this->outFile";
			
			$file = $this->outFile;
		
		}else{

			$fx = $this->convertTo;
			
			if($this->convertTo == "json"){

				$fx = "js";
			
			}
			
			$msg .= "$this->defaultOutFile.$fx";
			
			$file = "$this->defaultOutFile.$fx";
		
		}	
		
		print $msg."\n";
		
		if(isset($result) && $result){
			
			$this->addResultToFile($file,$result);
		}
		if($this->interactivemode){
			print"\n";
			$this->optionsMenu();
		}
	}
	
	public function parseToJson(){
		
		$this->toJson = json_encode($this->arrayOfElements);
		
		echo $this->toJson;
		
		return $this->toJson;
	}
	
	public function parseToXML(){
		
		$xmlParser = ArrayToFileFactory::factory("xml");
		
		$xmlParser->setInputArray($this->arrayOfElements);
		
		$this->toXML = $xmlParser->convertToXML();
		
		$this->toXML->asXML("php://stdout");
		
		return $this->toXML->asXML();
	}
	
	public function parseToCSV(){
		
		$csvParser = ArrayToFileFactory::factory("csv");
		
		$csvParser->setInputArray($this->arrayOfElements);
		
		$this->toCSV = $csvParser->convertToCSV();
		
		echo "\nDisplaying csv output with first row as heading and rets the data.\n";
		
		//print_r($this->toCSV);
		$this->flushCSV();
		return $this->toCSV;
	}
	
	private function flushCSV(){
		$data = $this->toCSV;
		foreach($data as $row){
			print implode(",",$row)."\n";
		}
	}
	
	public function createConfig($configArray){
		
		if(count($configArray)> 0){

			if(array_key_exists("convertto", $configArray)){

				$this->convertTo = strtolower($configArray["convertto"]);
			
			}
			
			
			if(array_key_exists("outputfile", $configArray)){

				$this->outFile = $configArray["outputfile"];
			
			}
			
			if(array_key_exists("inputfileformat", $configArray)){
				
				$this->inputFormat = strtolower($configArray["inputfileformat"]);
			}
			
			if(array_key_exists("csvdeliminter", $configArray)){
			
				Parser::$CSVDeliminter = strtolower($configArray["csvdeliminter"]);
			}
			
		}
	}
	
	private function addResultToFile($file,$data){
		
		if(!file_exists($file)){

			Utilities::mkdirs($file);
		
		}
		if($this->convertTo == "csv"){
			$hand = fopen($file,"w");
			foreach($data as $d){
				fputcsv($hand, $d);
			}
			fclose($hand);
		}else{
			file_put_contents($file,$data);
		}
		print "Result added to $file \n";
	}
	
	public function startIntercativeMode(){
		print "Type 'exit' to exit.\nEnter input file";
		$handler = fopen("php://stdin","r");
		$inputFileprovided = false;
		$optionSel = "";
		$optionSelected = false;
		while(($input = fgets($handler))!== false){
			
			if(isset($input) && !empty($input)){
				$input = trim($input);
				if($input){
					if($input == "exit"){
						fclose($handler);
						exit;
					}
					if(!$inputFileprovided){
						if(file_exists($input)){
	
							$this->setInputFileAndParseIT($input);
							
							$inputFileprovided = true;
	
							print "Press appropiate option key to set the property:\n ";
							
							$this->optionsMenu();
						}else{
							print "file doesn't exit enter correct file.\n";
						}	
					}elseif($input == "convert"){
	
						$this->parserHandler();
					
					}else{
						if($optionSelected){
							
							switch($optionSel){
								case "4":
									$this->setInputFileAndParseIT($input);
								case "1":
									$this->convertTo = $input;
									break;
								case "2":
									$this->outFile = $input;
									break;
								case "3":
									$this->inputFormat = $input;
									break;
								default:
									break;
							}
	
							print "Press appropiate option key to set the property: ";
							$this->optionsMenu();
							$optionSelected = false;
							$optionSel = "";
						}
						else{
							$optionSel = $input."";
							$optionSelected = true;
							$msg = " and press enter. After that select appropiate options.";
							switch($optionSel){
								case "4":
									print " Enter 'input file' $msg ";
									break;
								case "1":
									print " Enter 'convert to' option $msg";
								
									break;
								case "2":
									print " Enter 'output file' $msg";
				
									break;
								case "3":
									print " Enter 'input format' $msg";
							
									break;
								default:
									print "unrecognised option";

									print "Press appropiate option key to set the property: ";
									$this->optionsMenu();
									break;
							}
							
						}
	
					}
				}
			}
		}		
	}
	
	private function optionsMenu(){
		print "Press 1: Convert To: can be csv,xml,json\nPress 2:OutputFile: path to output file\nPress 3: Input file format: Can be csv, xml, js\nPress 4 : path to Input file\nType convert: To convert..  ";
	}
}


spl_autoload_register(function($i){
	require_once( $i.".php");
},true);

if(PHP_SAPI){
	
	if(count($argv) < 2){
		
		print  "Usage : php Parser.php inputFile {ConvertTO:[xml|json|csv]} {InputFileFormat:[csv|js|xml]} {OutputFile:outfile}\nAny option inside {} this is optional.
Option can be any order except first one has to be input file.\nkey : value this is how the options should be provided without curly braces. 
First is the key and second is the value.\nEnetring command line mode now!!";
		$p = new Parser();
		$p->interactivemode = true;
		$p->startIntercativeMode();
		
		exit;
	}
	
	$ifile = $argv[1];
	
	if(file_exists($ifile)){

		array_splice($argv, 0,2);
		
		$p = new Parser();
		
		$p->setInputFileAndParseIT($ifile);
		
		$p->createConfig(ConfigParser::parseConfigArray($argv));
		
		$p->parserHandler();
		
	
	}else{

		print "Input file $ifile doesn't exists.";
	
	}

}else{

	print "Only command line access is allowed.";

}
?>