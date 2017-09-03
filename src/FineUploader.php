<?php
namespace MehrdadDadkhah\Video;

class FineUploader
{
    private $uploader;
    private $requestMethod;
    private $uniqueIdentifier;
    private $checkDuplicate = false;
    private $checkDublicateFileMethod;
    private $domain;

    public function __construct()
    {
        $this->uploader       = new ChunksUploader();
        $this->requestMethod  = $this->get_request_method();
        $this->requestHeaders = $this->parseRequestHeaders();
    }

    public function setConfigs(int $sizeLimit, string $inputName, string $chunksFolder)
    {
        $this->uploader->setMainFileName($_REQUEST['qqfilename'])
            ->setFileTotalSize($_REQUEST['qqtotalfilesize'])
            ->setInputName($inputName)
            ->setChunksFolderPath($chunksFolder);

        return $this;
    }

    public function setUniqueIdentifier(string $identifier)
    {
        $this->uniqueIdentifier = $identifier;

        $this->uploader->setUniqueIdentifier($this->uniqueIdentifier);

        return $this;
    }

    public function setUploadName(strign $fileName)
    {
        $this->uploader->setUploadName($fileName);

        return $this;
    }

    public function checkAndGenerateOutputDirectory()
    {
        $this->uploader->checkAndGenerateOutputDirectory();

        return $this;
    }

    public function upload($uploadDirectory)
    {
        if ($this->requestMethod != "POST") {
            header("HTTP/1.0 405 Method Not Allowed");
            return;
        }

        $this->handleCorsRequest();
        header("Content-Type: text/plain");

        // Assumes you have a chunking.success.endpoint set to point here with a query parameter of "done".
        // For example: /myserver/handlers/endpoint.php?done
        if (isset($_GET["done"])) {
            $result = $this->uploader->setUploadDirectory($uploadDirectory)
                ->finishUpload();

            $result['success'] = $result['status'];
            $result['status']  = ($result['status'] ? 0 : 1);

            if ($result['success'] && $this->checkDuplicate($result['uuid'])) {
                unlink($uploadDirectory . '/' . $result['uplodedName']);

                $result = [
                    'error'    => 5,
                    "uuid"     => $result['uuid'],
                    'status'   => -5,
                    'messages' => 'duplicated',
                ];
            }

            unset($result['chunksSubDirectryPath']);
            // return $result;
            echo json_encode($result);

        }
        // Handles upload requests
        else {

            // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
            $result = $this->uploader->uploadChunk($_REQUEST['qqpartindex']);

            $result['success'] = $result['status'];

            // To return a name used for uploaded file you can use the following line.

            // iframe uploads require the content-type to be 'text/html' and
            // return some JSON along with self-executing javascript (iframe.ss.response)
            // that will parse the JSON and pass it along to Fine Uploader via
            // window.postMessage
            if ($this->checkIframe()) {
                header("Content-Type: text/html");

                echo json_encode($result) . "<script src='http://jabeh.com/assets/js/iframe.xss.response.js'></script>";

            } else {
                echo json_encode($result);
                // return $result;
            }

        }
    }

    private function checkIframe(): bool
    {
        if (!isset($this->requestHeaders['X-Requested-With']) || $this->requestHeaders['X-Requested-With'] != "XMLHttpRequest") {
            return true;
        }

        return false;
    }

    private function get_request_method()
    {
        global $HTTP_RAW_POST_DATA;
        // This should only evaluate to true if the Content-Type is undefined
        // or unrecognized, such as when XDomainRequest has been used to
        // send the request.
        if (isset($HTTP_RAW_POST_DATA)) {
            parse_str($HTTP_RAW_POST_DATA, $_POST);
        }
        if (isset($_POST["_method"]) && $_POST["_method"] != null) {
            return $_POST["_method"];
        }
        return $_SERVER["REQUEST_METHOD"];
    }

    private function parseRequestHeaders()
    {
        $headers = array();
        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) != 'HTTP_') {
                continue;
            }
            $header           = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
            $headers[$header] = $value;
        }
        return $headers;
    }

    private function handleCorsRequest()
    {
        header('Access-Control-Allow-Origin: ' . $this->domain);
    }

    public function setDomain(string $domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /*
     * handle pre-flighted requests. Needed for CORS operation
     */
    private function handlePreflight()
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, X-Requested-With, Cache-Control");
    }

    public function checkDublicateFile($method)
    {
        $this->checkDuplicate = true;

        $this->checkDublicateFileMethod = $method;

        return $this;
    }

    private function checkDuplicate(string $uuid): bool
    {
        $method = $this->checkDublicateFileMethod;
        return $method($uuid);
    }
}
