<?php

/**
 *  File Name:    BarChart.php
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
 * Date: 3 Sept 2021
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

/**
 * This class provides functionality to generate a Bar Chart.
 * <p>
 * Some source code and ideas were copied from:<br/>
 * https://code.web-max.ca/image_graph.php
 *
 * @category Charting
 * @package  Charting
 * @author   Bradley Willcott <bw.opensource@yahoo.com>
 * @license  https://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License Version 3
 * @version  Release: v1.0
 * @link     BarChart This class
 */
class BarChart
{
    // The data to gragh
    private $_data_series;
    private $_max_value;
    // The image being processed
    private $_image;
    // The total number of columns we are going to plot
    private $_columns;
    // Axis adornments
    private static $_tick_length = 3;
    private static $_tick_text_font_size = 8;
    // X-Axis adorments
    private $_x_axis_ticks;
    private $_x_axis_title;
    private static $_x_axis_padding = 10;
    // Y-Axis adornments
    private $_y_axis_ticks;
    private $_y_axis_title;
    private $_horizontal_grid_lines;
    private $_horizontal_grid_lines_colour;
    private static $_y_axis_padding = 10;
    // Image dimensions
    private $_height;
    private $_width;
    // Graph position
    private $_graph_pos_x;
    private $_graph_pos_y;
    private $_graph_pos_x2;
    private $_graph_pos_y2;
    // Graph dimensions
    private $_graph_height;
    private $_graph_width;
    // Font
    private $_font_filename;
    // Graph titles
    private $_title;
    private $_sub_title;
    private $_footer;
    // Main padding
    private static $_padding = 10;
    private $_padding_bottom = 0;
    private $_padding_left = 0;
    private $_padding_right = 0;
    private $_padding_top = 0;
    // Graph margin
    private static $_graph_margin = 10;
    private $_graph_margin_bottom;
    private $_graph_margin_left;
    private $_graph_margin_right;
    private $_graph_margin_top;
    // Graph padding
    private static $_graph_padding = 10;
    private $_graph_padding_bottom;
    private $_graph_padding_left;
    private $_graph_padding_right;
    private $_graph_padding_top;
    // The bar dimension settings
    private $_max_bar_height;
    private $_bar_baseline;
    private $_bar_width; // width of 1 bar
    private static $_bar_padding = 15;
    // The bar colours
    private $_bar_fill;
    private $_bar_inner_dark;
    private $_bar_inner_light;
    private $_bar_outer_dark;
    private $_bar_outer_light;
    // The general colours
    private $_background;
    private $_graph_background;
    private $_graph_axis_lines;
    private $_text_colour;
    // Colours
    private $_black;
    private $_grey;
    private $_grey_dark;
    private $_grey_light;
    private $_white;

    /**
     * Default constructor.
     *
     * @param array $data_series Only tested with an array of integers
     */
    public function __construct(array $data_series)
    {
        $this->_data_series = $data_series;
        $this->_columns = count($data_series);

        // Calculate the maximum value we are going to plot
        $maxv = 0;

        for ($i = 0; $i < $this->_columns; $i++) {
            $maxv = max($data_series[$i], $maxv);
        }

        $this->_max_value = $maxv;
        $this->_initGraphSettings();
    }

    /**
     * Construct and output the graph to the browser.
     * <p>
     * This is the main method - it does, or controls, all the work of producing the final image.
     *
     * @param int $width  of the new image
     * @param int $height of the new image
     *
     * @return void
     */
    public function draw(int $width = 300, int $height = 200): void
    {
        // Create the initial image
        $this->_image = imagecreate($width, $height);
        $this->_setColours();

        $this->_drawTitles($width, $height);
        $this->_setDimAndPos($width, $height);
        $this->_drawBorders($width, $height);
        $this->_drawAxisLines();
        $this->_drawAxisTitles();
        $this->_drawBars();

        // output image and destroy resource
        header("Content-type: image/png");
        imagepng($this->_image);
        imagedestroy($this->_image);
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
        $this->_background = array("r" => $r, "g" => $g, "b" => $b);

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
        $this->_bar_fill = array("r" => $r, "g" => $g, "b" => $b);

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
        $this->_font_filename = $font_filename;

        // Build chaining
        return $this;
    }

    /**
     * Set the main graph footer and font size.
     *
     * @param string $text      to display
     * @param int    $font_size font size
     *
     * @return BarChart for method chaining
     */
    public function setFooter(string $text, int $font_size): BarChart
    {
        $this->_footer = array("text" => $text, "font_size" => $font_size);
        $this->_padding_bottom += self::$_padding;

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
        $this->_graph_background = array("r" => $r, "g" => $g, "b" => $b);

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
        $this->_horizontal_grid_lines_colour = array("r" => $r, "g" => $g, "b" => $b);

        // Build chaining
        return $this;
    }

    /**
     * Set the main graph sub-title and font size.
     *
     * @param string $text      to display
     * @param int    $font_size font size
     *
     * @return BarChart for method chaining
     */
    public function setSubTitle(string $text, int $font_size): BarChart
    {
        $this->_sub_title = array("text" => $text, "font_size" => $font_size);
        $this->_padding_top = self::$_padding;

        // Build chaining
        return $this;
    }

    /**
     * Set the main graph title and font size.
     *
     * @param string $text      to display
     * @param int    $font_size font size
     *
     * @return BarChart for method chaining
     */
    public function setTitle(string $text, int $font_size): BarChart
    {
        $this->_title = array("text" => $text, "font_size" => $font_size);
        $this->_padding_top = self::$_padding;

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
        $this->_x_axis_ticks = $ticks;
        $this->_graph_padding_bottom += self::$_graph_padding;

        // Build chaining
        return $this;
    }

    /**
     * Set the x-axis title and font size.
     *
     * @param string $text      to display
     * @param int    $font_size font size
     *
     * @return BarChart for method chaining
     */
    public function setXAxisTitle(string $text, int $font_size): BarChart
    {
        $this->_x_axis_title = array("text" => $text, "font_size" => $font_size);
        $this->_graph_padding_bottom += self::$_graph_padding +
                $this->_getTextWidthHeight($text, $font_size)["height"];

        // Build chaining
        return $this;
    }

    /**
     * Set the y-axis ticks divisor.
     *
     * @param int  $divisor               Y-axis tick divisor
     * @param bool $horizontal_grid_lines <i>true</i> to display horizontal grid lines<br/>
     *                                    (default: false)
     *
     * @return BarChart for method chaining
     */
    public function setYAxisTicks(int $divisor, bool $horizontal_grid_lines = false): BarChart
    {
        $this->_y_axis_ticks = $divisor;
        $this->_graph_padding_left += self::$_graph_padding;
        $this->_horizontal_grid_lines = $horizontal_grid_lines;

        // Build chaining
        return $this;
    }

    /**
     * Set the y-axis title and font size.
     *
     * @param string $text      to display
     * @param int    $font_size font size
     *
     * @return BarChart for method chaining
     */
    public function setYAxisTitle(string $text, int $font_size): BarChart
    {
        $this->_y_axis_title = array("text" => $text, "font_size" => $font_size);
        $this->_graph_padding_left += self::$_graph_padding +
                $this->_getTextWidthHeight($text, $font_size)["height"];

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
    private function _drawAxisLines(): void
    {
        // Y-Axis bar
        imagesetthickness($this->_image, 2);
        imageline(
            $this->_image,
            $this->_graph_pos_x + $this->_graph_padding_left,
            $this->_graph_pos_y + $this->_graph_padding_top,
            $this->_graph_pos_x + $this->_graph_padding_left,
            $this->_graph_pos_y + $this->_graph_padding_top + $this->_max_bar_height + 1,
            $this->_graph_axis_lines
        );
        imagesetthickness($this->_image, 1);

        if (isset($this->_y_axis_ticks)) {
            $tick = 0;

            while ($tick < $this->_max_value) {
                $tick += $this->_y_axis_ticks;
                $column_height = ($this->_max_bar_height / 100) *
                        (($tick / $this->_max_value) * 100);
                $tick_y = $this->_bar_baseline - $column_height;

                imageline(
                    $this->_image,
                    $this->_graph_pos_x + $this->_graph_padding_left - 4,
                    $tick_y,
                    $this->_graph_pos_x + $this->_graph_padding_left,
                    $tick_y,
                    $this->_graph_axis_lines
                );

                if ($this->_horizontal_grid_lines) {
                    imageline(
                        $this->_image,
                        $this->_graph_pos_x + $this->_graph_padding_left + 1,
                        $tick_y,
                        $this->_graph_pos_x2 - $this->_graph_padding_right,
                        $tick_y,
                        $this->_horizontal_grid_lines_colour
                    );
                }

                $this->_drawText(
                    $this->_graph_pos_x + $this->_graph_padding_left - self::$_x_axis_padding - 1,
                    $tick_y,
                    $tick,
                    self::$_tick_text_font_size
                );
            }
        }
    }

    /**
     * Draw the title for each of the graph's axes.
     *
     * @return void
     */
    private function _drawAxisTitles(): void
    {
        if (isset($this->_x_axis_title)) {
            $centre_x = $this->_graph_pos_x + $this->_graph_width / 2;
            $centre_y = $this->_bar_baseline + $this->_graph_padding_bottom - self::$_graph_padding;

            $this->_drawText(
                $centre_x,
                $centre_y,
                $this->_x_axis_title["text"],
                $this->_x_axis_title["font_size"]
            );
        }

        if (isset($this->_y_axis_title)) {
            $centre_x = $this->_graph_pos_x + self::$_y_axis_padding;
            $centre_y = $this->_graph_pos_y + $this->_graph_padding_top +
                    ($this->_max_bar_height / 2);

            $this->_drawText(
                $centre_x,
                $centre_y,
                $this->_y_axis_title["text"],
                $this->_y_axis_title["font_size"],
                90
            );
        }
    }

    /**
     * Draw the bars of the graph.
     *
     * @return void
     */
    private function _drawBars(): void
    {
        // Now plot each bar
        $y2 = $this->_bar_baseline;

        for ($i = 0; $i < $this->_columns; $i++) {
            $column_height = ($this->_max_bar_height / 100) *
                    (($this->_data_series[$i] / $this->_max_value) * 100);

            $x1 = $this->_graph_pos_x + 1 + $this->_graph_padding_left + ($i * $this->_bar_width);
            $y1 = $this->_graph_pos_y + $this->_graph_padding_top + $this->_max_bar_height - $column_height;
            $x2 = $this->_graph_pos_x + $this->_graph_padding_left + (($i + 1) * $this->_bar_width) -
                    self::$_bar_padding;

            imagefilledrectangle($this->_image, $x1, $y1, $x2, $y2, $this->_bar_fill);
            // This part is just for 3D effect
            // Outer lines
            imageline($this->_image, $x1, $y1, $x1, $y2, $this->_bar_outer_light); // left
            imageline($this->_image, $x1, $y1, $x2, $y1, $this->_bar_outer_light); // top
            imageline($this->_image, $x2, $y1 + 1, $x2, $y2, $this->_bar_outer_dark); // right
            // Inner lines
            imageline($this->_image, $x1 + 1, $y1 + 1, $x1 + 1, $y2 - 1, $this->_bar_inner_light); // left
            imageline($this->_image, $x1 + 1, $y1 + 1, $x2 - 1, $y1 + 1, $this->_bar_inner_light); // top
            imageline($this->_image, $x2 - 1, $y1 + 2, $x2 - 1, $y2 - 1, $this->_bar_inner_dark); // right
            imageline($this->_image, $x1 + 1, $y2 - 1, $x2 - 1, $y2 - 1, $this->_bar_inner_dark); // bottom
            // X-Axis bar
            imagesetthickness($this->_image, 2);
            imageline($this->_image, $x1, $y2 + 1, $x2 + self::$_bar_padding, $y2 + 1, $this->_graph_axis_lines);
            imagesetthickness($this->_image, 1);

            // X-Axis ticks
            if (isset($this->_x_axis_ticks)) {
                $x3 = $x1 + (($x2 - $x1) / 2);
                imageline($this->_image, $x3, $y2 + 1, $x3, $y2 + 1 + self::$_tick_length, $this->_graph_axis_lines);
                $this->_drawText(
                    $x3,
                    $y2 + 1 + self::$_x_axis_padding,
                    $this->_x_axis_ticks[$i],
                    self::$_tick_text_font_size
                );
            }
        }
    }

    /**
     * Draw the borders for both the whole image and the inner graph.
     *
     * @param int $width  of image
     * @param int $height of image
     *
     * @return void
     */
    private function _drawBorders(int $width, int $height): void
    {
        // set the image border
        imagerectangle($this->_image, 0, 0, $width - 1, $height - 1, $this->_black);

        // set the graph border and fill
        imagerectangle(
            $this->_image,
            $this->_graph_pos_x - 1,
            $this->_graph_pos_y - 1,
            $this->_graph_pos_x2 + 1,
            $this->_graph_pos_y2 + 1,
            $this->_black
        );
        imagefilledrectangle(
            $this->_image,
            $this->_graph_pos_x,
            $this->_graph_pos_y,
            $this->_graph_pos_x2,
            $this->_graph_pos_y2,
            $this->_graph_background
        );
    }

    /**
     * Draw text on graph.
     *
     * @param int    $centre_x  centre X
     * @param int    $centre_y  centre Y
     * @param string $text      to display
     * @param float  $font_size font size
     * @param float  $angle     angle
     *
     * @return int   height of text box
     */
    private function _drawText(int $centre_x, int $centre_y, string $text, float $font_size, float $angle = 0): int
    {
        // Create bounding box
        $bbox = imageftbbox($font_size, $angle, $this->_font_filename, $text);

        // Coordinates for x and y
        $text_x = $centre_x - (($bbox[0] + $bbox[4]) / 2);
        $text_y = $centre_y - (($bbox[1] + $bbox[5]) / 2);

        imagefttext(
            $this->_image,
            $font_size,
            $angle,
            $text_x,
            $text_y,
            $this->_text_colour,
            $this->_font_filename,
            $text
        );

        return abs($bbox[1]) + abs($bbox[5]);
    }

    /**
     * Draw the Title and Sub-title.
     *
     * @param int $width  of image
     * @param int $height of image
     *
     * @return void
     */
    private function _drawTitles(int $width, int $height): void
    {
        if (isset($this->_title)) {
            $centre_x = $width / 2;
            $centre_y = $this->_padding_top + ($this->_title["font_size"] / 2);
            $this->_padding_top += $this->_drawText(
                $centre_x,
                $centre_y,
                $this->_title["text"],
                $this->_title["font_size"]
            );

            if (isset($this->_sub_title)) {
                $centre_x = $width / 2;
                $centre_y = $this->_padding_top + ($this->_sub_title["font_size"] / 2);
                $this->_padding_top += $this->_drawText(
                    $centre_x,
                    $centre_y,
                    $this->_sub_title["text"],
                    $this->_sub_title["font_size"]
                );
            }
        }

        if (isset($this->_footer)) {
            $text = $this->_footer["text"];
            $font_size = $this->_footer["font_size"];
            $bbox = $this->_getTextWidthHeight($text, $font_size);

            $centre_x = $width - $this->_padding_right - $this->_graph_margin_right - ($bbox["width"] / 2);
            $centre_y = $height - $this->_padding_bottom - ($bbox["height"] / 2);
            $this->_padding_bottom += $this->_drawText($centre_x, $centre_y, $text, $font_size);
        }
    }

    /**
     * Get the width and height of the text string as an associative array:
     * $arr["width"], $arr["height"].
     *
     * @param string $text      to display
     * @param float  $font_size font size
     * @param float  $angle     angle
     *
     * @return array containing "width" and "height"
     */
    private function _getTextWidthHeight(string $text, float $font_size, float $angle = 0): array
    {
        // Create bounding box
        $bbox = imageftbbox($font_size, $angle, $this->_font_filename, $text);

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
    private function _initGraphSettings(): void
    {
        $this->_graph_margin_bottom = self::$_graph_margin;
        $this->_graph_margin_left = self::$_graph_margin;
        $this->_graph_margin_right = self::$_graph_margin;
        $this->_graph_margin_top = self::$_graph_margin;

        $this->_graph_padding_bottom = self::$_graph_padding;
        $this->_graph_padding_left = self::$_graph_padding;
        $this->_graph_padding_right = self::$_graph_padding;
        $this->_graph_padding_top = self::$_graph_padding;
    }

    /**
     * Set the colours used for this image.
     *
     * @return void
     */
    private function _setColours(): void
    {
        $this->_background = isset($this->_background) ?
                imagecolorallocate(
                    $this->_image,
                    $this->_background["r"],
                    $this->_background["g"],
                    $this->_background["b"]
                ) :
                imagecolorallocate($this->_image, 255, 255, 255); // white

        $this->_black = imagecolorallocate($this->_image, 0, 0, 0);
        $this->_grey = imagecolorallocate($this->_image, 204, 204, 204);
        $this->_grey_light = imagecolorallocate($this->_image, 238, 238, 238);
        $this->_grey_dark = imagecolorallocate($this->_image, 0x7f, 0x7f, 0x7f);
        $this->_white = imagecolorallocate($this->_image, 255, 255, 255);

        $this->_graph_background = isset($this->_graph_background) ?
                imagecolorallocate(
                    $this->_image,
                    $this->_graph_background["r"],
                    $this->_graph_background["g"],
                    $this->_graph_background["b"]
                ) :
                $this->_white;

        $outer_lighten = 60;
        $outer_darken = -60;
        $inner_lighten = 30;
        $inner_darken = -30;
        $max = 255;
        $alt = 204;

        if (isset($this->_bar_fill)) {
            $red = $this->_bar_fill["r"];
            $green = $this->_bar_fill["g"];
            $blue = $this->_bar_fill["b"];
        } else {
            $red = $alt;
            $green = $alt;
            $blue = $alt;
        }

        $red_inner_light = $red + $inner_lighten < $max ? $red + $inner_lighten : $max;
        $green_inner_light = $green + $inner_lighten < $max ? $green + $inner_lighten : $max;
        $blue_inner_light = $blue + $inner_lighten < $max ? $blue + $inner_lighten : $max;
        $red_inner_dark = $red + $inner_darken > 0 ? $red + $inner_darken : 0;
        $green_inner_dark = $green + $inner_darken > 0 ? $green + $inner_darken : 0;
        $blue_inner_dark = $blue + $inner_darken > 0 ? $blue + $inner_darken : 0;

        $red_outer_light = $red + $outer_lighten < $max ? $red + $outer_lighten : $max;
        $green_outer_light = $green + $outer_lighten < $max ? $green + $outer_lighten : $max;
        $blue_outer_light = $blue + $outer_lighten < $max ? $blue + $outer_lighten : $max;
        $red_outer_dark = $red + $outer_darken > 0 ? $red + $outer_darken : 0;
        $green_outer_dark = $green + $outer_darken > 0 ? $green + $outer_darken : 0;
        $blue_outer_dark = $blue + $outer_darken > 0 ? $blue + $outer_darken : 0;

        $this->_bar_inner_dark = imagecolorallocate($this->_image, $red_inner_dark, $green_inner_dark, $blue_inner_dark);
        $this->_bar_inner_light = imagecolorallocate($this->_image, $red_inner_light, $green_inner_light, $blue_inner_light);
        $this->_bar_outer_dark = imagecolorallocate($this->_image, $red_outer_dark, $green_outer_dark, $blue_outer_dark);
        $this->_bar_outer_light = imagecolorallocate($this->_image, $red_outer_light, $green_outer_light, $blue_outer_light);

        $this->_bar_fill = isset($this->_bar_fill) ?
                imagecolorallocate(
                    $this->_image,
                    $this->_bar_fill["r"],
                    $this->_bar_fill["g"],
                    $this->_bar_fill["b"]
                ) :
                $this->_grey;

        $this->_graph_axis_lines = $this->_black;
        $this->_text_colour = $this->_black;
        $this->_horizontal_grid_lines_colour = isset($this->_horizontal_grid_lines_colour) ?
                imagecolorallocate(
                    $this->_image,
                    $this->_horizontal_grid_lines_colour["r"],
                    $this->_horizontal_grid_lines_colour["g"],
                    $this->_horizontal_grid_lines_colour["b"]
                ) :
                $this->_graph_axis_lines;
    }

    /**
     * Set dimensions and graph position.
     *
     * @param int $width  of image
     * @param int $height of image
     *
     * @return void
     */
    private function _setDimAndPos(int $width, int $height): void
    {
        // Set the various heights
        $this->_height = $height;
        $this->_graph_height = $height - ($this->_padding_bottom + $this->_padding_top) -
                ($this->_graph_margin_bottom + $this->_graph_margin_top) - 2;
        $this->_max_bar_height = $this->_graph_height - ($this->_graph_padding_top + $this->_graph_padding_bottom);

        // Set the various widths
        $this->_width = $width;
        $this->_graph_width = $width - ($this->_padding_left + $this->_padding_right) -
                ($this->_graph_margin_left + $this->_graph_margin_right) - 2;
        $this->_bar_width = (($this->_graph_width - ($this->_graph_padding_left +
                $this->_graph_padding_right)) / $this->_columns);

        // Set the graph position within the image
        $this->_graph_pos_x = $this->_padding_left + $this->_graph_margin_left + 1;
        $this->_graph_pos_y = $this->_padding_top + $this->_graph_margin_top + 1;
        $this->_graph_pos_x2 = $this->_graph_pos_x + $this->_graph_width;
        $this->_graph_pos_y2 = $this->_graph_pos_y + $this->_graph_height;

        $this->_bar_baseline = $this->_graph_pos_y + $this->_graph_padding_top + $this->_max_bar_height;
    }
}
