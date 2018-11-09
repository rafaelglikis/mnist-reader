<?php

namespace MnistReader;

use Exception;

class MnistReader
{
    const MAGIC_NUMBER_IMAGES = 2051;
    const MAGIC_NUMBER_LABELS = 2049;
    const ROWS = 28;
    const COLUMNS = 28;

    private $images;

    public function getImages()
    {
        return $this->images;
    }

    private $dataDir;

    function __construct($dataDir = ".")
    {
        $this->dataDir = $dataDir;
    }

    function loadData()
    {
        $files = [
            "train-images-idx3-ubyte",
            "train-labels-idx1-ubyte",
            "t10k-images-idx3-ubyte",
            "t10k-labels-idx1-ubyte"
        ];

        $this->download($files);
        $this->extract($files);

        $this->processDataset($files[0], $files[1], "train");
        $this->processDataset($files[2], $files[3], "test");
    }

    function download($files)
    {
        foreach ($files as $file) {
            if (!file_exists($this->dataDir."/".$file) && !file_exists($this->dataDir."/".$file.".gz")) {
                $url = "http://"."yann.lecun.com/exdb/mnist/".$file.".gz";
                print "Downloading " . $url . "\n";
                $data = file_get_contents($url);
                file_put_contents($this->dataDir."/".$file.".gz", $data);
            }
        }
    }

    function extract($files)
    {
        // Raising this value may increase performance
        $bufferSize= 4096; // read 4kb at a time

        foreach ($files as $file) {
            if (!file_exists($this->dataDir."/".$file)) {
                print "Extracting " . $file . ".gz" . "\n";
                $path = $this->dataDir."/".$file;

                // Open our files (in binary mode)
                $fd = gzopen($path.".gz", 'rb');
                $outfd = fopen($path, 'wb');

                while (!gzeof($fd)) {
                    fwrite($outfd, gzread($fd, $bufferSize));
                }

                fclose($outfd);
                gzclose($fd);
            }
        }
    }

    /**
     * @param $imagesFile
     * @throws Exception
     */
    function processDataset($imagesFile, $labelsFile, $set)
    {
        $labelsHandle = fopen($this->dataDir."/".$labelsFile, "rb");
        if ($labelsHandle === false) {
            throw new Exception("Failed to open stream for " . $this->dataDir."/".$labelsFile);
        }

        $labelsMagicNumber = hexdec(bin2hex(fread($labelsHandle, 4)));
        if ($labelsMagicNumber !== MnistReader::MAGIC_NUMBER_LABELS ) {
            throw new Exception("Invalid labels magic number");
        }

        $imagesHandle = fopen($this->dataDir."/".$imagesFile, "rb");
        if ($imagesHandle === false) {
            throw new Exception("Failed to open stream" . $this->dataDir."/". $imagesFile);
        }

        $imagesMagicNumber = hexdec(bin2hex(fread($imagesHandle, 4)));
        if ($imagesMagicNumber !== MnistReader::MAGIC_NUMBER_IMAGES ) {
            throw new Exception("Invalid images magic number");
        }

        $numberOfLabels = hexdec(bin2hex(fread($labelsHandle, 4)));
        $numberOfImages = hexdec(bin2hex(fread($imagesHandle, 4)));

        if($numberOfLabels !== $numberOfImages) {
            throw new Exception("Label and Images numbers do not match");
        }

        $rows = hexdec(bin2hex(fread($imagesHandle, 4)));
        if ($rows !== MnistReader::ROWS) {
            throw new Exception("Invalid number of rows");
        }

        $columns = hexdec(bin2hex(fread($imagesHandle, 4)));
        if ($columns !== MnistReader::COLUMNS) {
            throw new Exception("Invalid number of columns");
        }


        // Actual processing
        for ($i = 0; $i < $numberOfImages; ++$i) {
            $image = new Image();
            $image->setLabel(hexdec(bin2hex(fread($labelsHandle, 1))));
            for ($row=0; $row<$rows; ++$row) {
                for ($col=0; $col<$columns; ++$col) {
                    $image->setPixel($row, $col,  hexdec(bin2hex(fread($imagesHandle, 1))));
                }
            }
            $this->images[$set][$i] = $image;
        }

        fclose($imagesHandle);
    }
}
