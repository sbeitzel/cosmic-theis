<?php
// $Id: Image.php,v 1.2 2003/12/23 01:30:59 admin Exp $

class Image {
	var $imageLocation;
	var $image;
	
	function Image( $imageLoc ) {
		$this->createImage( $imageLoc );
	}

	/*************************************
	 *		PUBLIC FUNCTIONS			 *
	 *************************************/

	function load ( $imageLoc ) {
		$this->createImage( $imageLoc );
	}

	function createAndSaveThumbnail( $newPath, $width="80", $height="80" ) {
		if ( $this->image != null ) {
            if ( $this->resizeImage( $newPath, $width, $height) ) return $newPath;
			else return "Error while creating the thumbnail.";
		} else return "Can't create thumbnail from null image.";
	}

	/*************************************
	 *		PRIVATE FUNCTIONS			 *
	 *************************************/

	function createImage( $imageLoc ) {
		$this->imageLocation = $imageLoc;
		// create new image
    	if(eregi("\.png$",$imageLoc)) $this->image = ImageCreateFromPNG ( $imageLoc );
		if(eregi("\.(jpg|jpeg)$",$imageLoc)) $this->image = ImageCreateFromJPEG ( $imageLoc );
	}

	// check to see what version (if any) of the gd libraries are installed
	function chkgd2() {
		$testGD = get_extension_funcs("gd"); // Grab function list
		if (!$testGD) { 
			echo "GD not even installed."; 
			return false; 
		} else {
			ob_start(); // Turn on output buffering
			phpinfo(8); // Output in the output buffer the content of phpinfo
			$grab = ob_get_contents(); // Grab the buffer
			ob_end_clean(); // Clean (erase) the output buffer and turn off output buffering 
			
			$version = strpos  ($grab,"2.0 or higher"); // search for string '2.0 or higher'
			if ( $version ) return "gd2"; // if find the string return gd2
			else return "gd"; // else return "gd"
		}
	}	
	
	// resize method
	function resizeImage( $new_image_file_path, $max_width=2000, $max_height=1600 ) {
    	$return_val = 1;
    	$img = &$this->image;
    	$FullImage_width = imagesx ($img);    // original width
    	$FullImage_height = imagesy ($img);    // original width
    	// now we check for over-sized images and pare them down
    	// to the dimensions we need for display purposes
    	$ratio =  ( $FullImage_width > $max_width ) ? (real)($max_width / $FullImage_width) : 1 ;
    	$new_width = ((int)($FullImage_width * $ratio));    //full-size width
    	$new_height = ((int)($FullImage_height * $ratio));    //full-size height
    	//check for images that are still too high
    	$ratio =  ( $new_height > $max_height ) ? (real)($max_height / $new_height) : 1 ;
    	$new_width = ((int)($new_width * $ratio));    //mid-size width
    	$new_height = ((int)($new_height * $ratio));    //mid-size height
    	// --Start Full Creation, Copying--
    	// now, before we get silly and 'resize' an image that doesn't need it...
    	if ( $new_width == $FullImage_width && $new_height == $FullImage_height ) copy ( $this->imageLocation, $new_image_file_path );
		// check to see if gd2+ libraries are compiled with php
		$gd_version = ( $this->chkgd2() );
		if ( $gd_version == "gd2" ) {		
    		$full_id =  ImageCreateTrueColor ( $new_width , $new_height ); //Crea un'immagine
    		ImageCopyResampled ( $full_id, $img, 0,0,0,0, $new_width, $new_height, $FullImage_width, $FullImage_height );
		} elseif ( $gd_version == "gd" ) {		
    		$full_id = ImageCreate ( $new_width , $new_height ); //Crea un'immagine
    		ImageCopyResized ( $full_id, $img, 0,0,0,0, $new_width, $new_height, $FullImage_width, $FullImage_height );
		} else "GD Image Library is not installed.";
    	if(eregi("\.(jpg|jpeg)$",$this->imageLocation)) {
    		$return_val = ( $full = ImageJPEG( $full_id, $new_image_file_path, 80 ) && $return_val == 1 ) ? "1" : "0";
    	}
    	if(eregi("\.png$",$this->imageLocation)) {
			$return_val = ( $full = ImagePNG( $full_id, $new_image_file_path ) && $return_val == 1 ) ? "1" : "0";
    	}
    	ImageDestroy( $full_id );
    	// --End Creation, Copying--
    	return ($return_val) ? TRUE : FALSE ;
	}
	
}
?>