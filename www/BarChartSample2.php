<?php

/*
 *  File Name:    BarChartSample2.php
 *  Project Name: WebProgPHP-Five
 *
 *  Copyright (c) 2021 Bradley Willcott
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * ****************************************************************
 * Name: Bradley Willcott
 * ID:   M198449
 * Date: 2 Sept 2021
 * ****************************************************************
 */

$width = 400;

$height = 300;

$bar_w = 10;

$max_y = 20;

$margin_top = 20;

$margin_bottom = 20;

$margin_left = 20;

$margin_right = 20;
$y_div = 10;

$rects = array();

$last_x2 = $margin_left;

$i = 1;

$pt = "pt" . $i;

while(isset($_GET[$pt]))
{
    $x1 = $last_x2 + 1;
    $x2 = $x1 + $bar_w;
    $y1 = $margin_top + $max_y - $_GET[$pt];
    $y2 = $margin_top + $max_y;
    $ar = array($x1, $y1, $x2, $y2);
    array_push($rects, $ar);
    $i++;
    $last_x2 = $x2;
    $pt = "pt" . $i;
}

//$img_w = $last_x2 + $margin_right;
//$img_h = $margin_top + $max_y + $margin_bottom;

$ih = imagecreate($width, $height);
$white = imagecolorallocate($ih, 255, 255, 255);
$black = imagecolorallocate($ih, 0, 0, 0);

//imagefill($ih, 0, 0, $white);

for($r = 0; $r < count($rects); $r++)
{
    $red = rand(0, 255);
    $green = rand(0, 255);
    $blue = rand(0, 255);
    $hist_color = imagecolorallocate($ih, $red, $green, $blue);

    imagefilledrectangle($ih, $rects[$r][0], $rects[$r][1], $rects[$r][2],
            $rects[$r][3], $hist_color);

    imageline($ih, $rects[$r][2], $margin_top + $max_y, $rects[$r][2],
            $margin_top + $max_y + 3, $black);

    $ttfbox = imagettfbbox(8, 0, '..\fonts\arial1.ttf', "pt" . ($r + 1));
    $half_pt = ($bar_w / 2) - ceil(($ttfbox[4] - $ttfbox[6]) / 2);
    imagettftext($ih, 8, 0, $rects[$r][0] + $half_pt, $rects[$r][3] + 10,
            $black, '..\fonts\arial1.ttf', "pt" . ($r + 1));
}

imageline($ih, $margin_left, $margin_top, $margin_left, $margin_top + $max_y +
        3, $black);

imageline($ih, $margin_left, $margin_top + $max_y, $last_x2, $margin_top +
        $max_y, $black);

$tick = 0;

while($tick < $max_y)
{
    $tick += $y_div;
    $tick_y = $margin_top + $max_y - $tick;
    imageline($ih, $margin_left - 3, $tick_y, $margin_left, $tick_y, $black);
}

imagepng($ih);

imagedestroy($ih);
?>
