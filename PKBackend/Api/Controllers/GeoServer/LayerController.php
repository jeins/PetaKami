<?php


namespace PetaKami\Controllers\GeoServer;

use PetaKami\Controllers\BaseController;
use PetaKami\Controllers\Tools\CurlController;
use PetaKami\Controllers\Tools\QueryController;
use PetaKami\Processors\XmlRequestProcessor;

class LayerController extends BaseController
{
    private $queryBuilder;

    private $xmlRequestProcessor;

    private $curlController;

    public function onConstruct()
    {
        $this->xmlRequestProcessor = new XmlRequestProcessor();
        $this->queryBuilder = new QueryController();
        $this->curlController = new CurlController();
    }

    public function getLayersInGeoJSON($workspace, $layerGroupName)
    {
        $layers = $this->getLayersWithDrawType($workspace, $layerGroupName);

        $featureTypes = [];

        foreach($layers as $layer){
            $this->curlController->setUrl('/'.$workspace.
                '/ows?service=WFS&version=1.0.0&request=GetFeature&typeName='
                .$workspace.':'.$layer.
                '&maxFeatures=50&outputFormat=application%2Fjson', true);
            $this->curlController->run();
            array_push($featureTypes, [$layer => json_decode($this->curlController->responseBody[0])]);
        }

        return $featureTypes;
    }

    public function getBBox($workspace, $layerGroup){
        $this->curlController->setUrl('/wms/reflect?format=application/openlayers&layers=' .  $workspace . ':' . $layerGroup, true);
        $this->curlController->setRequestMethod = 'GET';
        $this->curlController->run();
        $responses = preg_replace('/\s+/', '', $this->get_string_between($this->curlController->responseBody[0], 'OpenLayers.Bounds(', ');'));

        return explode(',', $responses);
    }

    function get_string_between($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    public function getLayerDrawTypeInGeoJSON($workspace, $layerGroupName, $drawType)
    {
        $dTypes = explode('_', str_replace('d', '', $drawType));

        $isFirst = true;
        $geoJson = [];
        foreach($dTypes as $dType){
            if($dType != ""){
                if($dType == 'p') $dType = 'point';
                if($dType == 'l') $dType = 'line';
                if($dType == 'pl') $dType = 'poly';

                $this->curlController->setUrl('/'.$workspace.
                    '/ows?service=WFS&version=1.0.0&request=GetFeature&typeName='
                    .$workspace.':'.$layerGroupName.'_'.$dType.
                    '&maxFeatures=50&outputFormat=application%2Fjson', true);
                $this->curlController->run();
                $response = json_decode($this->curlController->responseBody[0]);

                if($isFirst){
                    $geoJson = $response;
                    $isFirst = false;
                } else{
                    $geoJson->features = array_merge($response->features, $geoJson->features);
                }
            }
        }

        return $geoJson;
    }

    public function getLayerByWorkspace($workspace)
    {
        $this->curlController->setUrl('/workspaces/'.$workspace.'/layergroups.json');
        $this->curlController->run();

        $responses = json_decode($this->curlController->responseBody[0]);

        $newLayerGroups = [];
        foreach($responses->layerGroups->layerGroup as $response){
            $drawTypes = $this->getLayersWithDrawType($workspace, $response->name);
            $drawType = '';
            foreach($drawTypes as $dType){
                $dType = explode('_', $dType);
                $drawType .=  $dType[count($dType)-1].'_';
            }
            $drawType = rtrim($drawType, '_');
            array_push($newLayerGroups, [$response->name, $drawType]);
        }

        return $newLayerGroups;
    }

    public function getLayersWithDrawType($workspace, $layerGroupName)
    {
        $this->curlController->setUrl('/workspaces/'.$workspace.'/layergroups/'.$layerGroupName.'.json');
        $this->curlController->run();

        $responses = json_decode($this->curlController->responseBody[0]);

        $newLayerGroups = [];
        $responses = $responses->layerGroup->publishables->published;
        if(is_array($responses)){
            foreach($responses as $response){
                array_push($newLayerGroups, $response->name);
            }
        } else{
            array_push($newLayerGroups, $responses->name);
        }

        return $newLayerGroups;
    }

    public function putAction($id)
    {
        $request = $this->getRequestBody();
        $request->name = str_replace(' ', '_', $request->name);
        $response = [];

        foreach($request->typ as $typ=>$val){
            $this->_setupTableName($request->name, $typ);
            $this->_setupColumnAndData($typ, $val);

            array_push($response, $this->queryBuilder->updateAction());
        }

        return $response;
    }

    /**
     * Contoh Request Body
     * {
        "name": "titik jarak",
        "workspace": "IDBangunan",
        "typ": {
                "point": {
                    "0": {"description":"abc bangunan", "lat": "-6.215340", "long": "106.851901"},
                    "bcd": {"description":"bcd bangunan", "lat": "-6.215340", "long": "106.851901"}
                },
                "line": {
                    "abc": {"description":"abc bangunan", "lat": "-6.215340", "long": "106.851901"},
                    "bcd": {"description":"bcd bangunan", "lat": "-6.215340", "long": "106.851901"}
                },
                "polygon":{
                    "abc": {"description":"abc bangunan", "lat": "-6.215340", "long": "106.851901"},
                    "bcd": {"description":"bcd bangunan", "lat": "-6.215340", "long": "106.851901"}
                }
            }
        }
     */
    public function postAction()
    {
        $request = $this->getRequestBody();
        $request->name = strtolower(str_replace(' ','_',$request->name));

        $layerNames = [];
        $index = 0;

        foreach($request->type as $type=>$val){
            $this->_setupTableName($request->name, $type);
            $this->_setupColumnAndData($type, $val);

            $layerNames[$index] = $this->queryBuilder->table;
            $index++;

            $this->queryBuilder->createTable($type);
            $this->queryBuilder->insertAction();
        }
        $this->xmlRequestProcessor->createLayer($request->workspace, $layerNames, $request->name);

        return ["OK"];
    }

    public function upload($type, $key)
    {
        $tmpFolder = $this->di->get('config')->application->tmpDir . $key.'/';

        if($this->request->hasFiles() == true){
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
        return ["OK"];
    }

    private function _splitFilenameFromDrawType($file)
    {
        $tmp = explode('._.', $file);
        return $tmp[0];
    }

    private function _get2DArrayFromCsv($csvfile) {
        $csv = Array();
        $rowcount = 0;
        if (($handle = fopen($csvfile, "r")) !== FALSE) {
            $max_line_length = defined('MAX_LINE_LENGTH') ? MAX_LINE_LENGTH : 10000;
            $header = fgetcsv($handle, $max_line_length);
            $header_colcount = count($header);
            while (($row = fgetcsv($handle, $max_line_length)) !== FALSE) {
                $row_colcount = count($row);
                if ($row_colcount == $header_colcount) {
                    $entry = array_combine($header, $row);
                    $csv[] = $entry;
                }
                else {
                    error_log("csvreader: Invalid number of columns at line " . ($rowcount + 2) . " (row " . ($rowcount + 1) . "). Expected=$header_colcount Got=$row_colcount");
                    return null;
                }
                $rowcount++;
            }
            //echo "Totally $rowcount rows found\n";
            fclose($handle);
        }
        else {
            error_log("csvreader: Could not read CSV \"$csvfile\"");
            return null;
        }
        return $csv;
    }

    public function uploadFileToGeoServer($workspace, $layer, $key)
    {
        $tmpFolder = $this->di->get('config')->application->tmpDir . $key.'/';
        $files = scandir($tmpFolder);
        $layer = strtolower(str_replace(' ','_',$layer));

        foreach($files as $file){
            if($file == '.' || $file == '..') continue;

            $fullFilePath = $tmpFolder . '/' . $file;
            $type = $this->_splitFilenameFromDrawType($file);
            $tmpParts = pathinfo($fullFilePath);
            $newName = $type . '_' . $layer . '.' .$tmpParts['extension'];

            #rename($fullFilePath, $tmpFolder . '/' . $newName);

            if($tmpParts['extension'] == 'zip'){
//                $this->xmlRequestProcessor->createDataStore($workspace, $layer);
//
//                $this->curlController->setUrl('/workspaces/' . $workspace .'/datastores/' . $layer .'/file.shp');
//                $this->curlController->setRequestMethod('put');
//                $this->curlController->setRequestBody(file_get_contents($tmpFolder . $newName));
//                $this->curlController->run();
            } else if($tmpParts['extension'] == 'csv'){
                $values = $this->_get2DArrayFromCsv($tmpFolder . $newName);
                $index = 1;
                foreach($values as $val){

                }
            }
        }

        $this->curlController->setUrl('/workspaces/'.$workspace.'/datastores/'. $layer.'/featuretypes.json');
        $this->curlController->run();

        $responses = json_decode($this->curlController->responseBody[0]);
        $featureTypes = $responses->featureTypes->featureType;
        $layerNames = [];

        foreach($featureTypes as $featureType){
            array_push ($layerNames, $featureType->name);
        }
        $this->xmlRequestProcessor->createLayerGroup($workspace, $layer, $layerNames);

        return ["OK"];
    }

    private function _setupColumnAndData($type, $val)
    {
        $this->queryBuilder->columns = [];
        array_push($this->queryBuilder->columns, 'id');
        $this->queryBuilder->data= [];

        switch(strtolower($type)){
            case 'point':
                array_push($this->queryBuilder->columns, 'point');
                $this->_mergeColumnAndData($val, 'POINT');
                break;
            case 'line':
                array_push($this->queryBuilder->columns, 'line');
                $this->_mergeColumnAndData($val, 'LINESTRING');
                break;
            case'poly':
                array_push($this->queryBuilder->columns, 'poly');
                $this->_mergeColumnAndData($val, 'POLYGON');
                break;
        }
    }

    private function _setupTableName($name, $typ)
    {
        if(strtolower($typ) == 'point') $this->queryBuilder->table = $name . '_point';
        else if(strtolower($typ) == 'line') $this->queryBuilder->table = $name . '_line';
        else if(strtolower($typ) == 'poly') $this->queryBuilder->table = $name . '_poly';
    }

    private function _mergeColumnAndData($value, $type)
    {
        for($i=0; $i<count($value); $i++){
            $geom = 'ST_GeomFromText(\''.$type.'(';
            if($type == 'POLYGON') $geom .= '(';

            foreach($value[$i] as $valA){
                if(is_array($valA)){
                    foreach($valA as $valB){
                        if(is_array($valB)){
                            foreach($valB as $valC){
                                $geom .= $valB[0].' '.$valB[1].',';
                                break;
                            }
                        } else{
                            $geom .= $valA[0].' '.$valA[1].',';
                            break;
                        }
                    }
                } else{
                    $geom .= $value[$i][0].' '.$value[$i][1].',';
                    break;
                }
            }
            $geom = rtrim($geom, ',');
            if($type == 'POLYGON') $geom .= ')';
            $geom .= ')\', 4326)';

            $this->queryBuilder->data = array_merge(
                $this->queryBuilder->data,
                [[$i+1, $geom]]
            );
        }
    }
}