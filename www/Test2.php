<?php

//declare(strict_types = 1); // strict requirement

/*
 *  File Name:    Test2.php
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
 * Date: 4 Sept 2021
 * ****************************************************************
 */

$font = "../fonts/LiberationSans-Regular.ttf";
$text = date(DATE_RSS);
$font_size = 10;
$angle = 0;

$bbox = imageftbbox($font_size, $angle, $font, $text);

$width = 400;
$height = 300;

$image = imagecreate($width, $height);
$white = imagecolorallocate($image, 255, 255, 255); // white background
$black = imagecolorallocate($image, 0, 0, 0);

imagerectangle($image, 0, 0, $width - 1, $height - 1, $black);

$centre_x = $width / 2;
$centre_y = $height / 2;

$text_x = $centre_x - (($bbox[0] + $bbox[4]) / 2);
$text_y = $centre_y - (($bbox[1] + $bbox[5]) / 2);

imagefttext($image, $font_size, $angle, $text_x, $text_y, $black, $font, $text);

header("Content-type: image/png");
imagepng($image);
imagedestroy($image);

