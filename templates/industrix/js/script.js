/**
 *------------------------------------------------------------------------------
 * @package       T3 Framework for Joomla!
 *------------------------------------------------------------------------------
 * @copyright     Copyright (C) 2004-2013 JoomlArt.com. All Rights Reserved.
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 * @authors       JoomlArt, JoomlaBamboo, (contribute to this project at github
 *                & Google group to become co-author)
 * @Google group: https://groups.google.com/forum/#!forum/t3fw
 * @Link:         http://t3-framework.org
 *------------------------------------------------------------------------------
 */

jQuery(function ($) {



    // -------------------------------------------------------------
    // Defining Skill progress
    // -------------------------------------------------------------

    (function () {
        $('.skill-progress').hippoSkillPercentage({
            width: 150,
            background: '#ccc',
            font: '14px verdana',
            fontColor: '#444'
        });
    }());

    // Hippo counter

    $('.hippo-counter').parent().waypoint(function () {
            $(this).find('>.hippo-counter').countTo();
        },
        {
            offset     : '100%',
            triggerOnce: true
    });

    //Sticky menu function

    (function () {

        $('.css-sticky').removeClass('sticky-menu');
        $(window).on('scroll', function () {
            if ($(document).scrollTop() > 150) {
                $('.css-sticky').addClass('sticky-menu');
            } else {
                $('.css-sticky').removeClass('sticky-menu');
            }
        });

    }());

    

});  //   end of jQuery(function($){




