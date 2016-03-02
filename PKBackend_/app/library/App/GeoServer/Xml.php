<?php


namespace PetaKami\GeoServer;


class Xml
{
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function xmlDataStore($workspace, $dataStore)
    {
        return '
            <dataStore>
              <name>'. $dataStore .'</name>
              <type>'. $this->config->geoserver->datastore_type .'</type>
              <enabled>true</enabled>
              <workspace>
                <name>'. $workspace .'</name>
                <atom:link xmlns:atom="http://www.w3.org/2005/Atom" rel="alternate"
                href="'.$this->config->geoserver->rest_url.'/workspaces/'. $workspace .'.xml"
                type="application/xml"/>
              </workspace>
              <connectionParameters>
                <entry key="port">5432</entry>
                <entry key="user">'.$this->config->database->username.'</entry>
                <entry key="passwd">'.$this->config->database->password.'</entry>
                <entry key="dbtype">postgis</entry>
                <entry key="host">'.$this->config->geoserver->db_host.'</entry>
                <entry key="database">'.$this->config->database->db_geo.'</entry>
                <entry key="schema">public</entry>
              </connectionParameters>
              <__default>false</__default>
            </dataStore>

        ';
    }

    public function xmlFeatureType($featureTypeName)
    {
        return '<featureType><name>'.$featureTypeName.'</name></featureType>';
    }

    public function xmlLayerGroup($workspace, $layerGroupName, $layerGroupLayers)
    {
        $layerGroup = '<layerGroup>
                        <name>'.$layerGroupName.'</name>
                        <workspace>
                            <name>'. $workspace .'</name>
                            <atom:link xmlns:atom="http://www.w3.org/2005/Atom" rel="alternate"
                            href="'.$this->config->geoserver->rest_url.'/workspaces/'. $workspace .'.xml"
                            type="application/xml"/>
                        </workspace>
		                <layers>';

        foreach($layerGroupLayers as $layer){
            $layerGroup .= '<layer>'.$layer.'</layer>';
        }

        $layerGroup .= '</layers></layerGroup>';

        return $layerGroup;
    }
}