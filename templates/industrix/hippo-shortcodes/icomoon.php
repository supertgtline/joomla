<?php
    /**
     * @package Hippo Shortcode
     * @author ThemeHippo http://www.themehippo.com
     * @copyright Copyright (c) 2013 - 2014 ThemeHippo
     * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
     */
//no direct accees
defined ('_JEXEC') or die('resticted aceess');

//[hippo-flaticon name="" size="" color="" class=""]

if(!function_exists('icomoon_sc')) {

	function icomoon_sc( $atts, $content="" ) {
	
		extract(shortcode_atts(array(
			   'name' => 'home',
			   'size' => '',
			   'color' => '',
			   'class' =>"",
		 ), $atts));
		 
		 $options = 'style="';
		 $options .= ($size) ? 'font-size:'. (int) $size .'px;' : '';
		 $options .= ($color) ? 'color:'. $color . ';': '';
		 $options .='"';
		 
		return '<i ' . $options . ' class="icon icon-' . str_replace( 'icon-', '', $name ) . ' ' . $class . '"></i>' . $content;
	 
	}
		
	add_shortcode( 'hippo-icomoon', 'icomoon_sc' );
}