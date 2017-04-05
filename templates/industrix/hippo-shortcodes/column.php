<?php
    /**
     * @package   Hippo Shortcode
     * @author    ThemeHippo http://www.themehippo.com
     * @copyright Copyright (c) 2013 - 2014 ThemeHippo
     * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
     */
    //no direct accees
    defined('_JEXEC') or die('resticted aceess');

    //======================================================================
    //
    //  [hippo-row id="" class=""]
    //  [hippo-column class=""]  bootstrap 3 grid class
    //
    //  [/hippo-column]
    //  [/hippo-row],
    //
    //======================================================================


    if (!function_exists('row_sc')) {
      

        function row_sc($atts, $content = "")
        {

            $params = shortcode_atts(array(
                'id'    => '',
                'class' => ''
            ), $atts);


            $id = '';
            if ($params[ 'id' ])
                $id = 'id="' . $params[ 'id' ] . '"';


            $html = '<div class="row ' . $params[ 'class' ] . '" ' . $id . '>';
            $html .= do_shortcode($content);
            $html .= '</div>';

            return $html;
        }

        add_shortcode('hippo-row', 'row_sc');

        //Row Items
        function column_sc($atts, $content = "")
        {

            $params = shortcode_atts(array(
                'id'    => '',
                'class' => ''
            ), $atts);

            return '<div class="' . $params[ 'class' ] . '">' . do_shortcode($content) . '</div>';
        }

        add_shortcode('hippo-column', 'column_sc');
    }