<?php
/*
* Fonction pour modifier les images
*
* CRии LE 7 NOVEMBRE 2011
***************************************
* Functions to edit images/pictures
*
* CREATED ON NOVEMBER 07, 2011
*
*
*
* EXEMPLE D'UTILISATION / USAGE EXAMPLE:
//////////////
//

// CrИation d'une image Ю taille rИduite si elle est trop grande
$image = new c_ImageEdit; // Utilisation de la classe c_ImageEdit
$image->setImage("image.jpg"); // On charge l'image (jpeg ou png)
$image->resizeImageIfTooBig(800, 720); // On resize l'image selon le gout
$image->saveImage("test.jpg"); // On enregistre l'image resizИe

// CrИation d'un thumbs de 200x200, zoomИ Ю 25%
$image = new c_ImageEdit; // Utilisation de la classe c_ImageEdit
$image->setImage("image.jpg"); // On charge l'image (jpeg ou png)
$image->createThumb(200, 0.75); // CrИation d'un thumb 200x200, zoomИ Ю 25%
$image->saveThumb("testThumb.jpg");

/* 
//////////////
*/

class c_ImageEdit{
	var $imgSrc, $myImage, $cropHeight, $cropWidth, $x, $y, $thumb, $width, $height;  
	
	function setImage($image){
	//Your Image
	   $this->imgSrc = $image; 
						 
	//getting the image dimensions
	   list($this->width, $this->height) = getimagesize($this->imgSrc); 
						 
	//create image from the jpeg
    $srcString = Array();
    $srcString = explode('.', $this->imgSrc);
        if($srcString[count($srcString) - 1] == "jpeg" || $srcString[count($srcString) - 1] == "jpg")
            $this->myImage = imagecreatefromjpeg($this->imgSrc) or die("Erreur lors de l'ouverture de l'image");
        else
            $this->myImage = imagecreatefrompng($this->imgSrc) or die("Erreur lors de l'ouverture de l'image");
            
				 
	} 
	function createThumb($thumbSize, $cropPercent = .30){
        if($this->width > $this->height) $biggestSide = $this->width; //find biggest length
        else $biggestSide = $height;
       
        //The crop size will be half that of the largest side
        $this->cropWidth   = $biggestSide*$cropPercent; 
        $this->cropHeight  = $biggestSide*$cropPercent; 
						 				 
        //getting the top left coordinate
        $this->x = ($this->width - $this->cropWidth)/2;
        $this->y = ($this->height - $this->cropHeight)/2;
        $this->thumb = imagecreatetruecolor($thumbSize, $thumbSize); 
        imagecopyresampled($this->thumb, $this->myImage, 0, 0, $this->x, $this->y, $thumbSize, $thumbSize, $this->cropWidth, $this->cropHeight);
	}
	function saveThumb($stringSrc){
		imagejpeg($this->thumb, $stringSrc);
	}
    function saveImage($stringSrc){
        imagejpeg($this->myImage, $stringSrc);
    }
    function resizeImageIfTooBig($maxWidth, $maxHeight){
        /*
        * Resize l'image en gardant le ratio de pixels.
        * Elle sera resize seulement si elle est plus grande que 
        * les dimensions donnИes en paramХtre
        */
        $xFirst = $this->width;
        $yFirst = $this->height;
        $xFinal = 0;
        $yFinal = $yFirst;
        //Resize the image
        $ratioX = $yFirst / $xFirst;
        $ratioY = $xFirst / $yFirst;
        $diff = 0;
        if($xFirst > $maxWidth){
            $diff = $xFirst - $maxWidth;
            $xFinal = $maxWidth;
            $yFinal -= round($diff * $ratioX);
        }else{
            $xFinal = $xFirst;
        }
        if($yFinal > $maxHeight){
            $diff = $yFinal - $maxHeight;
            $yFinal -= $diff;
            $xFinal -= round($diff * $ratioY);
        }
        $this->width = $xFinal;
        $this->height = $yFinal;
        $finalImage = imagecreatetruecolor($xFinal, $yFinal);
        imagecopyresampled($finalImage, $this->myImage, 0, 0, 0, 0, $this->width, $this->height, $xFirst, $yFirst);
        $this->myImage = $finalImage;
    }
} 
?>