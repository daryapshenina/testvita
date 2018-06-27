<?php

include_once "ImageResizeMain.php";

class ImageResizeCompression extends ImageResizeMain
{
	protected function resize()
	{
		$this->resImage = imagecreatetruecolor($this->resWidth, $this->resHeight);
		$white = imagecolorallocate($this->resImage, 255, 255, 255);
		imagefilledrectangle($this->resImage, 0, 0, $this->resWidth, $this->resHeight, $white);

		imagecopyresampled($this->resImage, $this->srcImage,
			0, 0, 0, 0,
			$this->resWidth, $this->resHeight,
			$this->srcWidth, $this->srcHeight);

		ImageJpeg($this->resImage, $this->resPath, 100);
		return true;
	}
};
