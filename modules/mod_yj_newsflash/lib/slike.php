<?php
/*----------------------------------------------------------------------
#Youjoomla Images
# ----------------------------------------------------------------------
# Copyright (C) 2007 You Joomla. All Rights Reserved.
# Designed by: You Joomla
# License: Copyright Youjoomla.com
# Website: http://www.youjoomla.com
------------------------------------------------------------------------*/

	// no direct access
	defined('_JEXEC') or die('Restricted access');

	function slikice_yjnf1 ( &$row, $width=-1, $height=-1 ) {
		//generate common variables
		$database					=& JFactory::getDBO();
		$mosConfig_absolute_path	= JPATH_ROOT;
		$mosConfig_live_site 		= JURI :: base();

		if($row->images != ''){
			$images 		= $row->images;
			$row_images 	= explode("\n",$images);
			$row->images	= $row_images;
		}

 	    $total=count($row->images);
	    for ($i = 0; $i < $total; $i++ ){
			if($row->images != ''){
				$img = trim($row->images[$i]);
			
				if ( $img ) {
					$attrib = explode( '|', trim( $img ) );
				
					if ( !isset($attrib[1]) || !$attrib[1] ) {
						$attrib[1] = '';
					}
				
					if ( !isset($attrib[2]) || !$attrib[2] ) {
						$attrib[2] = 'Image';
					} else {
						$attrib[2] = htmlspecialchars( $attrib[2] );
					}
					
					if ( !isset($attrib[3]) || !$attrib[3] ) {
						$attrib[3] = 0;
					}
					$attrib[4]	= '';
					$border 	= 0;
					
					if ( !isset($attrib[5]) || !$attrib[5] ) {
						$attrib[5] = '';
					}
				
					if ( !isset($attrib[6]) || !$attrib[6] ) {
						$attrib[6] = '';
					}
				
					$image = '<img src="'.$mosConfig_live_site.'/images/stories/'. $attrib[0] .'"';
				
					if ( !$attrib[4] ) {
						$image .= $attrib[1] ? ' align="'. $attrib[1] .'"' : '';
					}
					$image .=' alt="'. $attrib[2] .'" title="'. $attrib[2] .'" border="'. $border .'" '. (($width != -1) ? 'width="'. $width . '"' : '') .(($height != -1) ? ' height="'. $height .'"' : '').' />';
					$img = $image;
					$regex = '/{mosimage\s*.*?}/i';
					$row->introtext = trim($row->introtext);
					$row->introtext = preg_replace($regex,$img,$row->introtext,1);
				}
			}
       }
	}
?>