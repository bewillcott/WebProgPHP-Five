<?php

/**
 *  File Name:    TestBarChart.php
 *  Project Name: WebProgPHP-Five
 *
 * PHP version 8
 *
 * LICENSE:
 *  This code is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This code is distributed in the hope that it will be useful,
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
 *
 * @category  Charting
 * @package   Charting
 * @author    Bradley Willcott <bw.opensource@yahoo.com>
 * @copyright 2021 Bradley Willcott
 * @license   https://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License Version 3
 * @version   Release: v1.0
 * @link      BarChart This class
 */
require_once './BarChart.php';
$arr = array();
$x_ticks = array();

$width = filter_has_var(INPUT_GET, "width") ? filter_input(INPUT_GET, "width") : 400;
$height = filter_has_var(INPUT_GET, "height") ? filter_input(INPUT_GET, "height") : 300;

// initialize the array
for ($index = 0; $index < 10; $index++) {
    $arr[$index] = 0;
    $x_ticks[$index] = $index + 1;
}

// process 100 random integers: 1 to 10 (inclusively)
for ($index = 0; $index < 100; $index++) {
    $int = random_int(1, 10);
    $arr[$int - 1]++;
}

$year = date("Y");

$png = new BarChart($arr);
$png->setFontFilename("../fonts/LiberationSans-Regular.ttf")
    ->setTitle("Web Programming PHP - Five", 14)
    ->setSubTitle("Random Integer Report", 12)
    ->setXAxisTitle("Numbers", 10)
    ->setYAxisTitle("Count", 10)
    ->setFooter("Copyright © {$year} Bradley Willcott (M198449)", 8)
    ->setBackgroundColour(0, 255, 150)
    ->setGraphBackgroundColour(128, 255, 255)
    ->setHorizontalGridLinesColour(200, 200, 200)
    ->setBarFillColour(172, 57, 0)
    ->setXAxisTicks($x_ticks)
    ->setYAxisTicks(1, true)
    ->draw($width, $height);
