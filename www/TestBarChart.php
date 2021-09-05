<?php

/*
 *  File Name:    TestBarChart.php
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

include_once './BarChart.php';
$arr = array();
$x_ticks = array();

// initialize the array
for($index = 0; $index < 10; $index++)
{
    $arr[$index] = 0;
    $x_ticks[$index] = $index + 1;
}

// process 100 random integers: 1 to 10 (inclusively)
for($index = 0; $index < 100; $index++)
{
    $int = random_int(1, 10);
    $arr[$int - 1]++;
}

$png = new BarChart($arr);
$png->setFontFilename("../fonts/LiberationSans-Regular.ttf")
        ->setTitle("Bar Chart Example", 14)
        ->setSubTitle("Sub-title", 12)
        ->setXAxisTitle("Numbers", 10)
        ->setYAxisTitle("Count", 10)
        ->setBackground(0, 200, 0)
        ->setGraphBackground(200, 0, 0)
        ->setXAxisTicks($x_ticks)
        ->draw(400, 300);

