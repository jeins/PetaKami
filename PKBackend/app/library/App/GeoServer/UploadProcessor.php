<?php


namespace PetaKami\GeoServer;


use PetaKami\Constants\PKConst;
use Phalcon\Di;
use Phalcon\Di\Injectable;

class UploadProcessor extends Injectable
{
    private $config;

    private $xmlProcessor;

    private $jsonProcessor;

    /**
     * UploadProcessor constructor.
     */
    public function __construct()
    {
        $this->config = Di::getDefault()->get(PKConst::CONFIG);
        $this->xmlProcessor = new XmlProcessor();
        $this->jsonProcessor = new JsonRequestProcessor();
    }

    /**
     * upload file to tmp folder
     * 
     * @param $type
     * @param $key
     */
    public function uploadFileToTmpFolder($type, $key)
    {
        $tmpFolder = $this->config->application->tmpFolder  . $key.'/';

        if($this->request->hasFiles()){
            foreach($this->request->getUploadedFiles() as $file){
                if(!file_exists($tmpFolder)){
                    mkdir($tmpFolder, 0777, true);
                }

                $files = scandir($tmpFolder);
                foreach($files as $fileName){
                    if($type == $this->_splitFilenameFromDrawType($fileName)){
                        unlink($tmpFolder . $fileName);
                    }
                }

                $file->moveTo($tmpFolder . $type.'._.'.$file->getName());
            }
        }
    }

    /**
     * upload file to geoserver
     * 
     * @param $workspace
     * @param $dataStore
     * @param $key
     */
    public function uploadFileToGeoServer($workspace, $dataStore, $key)
    {
        $tmpFolder = $this->config->application->tmpFolder  . $key.'/';
        $files = scandir($tmpFolder);
        $dataStore = strtolower(str_replace(' ','_', $dataStore));

        foreach($files as $file){
            if($file == '.' || $file == '..') continue;

            $fullFilePath = $tmpFolder . '/' . $file;
            $type = $this->_splitFilenameFromDrawType($file);
            $tmpParts = pathinfo($fullFilePath);
            $newName = $type . '_' . $dataStore . '.' .$tmpParts['extension'];

            rename($fullFilePath, $tmpFolder . '/' . $newName);

            switch($tmpParts['extension']){
                case 'zip':
                    $this->xmlProcessor->createDataStore($workspace, $dataStore);
                    $this->xmlProcessor->createLayerFromShp($workspace, $dataStore, file_get_contents($tmpFolder . $newName));
                    break;
                case 'csv':
                    //TODO: Upload CSV
                    break;
                case 'json':
                    //TODO: Upload JSON
                    break;
            }
        }

        $this->_setupLayerGroup($workspace, $dataStore);
    }

    private function _setupLayerGroup($workspace, $dataStore)
    {
        $layerNames = $this->jsonProcessor->layersFromLayerGroup($workspace, $dataStore);
        $this->xmlProcessor->createLayerGroup($workspace, $dataStore, $layerNames);
    }

    private function _splitFilenameFromDrawType($file)
    {
        $tmp = explode('._.', $file);
        return $tmp[0];
    }
}