/**

HTML: 

<div class="skill-progress" data-skill="70"></div>

JS:

(function () {
        $('.skill-progress').hippoSkillPercentage({
            width     : 130,
            background: 'rgba(200,200,200,0.09)',
            font      : '14px verdana',
            fontColor : '#444'
        });
    }());


*/

;(function ($) {

    'use strict';

    $.fn.hippoSkillPercentage = function (options) {

        var getArcPoints = function (xOfArc, yOfArc, radius, whichAngle) {
            return {'x': xOfArc + Math.cos(whichAngle) * radius, 'y': yOfArc + Math.sin(whichAngle) * radius};
        }

        var settings = $.extend({
            width     : 150,       //  canvas width and height
            color     : '#e74c3c',  //  progress bar color
            background: '#e74c3c',  //  progress bar path background color
            font      : 'regular 1em Lato',  //  font of skillname and value
            fontColor : '#444',    //   font color
            class     : 'css-canvas',  // canvas class
            weight    : 10,  // progress bar weight
            skill     : 1,  //  skill in percentage, Must define in inline
            position  : 'center' // center
        }, options);

        return this.each(function () {

            var data = {
                width     : $(this).attr('data-width') ? $(this).attr('data-width') : settings.width,
                color     : $(this).attr('data-color') ? $(this).attr('data-color') : settings.color,
                background: $(this).attr('data-background') ? $(this).attr('data-background') : settings.background,
                font      : $(this).attr('data-font') ? $(this).attr('data-font') : settings.font,
                fontColor : $(this).attr('data-fontcolor') ? $(this).attr('data-fontcolor') : settings.fontColor,
                class     : $(this).attr('data-class') ? $(this).attr('data-class') : settings.class,
                skill     : $(this).attr('data-skill') ? $(this).attr('data-skill') : settings.skill,
                weight    : $(this).attr('data-weight') ? $(this).attr('data-weight') : settings.weight,
                position  : $(this).attr('data-position') ? $(this).attr('data-position') : settings.position,
                content   : $(this).text()
            };


            var canvas = $('<canvas/>', {'class': data.class})[0];
            var context = canvas.getContext('2d');
            $(this).html(canvas);


            canvas.width = data.width;
            canvas.height = data.width;   //

            var x = Math.round(data.width / 2);
            var y = Math.round(data.width / 2);
            var padding = Math.round(data.width / 15);   //   padding
            var margin = 20;   //   margin
            var radius = x - padding;

            var percentage = parseInt(data.skill);
            var percentageText = data.skill;

            var startPoint = 1.5 * Math.PI;   //  starting from top

            var dividePercentage = (percentage / 100);
            var degrees = dividePercentage * 380;
            var endPoint = (degrees * (Math.PI / 180) ) + startPoint;

            context.lineCap = "round";
            context.lineWidth = parseInt(data.weight);

            context.font = data.font;
            context.fillStyle = data.fontColor;
            context.textAlign = 'center';
            context.textBaseline = 'middle';
            context.fillText(data.content, x, y);

            var arcPoints = getArcPoints(x, y, radius, endPoint);

            console.log( x, y );

            var pX = arcPoints.x;
            var pY = arcPoints.y;

            if (percentage > 0 && percentage <= 12.5) {
                pY += margin;
            } else if (percentage > 12.5 && percentage <= 25) {

                pY = margin;
            } else if (percentage > 25 && percentage <= 37.5) {
                pX -= margin;
                pY = margin;
            } else if (percentage > 37.5 && percentage <= 49) {
                pX -= margin;
                pY = margin;
            } else if (percentage > 49 && percentage <= 61.5) {
                pX -= margin;
                pY -= margin;
            } else if (percentage > 61.5 && percentage <= 74) {
                pX += margin;
                pY = margin;
            } else if (percentage > 74 && percentage <= 86.5) {
                pX += margin;
                pY = margin;
            } else {
                pX += margin;
                pY = margin;
            }


            if( data.position=='center' ){
                pX = x;
                pY += margin;
            }

            context.fillText(percentageText, pX, pY);

            context.beginPath();
            context.arc(x, y, radius, 0, 2 * Math.PI, false);
            context.strokeStyle = data.background;
            context.stroke();
            context.closePath();

            context.beginPath();
            context.arc(x, y, radius, startPoint, endPoint, false);
            context.strokeStyle = data.color;
            context.stroke();
            context.closePath();
        });
    }
}(jQuery));

