<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
//Ctes que identifican la posicion donde preguntar por el Width y el Height del Array ImageInfo
define("IMG_INFO_WIDTH",0);
define("IMG_INFO_HEIGHT",1);
//Modos de realizar el redimensionamiento de una imagen
define("IMG_RESIZE_STRICT",0);
define("IMG_RESIZE_CROP",1);
define("IMG_RESIZE_MAINTAIN_ASPECT_RATIO",2);

/**
 * Crea una imagen en formato JPEG, fruto de la redimensión de otra imagen.
 *
 * @param $infile Path del fichero fuente
 * @param $outfile Path del fichero destino después de la redimensión
 * @param $dst_width Ancho de la nueva imagen
 * @param $dst_height Alto de la nueva imagen
 * @param $mode modos de realizar el redimensionamiento de una imagen. Sus valores pueden ser:
 * 			IMG_RESIZE_STRICT: la imagen original se redimensiona sin importar las proporciones de la misma.
 * 			IMG_RESIZE_CROP: se recorta un cuadrado con las proporciones de la imagen destino y se redimensiona
 *			IMG_RESIZE_MAINTAIN_ASPECT_RATIO: la imagen resultante tiene que mantener las proporciones de la imagen
 * 				original, para ello el alto será el recibido por parámetro y se calculará el nuevo ancho.
 * @return true si no se ha producido ningún error, false en caso contrario
 */
function resize_image($infile, $outfile, $dst_width, $dst_height, $mode=IMG_RESIZE_STRICT){
	try{
		//Extraemos la información de la imagen
		$image_info = getimagesize($infile);
		$src_width = $image_info[IMG_INFO_WIDTH];
		$src_height = $image_info[IMG_INFO_HEIGHT];
		$src_x = $src_y = 0;

		switch ($image_info['mime']) {
			case 'image/gif':
				if (imagetypes() & IMG_GIF)  {
					$src_img = imageCreateFromGIF($infile) ;
				}
				break;
			case 'image/jpeg':
				if (imagetypes() & IMG_JPG)  {
					//Comprobamos si el JPEG que queremos modificar es del mismo tamaño que el JPEG destino
					if ($src_width==$dst_width && $src_width==$dst_height){
						copy($infile,$outfile);
						return true;
					}else{
						$src_img = imageCreateFromJPEG($infile) ;
					}
				}
				break;
			case 'image/png':
				if (imagetypes() & IMG_PNG)  {
					$src_img = imageCreateFromPNG($infile) ;
				}
				break;
			case 'image/wbmp':
				if (imagetypes() & IMG_WBMP)  {
					$src_img = imageCreateFromWBMP($infile) ;
				}
				break;
		}
		if (isset($src_img)) {
			if ($mode == IMG_RESIZE_MAINTAIN_ASPECT_RATIO){
				//Calculamos el nuevo ancho manteniendo la proporcion
				$ratio = $src_width / $src_height;
				$dst_width = intval($dst_height * $ratio);

			}	else if ($mode == IMG_RESIZE_CROP){
				$src_ratio = $src_width / $src_height;
				$dst_ratio = $dst_width / $dst_height;
				if ($src_ratio>$dst_ratio){
					//La imagen original es más ancha q la imagen destino, calculamos el ancho que vamos a recortar
					//y desde que coordenada X
					$src_new_width = intval($src_height * $dst_ratio);
					$src_x=intval(($src_width-$src_new_width)/2);
					$src_width = $src_new_width;

				}else{
					//La imagen original es más alta q la imagen destino, calculamos el alto que vamos a recortar
					//y desde que coordenada Y
					$src_new_height = intval($src_width / $dst_ratio);
					$src_y = intval(($src_height-$src_new_height)/2);
					$src_height = $src_new_height;
				}
			}

			$dst_img = imagecreatetruecolor($dst_width,$dst_height);
			imagecopyresampled($dst_img,$src_img,0,0,$src_x,$src_y,$dst_width,$dst_height,$src_width,$src_height);
			imagejpeg($dst_img,$outfile,JPEG_QUALITY);
			return true;
		}
		return false;
	}catch (Exception $e){
		return false;
	}
}
?>
