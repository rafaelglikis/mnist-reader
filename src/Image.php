<?php
namespace MnistReader;


use Exception;

class Image
{
    private const COLUMNS = 28;
    private const ROWS = 28;

    private $pixels = [];
    private $label;

    /**
     * @param $path
     * @throws Exception
     */
    function save($path)
    {
        $img = imagecreatetruecolor(Image::ROWS,Image::COLUMNS);

        // Set each pixel
        for ($row = 0; $row < Image::ROWS; ++$row) {
            for ($col = 0; $col < Image::COLUMNS; ++$col) {
                $color = imagecolorallocate($img, $this->pixels[$row][$col], $this->pixels[$row][$col], $this->pixels[$row][$col]);
                imagesetpixel($img, $col, $row, $color);
            }
        }

        imagepng($img, $path);
    }

    function printChars()
    {
        for ($row=0; $row<Image::ROWS; ++$row) {
            for ($col=0; $col<Image::COLUMNS; ++$col) {
                printf("%d \t", $this->pixels[$row][$col]);
            }
            echo "\n";
        }
    }

    function printAscii()
    {
        for ($row=0; $row<Image::ROWS; ++$row) {
            for ($col=0; $col<Image::COLUMNS; ++$col) {
                if($this->pixels[$row][$col]==0)
                    printf(" ");
                else
                    printf("#");
            }
            echo "\n";
        }
    }

    function setPixel($row, $column, $value)
    {
        $this->pixels[$row][$column] = $value;
    }

    function getPixels() {
        return $this->pixels;
    }

    function setLabel($label)
    {
        $this->label = $label;
    }

    function getLabel(){
        return $this->label;
    }
}