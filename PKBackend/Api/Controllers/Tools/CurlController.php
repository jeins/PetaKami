<?php


namespace PetaKami\Controllers\Tools;

use PetaKami\Controllers\BaseController;

class CurlController extends BaseController
{
    private $_url;

    private $logFile;

    private $requestMethod;

    private $userPass;

    private $requestBody;

    public $responseBody;

    private $successCode = 200;

    private $returnString = true;

    private $verbose = true;

    public function onConstruct()
    {
        $this->_url = $this->di->get('config')->geoserver->REST_URL;
        $this->userPass = 'admin:geoserver'; // nanti pindahin ke yml
        $this->logFile = fopen(__DIR__ . "/../../../Logs/GeoserverPHP.log", 'w') or die("can't open log file");
    }

    public function setUrl($url, $replace = false)
    {
        if($replace) $this->_url = str_replace('/rest', '', $this->_url) . $url;
        else $this->_url .= $url;
    }

    public function setRequestMethod($requestMethod)
    {
        $this->requestMethod = $requestMethod;
    }

    public function setRequestBody($requestBody)
    {
        $this->requestBody = $requestBody;
    }

    public function run()
    {
        $ch = curl_init($this->_url);

        // Optional settings for debugging
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, $this->returnString); //option to return string
        curl_setopt($ch, CURLOPT_VERBOSE, $this->verbose);
        curl_setopt($ch, CURLOPT_STDERR, $this->logFile); // logs curl messages

        curl_setopt($ch, CURLOPT_USERPWD, $this->userPass);

        switch(strtolower($this->requestMethod)){
            case 'get':
                $this->successCode = 200;
                break;
            case 'post':
                $this->successCode = 201;
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: text/xml"));
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->requestBody);
                break;
            case 'put':
                curl_setopt($ch, CURLOPT_PUT, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: text/xml"));
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->requestBody);
                break;
            case 'delete':
                //TODO: belum beresin nih!
                break;
        }

        $responseBody = curl_exec($ch);
        $responseInfo = curl_getinfo($ch);
        $this->_writeLogFile($responseInfo, $responseBody);

        $this->responseBody = [$responseBody];

        curl_close($ch);
        fclose($this->logFile);
        $this->onConstruct();
    }

    private function _writeLogFile($responseInfo, $responseBody)
    {
        if ($responseInfo['http_code'] != $this->successCode) {
            $msgStr = "# Unsuccessful cURL request to ";
            $msgStr .= $this->_url." [". $responseInfo['http_code']. "]\n";
            fwrite($this->logFile, $msgStr);
        } else {
            $msgStr = "# Successful cURL request to ".$this->_url."\n";
            fwrite($this->logFile, $msgStr);
            fwrite($this->logFile, $responseBody."\n");
        }
    }
}