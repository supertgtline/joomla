<?php
    /**
     * @package Hippo Shortcode
     * @author ThemeHippo http://www.themehippo.com
     * @copyright Copyright (c) 2013 - 2014 ThemeHippo
     * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
     */
    //no direct accees
    defined('_JEXEC') or die('resticted aceess');

    //[hippo-video height="280" width="500"]filepath[/hippo-video]
    if (!function_exists('video_sc')) {
        function video_sc($atts, $content = "")
        {

            extract(shortcode_atts(array(
                'height' => 280,
                'width' => 500,
                'class' => ''
            ), $atts));


            ob_start();

            $video = parse_url($content);

            switch ($video[ 'host' ]) {
                case 'youtu.be':
                    $id  = trim($video[ 'path' ], '/');
                    $src = 'https://www.youtube.com/embed/' . $id;
                    break;

                case 'www.youtube.com':
                case 'youtube.com':
                    parse_str($video[ 'query' ], $query);
                    $id  = $query[ 'v' ];
                    $src = 'https://www.youtube.com/embed/' . $id;
                    break;

                case 'vimeo.com':
                case 'www.vimeo.com':
                    $id  = trim($video[ 'path' ], '/');
                    $src = "http://player.vimeo.com/video/{$id}";
            }

            ?>

            <div class="hippo-video-shortcode <?php echo $class; ?>">
                <iframe src="<?php echo $src; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" frameborder="0"
                        webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
            </div>

            <?php
            $data = ob_get_clean();
            return $data;
        }

        add_shortcode('hippo-video', 'video_sc');
    }