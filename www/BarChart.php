<?php

//declare(strict_types = 1); // strict requirement

/*
 *  File Name:    BarChart.php
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
 * Date: 3 Sept 2021
 * ****************************************************************
 */

/**
 * Description of BarChart
 *
 * @author <a href="mailto:bw.opensource@yahoo.com">Bradley Willcott</a>
 */
class BarChart
{

    // The data to gragh
    private $data_series;
    private $max_value;
    // The total number of columns we are going to plot
    private $columns;
    // X-Axis adorments
    private $x_axis_ticks;
    private $x_axis_title;
    private static $x_axis_padding = 10;
    private static $tick_length = 3;
    private static $tick_text_font_size = 8;
    // Y-Axis adornments
    private $y_axis_ticks;
    private $y_axis_title;
    // Image dimensions
    private $height;
    private $width;
    // Graph position
    private $graph_pos_x;
    private $graph_pos_y;
    private $graph_pos_x2;
    private $graph_pos_y2;
    // Graph dimensions
    private $graph_height;
    private $graph_width;
    // Font
    private $font_filename;
    // Graph titles
    private $title;
    private $sub_title;
    // Main padding
    private static $padding = 10;
    private $padding_bottom = 0;
    private $padding_left = 0;
    private $padding_right = 0;
    private $padding_top = 0;
    // Graph margin
    private static $graph_margin = 10;
    private $graph_margin_bottom;
    private $graph_margin_left;
    private $graph_margin_right;
    private $graph_margin_top;
    // Graph padding
    private static $graph_padding = 10;
    private $graph_padding_bottom;
    private $graph_padding_left;
    private $graph_padding_right;
    private $graph_padding_top;
    // The bar dimension settings
    private $max_bar_height;
    private $bar_baseline;
    private $bar_width; // width of 1 bar
    private static $bar_padding = 10;
    // The image being processed
    private $image;
    // The bar colours
    private $bar_fill;
    private $bar_left;
    private $bar_right;
    private $bar_top;
    // The general colours
    private $background;
    private $graph_background;
    private $graph_axis_lines;
    private $text_colour;
    // Colours
    private $black;
    private $grey;
    private $grey_dark;
    private $grey_light;
    private $white;

    // Default constructor
    public function __construct(array $array)
    {
        $this->data_series = $array;
        $this->columns = count($array);

        // Calculate the maximum value we are going to plot
        $maxv = 0;

        for($i = 0; $i < $this->columns; $i++)
        {
            $maxv = max($array[$i], $maxv);
        }

        $this->max_value = $maxv;
        $this->initGraphSettings();
    }

    // Construct and output the graph to the browser.
    // This is the main method - it does, or controls, all the work
    // of producing the final image.
    public function draw(int $width = 300, int $height = 200): void
    {
        // Create the initial image
        $this->image = imagecreate($width, $height);
        $this->setColours();

        $this->drawTitles($width);
        $this->setDimAndPos($width, $height);
        $this->drawBorders($width, $height);
        $this->plotBars();
        $this->drawAxisLines();
        $this->drawAxisTitles();

        // output image and destroy resource
        header("Content-type: image/png");
        imagepng($this->image);
        imagedestroy($this->image);
    }

    // Draw the X and Y axis lines
    private function drawAxisLines()
    {
        // Y-Axis bar
        imageline($this->image, $this->graph_pos_x + $this->graph_padding_left,
                $this->graph_pos_y + $this->graph_padding_top,
                $this->graph_pos_x + $this->graph_padding_left,
                $this->graph_pos_y + $this->graph_padding_top + $this->max_bar_height,
                $this->graph_axis_lines);
    }

    // Draw the boders for both the whoel image and the inner graph
    private function drawBorders(int $width, int $height)
    {
        // set the image border
        imagerectangle($this->image, 0, 0, $width - 1, $height - 1, $this->black);

        // set the graph border and fill
        imagerectangle($this->image, $this->graph_pos_x - 1, $this->graph_pos_y - 1,
                $this->graph_pos_x2 + 1, $this->graph_pos_y2 + 1, $this->black);
        imagefilledrectangle($this->image, $this->graph_pos_x, $this->graph_pos_y,
                $this->graph_pos_x2, $this->graph_pos_y2, $this->graph_background);
    }

    // Draw the Title and Sub-title
    private function drawTitles(int $width)
    {
        if(isset($this->title))
        {
            $centre_x = $width / 2;
            $centre_y = $this->padding_top + ($this->title["font_size"] / 2);
            $this->padding_top += $this->drawText($centre_x, $centre_y,
                    $this->title["text"], $this->title["font_size"]);
        }

        if(isset($this->sub_title))
        {
            $centre_x = $width / 2;
            $centre_y = $this->padding_top + ($this->sub_title["font_size"] / 2);
            $this->padding_top += $this->drawText($centre_x, $centre_y,
                    $this->sub_title["text"], $this->sub_title["font_size"]);
        }
    }

    // Draw the title for each of the graph's axes
    private function drawAxisTitles()
    {
        if(isset($this->x_axis_title))
        {
            $centre_x = $this->graph_pos_x + $this->graph_width / 2;
            $centre_y = $this->bar_baseline + $this->graph_padding_bottom - self::$graph_padding;

            $this->drawText($centre_x, $centre_y, $this->x_axis_title["text"],
                    $this->x_axis_title["font_size"]);
        }

//        if(isset($this->sub_title))
//        {
//            $centre_x = $width / 2;
//            $centre_y = $this->padding_top + ($this->sub_title["font_size"] / 2);
//            $this->padding_top += $this->drawText($centre_x, $centre_y,
//                    $this->sub_title["text"], $this->sub_title["font_size"]);
//        }
    }

    // Initialize the graph settings
    private function initGraphSettings(): void
    {
        $this->graph_margin_bottom = self::$graph_margin;
        $this->graph_margin_left = self::$graph_margin;
        $this->graph_margin_right = self::$graph_margin;
        $this->graph_margin_top = self::$graph_margin;

        $this->graph_padding_bottom = self::$graph_padding;
        $this->graph_padding_left = self::$graph_padding;
        $this->graph_padding_right = self::$graph_padding;
        $this->graph_padding_top = self::$graph_padding;
    }

    // Plot the bars of the graph
    private function plotBars(): void
    {
        // Now plot each bar
        $y2 = $this->bar_baseline;

        for($i = 0; $i < $this->columns; $i++)
        {
            $column_height = ($this->max_bar_height / 100) *
                    (( $this->data_series[$i] / $this->max_value) * 100);

            $x1 = $this->graph_pos_x + $this->graph_padding_left + ( $i * $this->bar_width);
            $y1 = $this->graph_pos_y + $this->graph_padding_top + $this->max_bar_height - $column_height;
            $x2 = $this->graph_pos_x + $this->graph_padding_left + (($i + 1) * $this->bar_width) -
                    self::$bar_padding;

            imagefilledrectangle($this->image, $x1, $y1, $x2, $y2, $this->bar_fill);
            // This part is just for 3D effect
            imageline($this->image, $x1, $y1, $x1, $y2, $this->bar_left);
            imageline($this->image, $x1, $y2, $x2, $y2, $this->bar_top);
            imageline($this->image, $x2, $y1, $x2, $y2, $this->bar_right);

            // X-Axis bar
            imageline($this->image, $x1, $y2, $x2 + self::$bar_padding, $y2, $this->graph_axis_lines);

            // X-Axis ticks
            if(isset($this->x_axis_ticks))
            {
                $x3 = $x1 + (($x2 - $x1) / 2);
                imageline($this->image, $x3, $y2, $x3, $y2 + self::$tick_length, $this->graph_axis_lines);
                $this->drawText($x3, $y2 + self::$x_axis_padding, $this->x_axis_ticks[$i],
                        self::$tick_text_font_size);
            }
        }
    }

    // Get the width and height of the text string as an associative array:
    // $arr["width"], $arr["height"].
    private function getTextWidthHeight(string $text, float $font_size, float $angle = 0): array
    {
        // Create bounding box
        $bbox = imageftbbox($font_size, $angle, $this->font_filename, $text);

        $arr = array();
        $arr["width"] = abs($bbox[0]) + abs($bbox[4]);
        $arr["height"] = abs($bbox[1]) + abs($bbox[5]);
        return $arr;
    }

    // Draw text on graph
    private function drawText(int $centre_x, int $centre_y, string $text, float $font_size,
            float $angle = 0): int
    {
        // Create bounding box
        $bbox = imageftbbox($font_size, $angle, $this->font_filename, $text);

        // Coordinates for x and y
        $text_x = $centre_x - (($bbox[0] + $bbox[4]) / 2);
        $text_y = $centre_y - (($bbox[1] + $bbox[5]) / 2);

        imagefttext($this->image, $font_size, $angle, $text_x, $text_y, $this->text_colour,
                $this->font_filename, $text);

        return abs($bbox[1]) + abs($bbox[5]);
    }

    // Set the colours used for this immage
    private function setColours(): void
    {
        $this->background = isset($this->background) ?
                imagecolorallocate($this->image, $this->background["r"],
                        $this->background["g"], $this->background["b"]) :
                imagecolorallocate($this->image, 255, 255, 255); // white

        $this->white = imagecolorallocate($this->image, 255, 255, 255);
        $this->graph_background = isset($this->graph_background) ?
                imagecolorallocate($this->image, $this->graph_background["r"],
                        $this->graph_background["g"], $this->graph_background["b"]) :
                $this->white;

        $this->grey = imagecolorallocate($this->image, 0xcc, 0xcc, 0xcc);
        $this->bar_fill = $this->grey;
        $this->grey_light = imagecolorallocate($this->image, 0xee, 0xee, 0xee);
        $this->bar_left = $this->grey_light;
        $this->bar_top = $this->bar_left;
        $this->grey_dark = imagecolorallocate($this->image, 0x7f, 0x7f, 0x7f);
        $this->bar_right = $this->grey_dark;
        $this->black = imagecolorallocate($this->image, 0, 0, 0);
        $this->graph_axis_lines = $this->black;
        $this->text_colour = $this->black;
    }

    // Set dimensions and graph position
    private function setDimAndPos(int $width, int $height)
    {
        // Set the various heights
        $this->height = $height;
        $this->graph_height = $height - ($this->padding_bottom + $this->padding_top) -
                ($this->graph_margin_bottom + $this->graph_margin_top) - 2;
        $this->max_bar_height = $this->graph_height - ($this->graph_padding_top + $this->graph_padding_bottom);

        // Set the various widths
        $this->width = $width;
        $this->graph_width = $width - ($this->padding_left + $this->padding_right) -
                ($this->graph_margin_left + $this->graph_margin_right) - 2;
        $this->bar_width = (($this->graph_width - ($this->graph_padding_left +
                $this->graph_padding_right)) / $this->columns);

        // Set the graph position within the image
        $this->graph_pos_x = $this->padding_left + $this->graph_margin_left + 1;
        $this->graph_pos_y = $this->padding_top + $this->graph_margin_top + 1;
        $this->graph_pos_x2 = $this->graph_pos_x + $this->graph_width;
        $this->graph_pos_y2 = $this->graph_pos_y + $this->graph_height;

        $this->bar_baseline = $this->graph_pos_y + $this->graph_padding_top + $this->max_bar_height;
    }

    // Set the background colour
    public function setBackground(int $r = 255, int $g = 255, int $b = 255): BarChart
    {
        $this->background = array("r" => $r, "g" => $g, "b" => $b);

        // Build chaining
        return $this;
    }

    // Set the graph background colour
    public function setFontFilename(string $font_filename): BarChart
    {
        $this->font_filename = $font_filename;

        // Build chaining
        return $this;
    }

    // Set the graph background colour
    public function setGraphBackground(int $r = 255, int $g = 255, int $b = 255): BarChart
    {
        $this->graph_background = array("r" => $r, "g" => $g, "b" => $b);

        // Build chaining
        return $this;
    }

    // Set the main graph sub-title and font size
    public function setSubTitle(string $text, int $font_size): BarChart
    {
        $this->sub_title = array("text" => $text, "font_size" => $font_size);
        $this->padding_top = self::$padding;

        // Build chaining
        return $this;
    }

    // Set the main graph title and font size
    public function setTitle(string $text, int $font_size): BarChart
    {
        $this->title = array("text" => $text, "font_size" => $font_size);
        $this->padding_top = self::$padding;

        // Build chaining
        return $this;
    }

    // Set the x-axis ticks labels
    public function setXAxisTicks(array $ticks): BarChart
    {
        $this->x_axis_ticks = $ticks;
        $this->graph_padding_bottom += self::$graph_padding;

        // Build chaining
        return $this;
    }

    // Set the x-axis title and font size
    public function setXAxisTitle(string $text, int $font_size): BarChart
    {
        $this->x_axis_title = array("text" => $text, "font_size" => $font_size);
        $this->graph_padding_bottom += self::$graph_padding +
                $this->getTextWidthHeight($text, $font_size)["height"];

        // Build chaining
        return $this;
    }

    // Set the y-axis ticks labels
    public function setYAxisTicks(int $increment, array $ticks): BarChart
    {
        $this->y_axis_ticks = array("increment" => $increment, "ticks" => $ticks);

        // Build chaining
        return $this;
    }

    // Set the y-axis title and font size
    public function setYAxisTitle(string $text, int $font_size): BarChart
    {
        $this->y_axis_title = array("text" => $text, "font_size" => $font_size);
        $this->graph_padding_left = self::$graph_padding;

        // Build chaining
        return $this;
    }

    public function toString(): string
    {
        return "Text height: GONE";
    }
}
