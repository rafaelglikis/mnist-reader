# Mnist Reader
A php file reader for Yann Lecun's MNIST handwritten digits database. The input data can be found on his site at:
http://yann.lecun.com/exdb/mnist/

## Requirements
* PHP 7.1

## Installation
```shell
composer require rafaelglikis/mnist-reader
```

## Usage
```php
use MnistReader\MnistReader;
use MnistReader\Image;

$mnistReader = new MnistReader("data");

try {
    $mnistReader->loadData();
} catch (Exception $e){
    print $e->getMessage() . "\n";
    die();
}

$images = $mnistReader->getImages();
```    

Access to label
```php
print $images['train'][0]->getLabel();
```

Print Ascii representation or numbers
```php
$images['train'][0]->printAscii();
$images['train'][0]->printChars();
```

Save the image
```php
$images['train'][0]->save('train.png');
```

Get raw data of an image
```php
$pixels = $images['train'][0]->getPixels();
```

Example with test set
```php
print $images['test'][0]->getLabel();
$images['test'][0]->printAscii();
$images['test'][0]->printChars();
$images['test'][0]->save('test.png');
$pixels = $images['test'][0]->getPixels();

```