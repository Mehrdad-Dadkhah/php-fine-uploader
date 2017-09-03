# php-fine-uploader


[![Software License](https://img.shields.io/badge/license-GPL-brightgreen.svg?style=flat-square)](LICENSE)
[![Packagist Version](https://img.shields.io/packagist/v/Mehrdad-Dadkhah/php-fine-uploader.svg?style=flat-square)](https://packagist.org/packages/mehrdad-dadkhah/php-fine-uploader)


php lib for use [fine-uploader js](https://fineuploader.com/) as client and upload big files with separate to chunks files.
This libarary is base on [chunks-uploader](https://github.com/Mehrdad-Dadkhah/chunks-uploader).


## Installation

```
composer require mehrdad-dadkhah/php-fine-uploader
```

## Usage

```PHP
use MehrdadDadkhah\Video\FineUploader;

$uploaderService = new FineUploader();
$uploadResult = $uploaderService->setConfigs(
                'form-input-name',
                'path-to-chunks-folder'
            )
                ->setUniqueIdentifier('unique-identifier')
                ->setDomain('http://example.com') //to set cors header
                ->checkDublicateFile(function($uuid) { //closure function to check file is duplicate or not it should get $uuid as input and return boolean. uuid is a video unique hash
                	return false;
                })
                ->upload('path-to-upload-directory');
```

## Custome file name

If want to set output file name try use setUploadName() function before fire finishUpload() function:
```PHP
$uploadResult = $uploadHandeler->setUploadName('my-name.mp4');
```
If don't set name your file name be with structur YYYY_m_d_hashname.mp4 and in final resutl generated name will be return.

## Check and generate output directory
If want to script make output directory automatically just set it:
```PHP
$uploadResult = $uploadHandeler->checkAndGenerateOutputDirectory();
```

## License

hls-video-generater is licensed under the [GPLv3 License](http://opensource.org/licenses/GPL).