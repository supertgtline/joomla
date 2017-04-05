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

	slikice_yjnf1($row);
	$img_url = "";
	unset($img_out);
	
	$introtext=$row->introtext;
	$nadji_sliku=strpos($introtext,"img");
	if ($nadji_sliku){
		$kraj=strpos($introtext,"src=",$nadji_sliku);
		if ($kraj){
			$klasa=$kraj;
			while (($introtext[$klasa]!='"')||($klasa<count($introtext))){
				$img_url .= $introtext[$klasa];
				$klasa++;
				if ($img_url=="src="){
					$img_url="";
					$klasa++;
				}
			}
		}
	}else{
		$fulltext=$row->fulltext;
		$nadji_sliku=strpos($fulltext,"img");
		if ($nadji_sliku){
			$kraj=strpos($fulltext,"src=",$nadji_sliku);
			if ($kraj){
				$klasa=$kraj;
				while (($fulltext[$klasa]!='"')||($klasa<count($fulltext))){
					$img_url.=$fulltext[$klasa];
					$klasa++;
					if ($img_url=="src="){
						$img_url="";
						$klasa++;
					}
				}
			}
		}	   
	}
?>