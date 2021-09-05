<!DOCTYPE html>
<!--
 File Name:    index.php
 Project Name: WebProgPHP-Five

 Copyright (c) 2021 Bradley Willcott

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.

****************************************************************
Name: Bradley Willcott
ID:   M198449
Date: 3 Sept 2021
****************************************************************
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>WebProgPHP-Five</title>
    </head>
    <body>
        <div>
            <h1>Random Integer Report</h1>
        </div>
        <div>
            <img src="TestBarChart.php" title="Bar Chart Example" alt="Bar Chart Example" />
            <p>
                <img src="Test2.php" title="Bar Test 2" alt="Bar Test 2" />
            </p>
            <?php
            require_once 'BarChart.php';
            $array = array(1, 2, 3, 4, 5);

            $bc = new BarChart($array);
            $bc->setFontFilename("../fonts/LiberationSans-Regular.ttf")
                    ->setXAxisTitle("Fred", 10);
            $text = $bc->toString();
            echo "toString(): $text<br/>";

            // Get the width and height of the text string as an associative array:
            // $arr["width"], $arr["height"].
            function getTextWidthHeight(string $text, float $font_size, float $angle = 0): array
            {
                // Create bounding box
                $bbox = imageftbbox($font_size, $angle, "../fonts/LiberationSans-Regular.ttf", $text);

                $arr = array();
                $arr["width"] = abs($bbox[0]) + abs($bbox[4]);
                $arr["height"] = abs($bbox[1]) + abs($bbox[5]);
                return $arr;
            }
            $arr = getTextWidthHeight("Hi there!", 10)["height"];
            print_r($arr);
            ?>
        </div>
    </body>
</html>
