<?php

class ImageResizeMain
{
	function __construct($_srcPath, $_resPath, $_resWidth, $_resHeight)
	{
		$this->srcPath = $_srcPath;
		$this->resPath = $_resPath;
		$this->resHeight = $_resHeight;
		$this->resWidth = $_resWidth;		
	}

	public function deleteSource()
	{
		if(!file_exists($this->srcPath)){return false;}

		unlink($this->srcPath);
		return true;
	}

	public function run()
	{	
		if($this->resHeight <= 0 && $this->resWidth <= 0){return false;}

		$inputProp = getimagesize($this->srcPath);
		$this->imageType = $inputProp[2];
		$this->srcHeight = $inputProp[1];
		$this->srcWidth = $inputProp[0];
		
		switch($this->imageType)
		{
			case 1:
				$this->srcImage = imagecreatefromgif($this->srcPath);
				break;

			case 2:
				$this->srcImage = imagecreatefromJpeg($this->srcPath);
				break;

			case 3:
				$this->srcImage = imagecreatefrompng($this->srcPath);
				break;

			default: //если залито что то не то, то он пошлёт нафиг и удалит залитое
				if(file_exists($this->srcPath))
				{
					@chmod($this->srcPath,0755);
					unlink($this->srcPath);
					exit;
				}			
				return false;
		}

		return $this->resize();
	}

	protected function resize()
	{
		$this->resImage = imagecreatetruecolor($this->srcWidth, $this->srcHeight);

		imagecopyresampled($this->resImage, $this->srcImage,
			0, 0, 0, 0,
			$this->srcWidth, $this->srcHeight,
			$this->srcWidth, $this->srcHeight);

		ImageJpeg($this->resImage, $this->resPath, 100);
		return true;
	}

	function __destruct()
	{
		if($this->resImage != null)
			ImageDestroy($this->resImage);

		if($this->srcImage != null)
			ImageDestroy($this->srcImage);
	}

	protected $srcImage;
	protected $srcPath;
	protected $srcHeight;
	protected $srcWidth;

	protected $resImage;
	protected $resPath;
	protected $resHeight;
	protected $resWidth;

	protected $imageType;
};
