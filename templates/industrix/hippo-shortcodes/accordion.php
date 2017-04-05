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
    //  [hippo-accordions id="" class=""]
    //  [hippo-accordion-item title="" id="UNIQUE ID" open="yes|no" class=""]
    //
    //  [/hippo-accordion-item]
    //  [/hippo-accordions],
    //
    //======================================================================


    //[Accordions]
    if (!function_exists('accordion_sc')) {

        function accordion_sc($atts, $content = "")
        {
            $params = shortcode_atts(array(
                'id' => 'collapse-wrapper',
                'class' => '',
            ), $atts);

            ob_start();
            ?>
            <div class="accordion hippo-accordion-shortcode">
            <div class="panel-group <?php echo $params[ 'class' ] ?>" id="<?php echo $params[ 'id' ] ?>">
                <?php echo do_shortcode($content, $params); ?>
            </div>
            </div>
            <?php
            return ob_get_clean();

        }

        add_shortcode('hippo-accordions', 'accordion_sc');

        //Accordion Items
        function accordion_item_sc($atts, $content = "", $parent_attr = array())
        {

            $params = shortcode_atts(array(
                'title' => '',
                'id'    => 'collapse-' . mt_rand(1, 99),
                'open'  => 'no',
                'class' => ''
            ), $atts);
            ob_start();
            ?>
            <div class="panel panel-default <?php echo $params[ 'class' ] ?>">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#<?php echo $parent_attr[ 'id' ] ?>"
                           href="#<?php echo $params[ 'id' ] ?>">
                            <?php echo do_shortcode($params[ 'title' ]) ?>
                        </a>
                    </h4>
                </div>
                <div id="<?php echo $params[ 'id' ] ?>" class="panel-collapse collapse <?php echo ($params['open']=='yes')?'in':'' ?>">
                    <div class="panel-body">
                        <?php echo do_shortcode($content); ?>
                    </div>
                </div>
            </div>
            <?php
            return ob_get_clean();
        }

        add_shortcode('hippo-accordion-item', 'accordion_item_sc');
    }