<?php


namespace PetaKami\GeoServer;


class Curl
{

    private $_url;

    private $httpMethod;

    private $requestBody;

    private $responseBody;

    private $successCode;

    private $returnString;

    private $verbose;

    private $config;

    public function __construct($config)
    {
        $this->config = $config;
        $this->_url = $this->config->geoserver->rest_url;
        $this->verbose = true;
        $this->returnString = true;
    }

    public function setUrl($url, $replace = false)
    {
        if($replace) $this->_url = str_replace('/rest', '', $this->_url) . $url;
        else $this->_url .= $url;
    }

    public function setHttpMethod($httpMethod)
    {
        $this->httpMethod = $httpMethod;
    }

    public function setRequestBody($requestBody)
    {
        $this->requestBody = $requestBody;
    }

    public function getResponseBody(){
        return $this->responseBody;
    }

    public function run()
    {
        $ch = curl_init($this->_url);

        // Optional settings for debugging
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, $this->returnString); //option to return string
        curl_setopt($ch, CURLOPT_VERBOSE, $this->verbose);

        curl_setopt($ch, CURLOPT_USERPWD, $this->config->geoserver->username .':'. $this->config->geoserver->password);

        switch(strtolower($this->httpMethod)){
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
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/zip"));
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->requestBody);
                break;
            case 'delete':
                //TODO: belum beres nih!
                break;
        }

        $this->responseBody = curl_exec($ch);

        curl_close($ch);
        $this->_url = $this->config->geoserver->rest_url;
    }
}