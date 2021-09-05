<!DOCTYPE html>
<!--
 File Name:    RandomIntegerReport.php
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
            <?php
            $arr = array();

            // initialize the array
            for($index = 1; $index <= 10; $index++)
            {
                $arr[$index] = 0;
            }

            // process 100 random integers: 1 to 10 (inclusively)
            for($index = 0; $index < 100; $index++)
            {
                $int = random_int(1, 10);
                $arr[$int]++;
            }

            // Build Bar Chart
            ?>
        </div>
    </body>
</html>
