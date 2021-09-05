<?php

/*
 *  File Name:    BarChartSample.php
 *  Project Name: WebProgPHP-Five
 *
 * The souce code was copied from:
 * https://code.web-max.ca/image_graph.php
 *
 * ****************************************************************
 * Name: Bradley Willcott
 * ID:   M198449
 * Date: 2 Sept 2021
 * ****************************************************************
 */


// This array of values is just here for the example.

$values = array("23", "32", "35", "57", "12",
    "3", "36", "54", "32", "15",
    "43", "24", "30");

// Get the total number of columns we are going to plot

$columns = count($values);

// Get the height and width of the final image

$width = 400;
$height = 300;

$padding = 10;

// Set the amount of space between each column

$colpadding = 5;

// Get the width of 1 column

$column_width = ($width - ($padding * 2)) / $columns;

// Get maximum bar height

$max_bar_height = $height - ($padding * 2);

// Y-Axis ticks
$y_div = 10;

// Generate the image variables

$image = imagecreate($width, $height);
$white = imagecolorallocate($image, 0xff, 0xff, 0xff); // image's background colour
$gray = imagecolorallocate($image, 0xcc, 0xcc, 0xcc);
$gray_lite = imagecolorallocate($image, 0xee, 0xee, 0xee);
$gray_dark = imagecolorallocate($image, 0x7f, 0x7f, 0x7f);
$black = imagecolorallocate($image, 0, 0, 0);

// Fill in the background of the image
//imagefilledrectangle($image, 0, 0, $width, $height, $white);
imagerectangle($image, 0, 0, $width - 1, $height - 1, $black);
//imageline($image, 0, 0, 0, $height, $color); // Left edge
//imageline($image, 0, $height - 1, $width, $height - 1, $color); // Bottom edge
//imageline($image, $width - 1, $height - 1, $width - 1, 0, $color); // Right edge
//imageline($image, 0, 0, $width, 0, $color); // Top edge

$maxv = 0;

// Calculate the maximum value we are going to plot

for($i = 0; $i < $columns; $i++)
{
    $maxv = max($values[$i], $maxv);
}


// Now plot each column

for($i = 0; $i < $columns; $i++)
{
    $column_height = (($max_bar_height - $padding) / 100) * (( $values[$i] / $maxv) * 100);

    $x1 = $padding + ( $i * $column_width);
    $y1 = $max_bar_height - $column_height;
    $x2 = $padding + ((($i + 1) * $column_width) - $colpadding);
    $y2 = $max_bar_height;

    imagefilledrectangle($image, $x1, $y1, $x2, $y2, $gray);
    // This part is just for 3D effect
    imageline($image, $x1, $y1, $x1, $y2, $gray_lite);
    imageline($image, $x1, $y2, $x2, $y2, $gray_lite);
    imageline($image, $x2, $y1, $x2, $y2, $gray_dark);

    // X-Axis bar
    imageline($image, $x1, $y2, $x2 + $colpadding, $y2, $black);
}

// Y-Axis bar
imageline($image, $padding, $padding, $padding, $padding + $max_bar_height - $padding, $gray_dark);

// Y-Axis ticks

$tick = 0;

while($tick < $max_y)
{
    $tick += $y_div;
    $tick_y = $margin_top + $max_y - $tick;
    imageline($ih, $margin_left - 3, $tick_y, $margin_left, $tick_y, $black);
}

// Send the PNG header information. Replace for JPEG or GIF or whatever

header("Content-type: image/png");
imagepng($image);
imagedestroy($image);
?>
