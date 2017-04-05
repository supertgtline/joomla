<?php
    /**
     * @package Hippo Shortcode
     * @author ThemeHippo http://www.themehippo.com
     * @copyright Copyright (c) 2013 - 2014 ThemeHippo
     * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
     */
//no direct accees
defined ('_JEXEC') or die('resticted aceess');


// <span class="timer" data-from="0" data-to="100"
//      data-speed="5000" data-refresh-interval="50"></span>

//[hippo-counter from="" to="" speed="" refresh="" class=""]
if(!function_exists('counter_sc')) {

	function counter_sc( $atts, $content="" ) {
	
		extract(shortcode_atts(array(
			   'from' => '0',
			   'to' => '0',
			   'speed' => '1000',
			   'refresh' => '100',
			   'class' =>''
		 ), $atts));
		 
		 
		 $options = ($from) ? 'data-from="'. $from .'"':'';
		 $options .= ($to) ? ' data-to="'. $to .'"':'';
		 $options .= ($speed) ? ' data-speed="'. $speed .'"':'';
		 $options .= ($refresh) ? ' data-refresh-interval="'. $refresh .'"':'';

		return '<span class="hippo-counter'.$class.'" ' . $options . '>'.$to.'</span>';
	 
	}
		
	add_shortcode( 'hippo-counter', 'counter_sc' );
}