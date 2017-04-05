<?php



    /***
    * 

    [skills]

        [skill value="" color="" size=""]
            Contents
        [/skill]

    [/skills]
    *
    *
    */

    function hippo_shortcode_skills($atts, $contents = '')
    {
        $attributes = shortcode_atts(array(), $atts);
        ob_start();
        ?>
        <div class="progress-bars">
            <?php
                echo do_shortcode($contents);
            ?>
        </div>
        <?php

        return ob_get_clean();
    }

    add_shortcode('skills', 'hippo_shortcode_skills');

    function hippo_shortcode_skill($atts, $contents = '')
    {
        $attributes = shortcode_atts(array(
            'title'   => '',
            'percent' => ''
        ), $atts);
        ob_start();
        ?>
        <div class="skill-progress"
             data-skill="<?php echo $attributes[ 'percent' ] ?>"><?php echo $attributes[ 'title' ] ?></div>
        <?php
        return ob_get_clean();
    }

    add_shortcode('skill', 'hippo_shortcode_skill');






