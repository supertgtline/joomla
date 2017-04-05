<?php
    /**
     * @package   Hippo Shortcode
     * @author    ThemeHippo http://www.themehippo.com
     * @copyright Copyright (c) 2013 - 2014 ThemeHippo
     * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
     */
    //======================================================================
    //
    //  [hippo-tabs button="" id="" class=""]
    //  [hippo-tab-item title="" id="UNIQUE ID" selected="yes|no" class=""]
    //
    //  [/hippo-tab-item]
    //  [/hippo-tabs],
    //
    //======================================================================


    //no direct accees
    defined('_JEXEC') or die('resticted aceess');

    //[Tab]
    if (!function_exists('tab_sc')) {
        $tabArray = array();
        function tab_sc($atts, $content = "")
        {
            global $tabArray;

            $params = shortcode_atts(array(
                'button' => 'nav-tabs',
                'id'     => 'tab',
                'class'  => 'tab-wrapper'
            ), $atts);

            do_shortcode($content);

            $html = '<div class="tab hippo-tab-shortcode">';
            $html .= '<div class="' . $params[ 'class' ] . '">';

            //Tab Title
            $html .= '<ul class="nav ' . $params[ 'button' ] . '" id="' . $params[ 'id' ] . '">';

            foreach ($tabArray as  $tabtitle) {
                $html .= '<li class="' . (($tabtitle['selected'] == 'yes') ? "active" : "") . '"><a href="#' . $tabtitle['id'] . '" data-toggle="tab">' . do_shortcode( $tabtitle[ 'title' ] ) . '</a></li>';
            }
            $html .= '</ul>';

            //Tab Content
            $html .= '<div class="tab-content">';
            foreach ($tabArray as  $tabcontent) {
                $html .= '<div class="tab-pane fade' . (($tabcontent['selected'] == 'yes') ? " in active" : "") . ' '.$tabcontent['class'] .'" id="' . $tabcontent['id'] . '">' . do_shortcode($tabcontent[ 'content' ]) . '</div>';
            }
            $html .= '</div>';

            $html .= '</div>';
            $html .= '</div>';

            $tabArray = array();

            return $html;
        }

        add_shortcode('hippo-tabs', 'tab_sc');

        //Tab Items
        function tab_item_sc($atts, $content = "")
        {
            global $tabArray;
            $params = shortcode_atts(array(
                'title'    => '',
                'id'       => '',
                'selected' => 'no',
                'class'    => '',
            ), $atts);

            $params[ 'content' ] = $content;

            $tabArray[ ] = $params;
        }

        add_shortcode('hippo-tab-item', 'tab_item_sc');
    }