# php-fine-uploader


[![Software License](https://img.shields.io/badge/license-GPL-brightgreen.svg?style=flat-square)](LICENSE)
[![Packagist Version](https://img.shields.io/packagist/v/Mehrdad-Dadkhah/php-fine-uploader.svg?style=flat-square)](https://packagist.org/packages/mehrdad-dadkhah/php-fine-uploader)
[![Packagist](https://img.shields.io/packagist/dt/Mehrdad-Dadkhah/php-fine-uploader.svg?style=flat-square&maxAge=259200)](https://packagist.org/packages/Mehrdad-Dadkhah/php-fine-uploader)


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
$uploadHandeler->setUploadName('my-name.mp4');
```
If don't set name your file name be with structur YYYY_m_d_hashname.mp4 and in final resutl generated name will be return.

## Check and generate output directory
If want to script make output directory automatically just set it:
```PHP
$uploadHandeler->checkAndGenerateOutputDirectory();
```
## Temp directory

If want to generate file in a temp directory and then move to main upload directory you can use setTempDirectory() function:

```PHP
$uploadHandeler->setTempDirectory('path-to-temp');
```
## Max upload size

ChunksUploader calculate uploaded file size (sum of chunks) and compare with upload_max_filesize ini config. If want to stop bigger file at first request for better ux can pass total main file size in bytes:

```PHP
$uploadHandeler->setVideoTotalSize(213456);
```

And can overwrite upload_max_filesize by:

```PHP
$uploadHandeler->setMaxUploadSize(213456);
```

## License

hls-video-generater is licensed under the [GPLv3 License](http://opensource.org/licenses/GPL).