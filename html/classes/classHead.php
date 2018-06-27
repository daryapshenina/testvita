<?php
defined('AUTH') or die('Restricted access');

class classHead
{
	public function __construct()
	{
		$this->fileArray = array();
		$this->codeArray = array();		
	}

	public function addFile($filePath)
	{
		foreach($this->fileArray  as &$iter)
		{
			if($iter == $filePath){return false;}
		}
		array_push($this->fileArray, $filePath);
	}

	public function addCode($code)
	{
		array_push($this->codeArray, $code);
	}	
	
	public function out()
	{
		$fileOut= '';
		foreach($this->fileArray as &$iter)
		{
			$strArr = explode('.', $iter);	
			$last = count($strArr)-1;
			$type = $strArr[$last];	
			
			if($type == 'css'){$fileOut .= '<link href="'.$iter.'" type="text/css" media="all" rel="stylesheet"/>';}
			else if($type == 'js'){$fileOut .= '<script type="text/javascript" src="'.$iter.'"></script>';}
			
			$fileOut.= "\n";
		}		
		$codeOut = implode("\n", $this->codeArray);
		echo $fileOut.$codeOut."\n";
	}

	private $fileArray;
	private $codeArray;
};

?>