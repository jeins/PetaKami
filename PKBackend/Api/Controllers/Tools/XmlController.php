<?php


namespace PetaKami\Controllers\Tools;


use PetaKami\Controllers\BaseController;

class XmlController extends BaseController
{
    public $workspace;

    public $dataStore;

    public $featureTypeName;

    public $layerGroupName;

    public $layerGroupLayers = [];

    public function dataStoreXML()
    {
        return '
            <dataStore>
              <name>'. $this->dataStore .'</name>
              <type>'. $this->config->geoserver->DATASTORE_TYP .'</type>
              <enabled>true</enabled>
              <workspace>
                <name>'. $this->workspace .'</name>
                <atom:link xmlns:atom="http://www.w3.org/2005/Atom" rel="alternate"
                href="'.$this->config->geoserver->REST_URL.'/workspaces/'. $this->workspace .'.xml"
                type="application/xml"/>
              </workspace>
              <connectionParameters>
                <entry key="port">5432</entry>
                <entry key="user">'.$this->config->database->username.'</entry>
                <entry key="passwd">'.$this->config->database->password.'</entry>
                <entry key="dbtype">postgis</entry>
                <entry key="host">'.$this->config->geoserver->DB_HOST.'</entry>
                <entry key="database">'.$this->config->database->dbname.'</entry>
                <entry key="schema">public</entry>
              </connectionParameters>
              <__default>false</__default>
            </dataStore>

        ';
    }

    public function featureTypeXML()
    {
        return '<featureType><name>'.$this->featureTypeName.'</name></featureType>';
    }

    public function layerGroupXML()
    {
        $layerGroup = '<layerGroup>
                        <name>'.$this->layerGroupName.'</name>
                        <workspace>
                            <name>'. $this->workspace .'</name>
                            <atom:link xmlns:atom="http://www.w3.org/2005/Atom" rel="alternate"
                            href="'.$this->config->geoserver->REST_URL.'/workspaces/'. $this->workspace .'.xml"
                            type="application/xml"/>
                        </workspace>
		                <layers>';

        foreach($this->layerGroupLayers as $layer){
            $layerGroup .= '<layer>'.$layer.'</layer>';
        }

        $layerGroup .= '</layers></layerGroup>';

        return $layerGroup;
    }
}