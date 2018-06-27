<?php

include_once "ImageResizeMain.php";

class ImageResizeSmart extends ImageResizeMain
{
	protected function resize()
	{
		if($this->resHeight <= 0 || $this->resWidth <= 0)
			return false;

		$canvasHeight = $this->resHeight;
		$canvasWidth = $this->resWidth;
		$canvasPaddingTop = 0;
		$canvasPaddingLeft = 0;

		$ratioX = $this->resWidth / $this->srcWidth;
		$ratioY = $this->resHeight / $this->srcHeight;

		if($ratioX > $ratioY)
		{
			$canvasWidth = floor($this->srcWidth / ($this->srcHeight / $this->resHeight));
			$canvasPaddingLeft = round(($this->resWidth / 2) - ($canvasWidth / 2));
		}
		else
		{
			$canvasHeight = floor($this->srcHeight / ($this->srcWidth / $this->resWidth));
			$canvasPaddingTop = round(($this->resHeight / 2) - ($canvasHeight / 2));
		}

		$this->resImage = imagecreatetruecolor($this->resWidth, $this->resHeight);
		$white = imagecolorallocate($this->resImage, 255, 255, 255);
		imagefilledrectangle($this->resImage, 0, 0, $this->resWidth, $this->resHeight, $white);

		imagecopyresampled($this->resImage, $this->srcImage,
			$canvasPaddingLeft, $canvasPaddingTop, 0, 0,
			$canvasWidth, $canvasHeight,
			$this->srcWidth, $this->srcHeight);

		ImageJpeg($this->resImage, $this->resPath, 100);
		return true;
	}
};
