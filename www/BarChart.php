<?php

/*
 *  File Name:    BarChart.php
 *  Project Name: WebProgPHP-Five
 *
 *  Copyright (c) 2021 Bradley Willcott
 *
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
 * Date: 3 Sept 2021
 * ****************************************************************
 */

/**
 * This class provides functionality to generate a Bar Chart.
 * <p>
 * Some source code and ideas were copied from:<br/>
 * https://code.web-max.ca/image_graph.php
 *
 * @author <a href="mailto:bw.opensource@yahoo.com">Bradley Willcott</a>
 *
 * @version v1.0
 */
class BarChart
{

    // The data to gragh
    private $data_series;
    private $max_value;
    // The image being processed
    private $image;
    // The total number of columns we are going to plot
    private $columns;
    // Axis adornments
    private static $tick_length = 3;
    private static $tick_text_font_size = 8;
    // X-Axis adorments
    private $x_axis_ticks;
    private $x_axis_title;
    private static $x_axis_padding = 10;
    // Y-Axis adornments
    private $y_axis_ticks;
    private $y_axis_title;
    private $horizontal_grid_lines;
    private $horizontal_grid_lines_colour;
    private static $y_axis_padding = 10;
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
    private $footer;
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
    private static $bar_padding = 15;
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

    /**
     * Default constructor.
     *
     * @param array $data_series Only tested with an array of integers
     */
    public function __construct(array $data_series)
    {
        $this->data_series = $data_series;
        $this->columns = count($data_series);

        // Calculate the maximum value we are going to plot
        $maxv = 0;

        for($i = 0; $i < $this->columns; $i++)
        {
            $maxv = max($data_series[$i], $maxv);
        }

        $this->max_value = $maxv;
        $this->initGraphSettings();
    }

    /**
     * Construct and output the graph to the browser.
     * <p>
     * This is the main method - it does, or controls, all the work of producing the final image.
     *
     * @param int $width of the new image
     * @param int $height of the new image
     *
     * @return void
     */
    public function draw(int $width = 300, int $height = 200): void
    {
        // Create the initial image
        $this->image = imagecreate($width, $height);
        $this->setColours();

        $this->drawTitles($width, $height);
        $this->setDimAndPos($width, $height);
        $this->drawBorders($width, $height);
        $this->drawAxisLines();
        $this->drawAxisTitles();
        $this->plotBars();

        // output image and destroy resource
        header("Content-type: image/png");
        imagepng($this->image);
        imagedestroy($this->image);
    }

    /**
     * Set the background RGB colour.
     *
     * @param int $r red value
     * @param int $g green value
     * @param int $b blue value
     *
     * @return BarChart for method chaining
     */
    public function setBackgroundColour(int $r = 255, int $g = 255, int $b = 255): BarChart
    {
        $this->background = array("r" => $r, "g" => $g, "b" => $b);

        // Build chaining
        return $this;
    }

    /**
     * Set the bar fill RGB colour.
     *
     * @param int $r red value
     * @param int $g green value
     * @param int $b blue value
     *
     * @return BarChart for method chaining
     */
    public function setBarFillColour(int $r = 255, int $g = 255, int $b = 255): BarChart
    {
        $this->bar_fill = array("r" => $r, "g" => $g, "b" => $b);

        // Build chaining
        return $this;
    }

    /**
     * Set the bar top and left borders RGB colour.
     *
     * @param int $r red value
     * @param int $g green value
     * @param int $b blue value
     *
     * @return BarChart for method chaining
     */
    public function setBarLightColour(int $r = 255, int $g = 255, int $b = 255): BarChart
    {
        $this->bar_left = array("r" => $r, "g" => $g, "b" => $b);

        // Build chaining
        return $this;
    }

    /**
     * Set the bar right border RGB colour.
     *
     * @param int $r red value
     * @param int $g green value
     * @param int $b blue value
     *
     * @return BarChart for method chaining
     */
    public function setBarShadowColour(int $r = 255, int $g = 255, int $b = 255): BarChart
    {
        $this->bar_right = array("r" => $r, "g" => $g, "b" => $b);

        // Build chaining
        return $this;
    }

    /**
     * Set the font file name.
     *
     * @param string $font_filename font file name
     *
     * @return BarChart for method chaining
     */
    public function setFontFilename(string $font_filename): BarChart
    {
        $this->font_filename = $font_filename;

        // Build chaining
        return $this;
    }

    /**
     * Set the main graph footer and font size.
     *
     * @param string $text to display
     * @param int $font_size font size
     *
     * @return BarChart for method chaining
     */
    public function setFooter(string $text, int $font_size): BarChart
    {
        $this->footer = array("text" => $text, "font_size" => $font_size);
        $this->padding_bottom += self::$padding;

        // Build chaining
        return $this;
    }

    /**
     * Set the graph background RGB colour.
     *
     * @param int $r red value
     * @param int $g green value
     * @param int $b blue value
     *
     * @return BarChart for method chaining
     */
    public function setGraphBackgroundColour(int $r = 255, int $g = 255, int $b = 255): BarChart
    {
        $this->graph_background = array("r" => $r, "g" => $g, "b" => $b);

        // Build chaining
        return $this;
    }

    /**
     * Set the horizontal grid lines RGB colour.
     *
     * @param int $r red value
     * @param int $g green value
     * @param int $b blue value
     *
     * @return BarChart for method chaining
     */
    public function setHorizontalGridLinesColour(int $r = 255, int $g = 255, int $b = 255): BarChart
    {
        $this->horizontal_grid_lines_colour = array("r" => $r, "g" => $g, "b" => $b);

        // Build chaining
        return $this;
    }

    /**
     * Set the main graph sub-title and font size.
     *
     * @param string $text to display
     * @param int $font_size font size
     *
     * @return BarChart for method chaining
     */
    public function setSubTitle(string $text, int $font_size): BarChart
    {
        $this->sub_title = array("text" => $text, "font_size" => $font_size);
        $this->padding_top = self::$padding;

        // Build chaining
        return $this;
    }

    /**
     * Set the main graph title and font size.
     *
     * @param string $text to display
     * @param int $font_size font size
     *
     * @return BarChart for method chaining
     */
    public function setTitle(string $text, int $font_size): BarChart
    {
        $this->title = array("text" => $text, "font_size" => $font_size);
        $this->padding_top = self::$padding;

        // Build chaining
        return $this;
    }

    /**
     * Set the x-axis ticks labels.
     *
     * @param array $ticks X-axis tick labels
     *
     * @return BarChart for method chaining
     */
    public function setXAxisTicks(array $ticks): BarChart
    {
        $this->x_axis_ticks = $ticks;
        $this->graph_padding_bottom += self::$graph_padding;

        // Build chaining
        return $this;
    }

    /**
     * Set the x-axis title and font size.
     *
     * @param string $text to display
     * @param int $font_size font size
     *
     * @return BarChart for method chaining
     */
    public function setXAxisTitle(string $text, int $font_size): BarChart
    {
        $this->x_axis_title = array("text" => $text, "font_size" => $font_size);
        $this->graph_padding_bottom += self::$graph_padding +
                $this->getTextWidthHeight($text, $font_size)["height"];

        // Build chaining
        return $this;
    }

    /**
     * Set the y-axis ticks divisor.
     *
     * @param int $divisor Y-axis tick divisor
     * @param bool $horizontal_grid_lines <i>true</i> to display horizontal grid lines<br/>
     * (default: false)
     *
     * @return BarChart for method chaining
     */
    public function setYAxisTicks(int $divisor, bool $horizontal_grid_lines = false): BarChart
    {
        $this->y_axis_ticks = $divisor;
        $this->graph_padding_left += self::$graph_padding;
        $this->horizontal_grid_lines = $horizontal_grid_lines;

        // Build chaining
        return $this;
    }

    /**
     * Set the y-axis title and font size.
     *
     * @param string $text to display
     * @param int $font_size font size
     *
     * @return BarChart for method chaining
     */
    public function setYAxisTitle(string $text, int $font_size): BarChart
    {
        $this->y_axis_title = array("text" => $text, "font_size" => $font_size);
        $this->graph_padding_left += self::$graph_padding +
                $this->getTextWidthHeight($text, $font_size)["height"];

        // Build chaining
        return $this;
    }

    /**
     * Misc info.
     *
     * @return string
     */
    public function toString(): string
    {
        return "Text height: GONE";
    }

    /**
     * Draw the X and Y axis lines.
     *
     * @return void
     */
    private function drawAxisLines(): void
    {
        // Y-Axis bar
        imageline($this->image, $this->graph_pos_x + $this->graph_padding_left,
                $this->graph_pos_y + $this->graph_padding_top,
                $this->graph_pos_x + $this->graph_padding_left,
                $this->graph_pos_y + $this->graph_padding_top + $this->max_bar_height,
                $this->graph_axis_lines);

        if(isset($this->y_axis_ticks))
        {

            $tick = 0;

            while($tick < $this->max_value)
            {
                $tick += $this->y_axis_ticks;
                $column_height = ($this->max_bar_height / 100) *
                        (( $tick / $this->max_value) * 100);
                $tick_y = $this->bar_baseline - $column_height;

                imageline($this->image, $this->graph_pos_x + $this->graph_padding_left - 3,
                        $tick_y, $this->graph_pos_x + $this->graph_padding_left,
                        $tick_y, $this->graph_axis_lines);

                if($this->horizontal_grid_lines)
                {
                    imageline($this->image, $this->graph_pos_x + $this->graph_padding_left + 1,
                            $tick_y,
                            $this->graph_pos_x2 - $this->graph_padding_right, $tick_y,
                            $this->horizontal_grid_lines_colour);
                }

                $this->drawText($this->graph_pos_x + $this->graph_padding_left - self::$x_axis_padding,
                        $tick_y, $tick, self::$tick_text_font_size);
            }
        }
    }

    /**
     * Draw the title for each of the graph's axes.
     *
     * @return void
     */
    private function drawAxisTitles(): void
    {
        if(isset($this->x_axis_title))
        {
            $centre_x = $this->graph_pos_x + $this->graph_width / 2;
            $centre_y = $this->bar_baseline + $this->graph_padding_bottom - self::$graph_padding;

            $this->drawText($centre_x, $centre_y, $this->x_axis_title["text"],
                    $this->x_axis_title["font_size"]);
        }

        if(isset($this->y_axis_title))
        {
            $centre_x = $this->graph_pos_x + self::$y_axis_padding;
            $centre_y = $this->graph_pos_y + $this->graph_padding_top +
                    ($this->max_bar_height / 2);

            $this->drawText($centre_x, $centre_y,
                    $this->y_axis_title["text"], $this->y_axis_title["font_size"], 90);
        }
    }

    /**
     * Draw the borders for both the whole image and the inner graph.
     *
     * @param int $width
     * @param int $height
     *
     * @return void
     */
    private function drawBorders(int $width, int $height): void
    {
        // set the image border
        imagerectangle($this->image, 0, 0, $width - 1, $height - 1, $this->black);

        // set the graph border and fill
        imagerectangle($this->image, $this->graph_pos_x - 1, $this->graph_pos_y - 1,
                $this->graph_pos_x2 + 1, $this->graph_pos_y2 + 1, $this->black);
        imagefilledrectangle($this->image, $this->graph_pos_x, $this->graph_pos_y,
                $this->graph_pos_x2, $this->graph_pos_y2, $this->graph_background);
    }

    /**
     * Draw text on graph.
     *
     * @param int $centre_x
     * @param int $centre_y
     * @param string $text
     * @param float $font_size
     * @param float $angle
     *
     * @return int
     */
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

    /**
     * Draw the Title and Sub-title.
     *
     * @param int $width
     *
     * @return void
     */
    private function drawTitles(int $width, int $height): void
    {
        if(isset($this->title))
        {
            $centre_x = $width / 2;
            $centre_y = $this->padding_top + ($this->title["font_size"] / 2);
            $this->padding_top += $this->drawText($centre_x, $centre_y,
                    $this->title["text"], $this->title["font_size"]);

            if(isset($this->sub_title))
            {
                $centre_x = $width / 2;
                $centre_y = $this->padding_top + ($this->sub_title["font_size"] / 2);
                $this->padding_top += $this->drawText($centre_x, $centre_y,
                        $this->sub_title["text"], $this->sub_title["font_size"]);
            }
        }

        if(isset($this->footer))
        {
            $text = $this->footer["text"];
            $font_size = $this->footer["font_size"];
            $bbox = $this->getTextWidthHeight($text, $font_size);

            $centre_x = $width - $this->padding_right - $this->graph_margin_right - ($bbox["width"] / 2);
            $centre_y = $height - $this->padding_bottom - ($bbox["height"] / 2);
            $this->padding_bottom += $this->drawText($centre_x, $centre_y, $text, $font_size);
        }
    }

    /**
     * Get the width and height of the text string as an associative array:
     * $arr["width"], $arr["height"].
     *
     * @param string $text
     * @param float $font_size
     * @param float $angle
     *
     * @return array
     */
    private function getTextWidthHeight(string $text, float $font_size, float $angle = 0): array
    {
        // Create bounding box
        $bbox = imageftbbox($font_size, $angle, $this->font_filename, $text);

        $arr = array();
        $arr["width"] = abs($bbox[0]) + abs($bbox[4]);
        $arr["height"] = abs($bbox[1]) + abs($bbox[5]);
        return $arr;
    }

    /**
     * Initialize the graph settings.
     *
     * @return void
     */
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

    /**
     * Plot the bars of the graph.
     * @return void
     */
    private function plotBars(): void
    {
        // Now plot each bar
        $y2 = $this->bar_baseline;

        for($i = 0; $i < $this->columns; $i++)
        {
            $column_height = ($this->max_bar_height / 100) *
                    (( $this->data_series[$i] / $this->max_value) * 100);

            $x1 = $this->graph_pos_x + 1 + $this->graph_padding_left + ( $i * $this->bar_width);
            $y1 = $this->graph_pos_y + $this->graph_padding_top + $this->max_bar_height - $column_height;
            $x2 = $this->graph_pos_x + $this->graph_padding_left + (($i + 1) * $this->bar_width) -
                    self::$bar_padding;

            imagefilledrectangle($this->image, $x1, $y1, $x2, $y2, $this->bar_fill);
            // This part is just for 3D effect
            imageline($this->image, $x1, $y1, $x1, $y2, $this->bar_left);
            imageline($this->image, $x1, $y1, $x2, $y1, $this->bar_top);
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

    /**
     * Set the colours used for this image.
     *
     * @return void
     */
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
        $this->bar_fill = isset($this->bar_fill) ?
                imagecolorallocate($this->image, $this->bar_fill["r"],
                        $this->bar_fill["g"], $this->bar_fill["b"]) :
                $this->grey;
        $this->grey_light = imagecolorallocate($this->image, 0xee, 0xee, 0xee);
        $this->bar_left = isset($this->bar_left) ?
                imagecolorallocate($this->image, $this->bar_left["r"],
                        $this->bar_left["g"], $this->bar_left["b"]) :
                $this->grey_light;
        $this->bar_top = $this->bar_left;
        $this->grey_dark = imagecolorallocate($this->image, 0x7f, 0x7f, 0x7f);
        $this->bar_right = $this->grey_dark;
        $this->black = imagecolorallocate($this->image, 0, 0, 0);
        $this->graph_axis_lines = $this->black;
        $this->text_colour = $this->black;
        $this->horizontal_grid_lines_colour = isset($this->horizontal_grid_lines_colour) ?
                imagecolorallocate($this->image, $this->horizontal_grid_lines_colour["r"],
                        $this->horizontal_grid_lines_colour["g"],
                        $this->horizontal_grid_lines_colour["b"]) :
                $this->graph_axis_lines;
    }

    /**
     * Set dimensions and graph position.
     *
     * @param int $width
     * @param int $height
     *
     * @return void
     */
    private function setDimAndPos(int $width, int $height): void
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
}
