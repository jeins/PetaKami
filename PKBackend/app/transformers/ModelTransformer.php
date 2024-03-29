<?php


namespace PetaKami\Transformers;


use Phalcon\Db\Column;
use Phalcon\Di;
use PhalconRest\Constants\Services;

class ModelTransformer extends \League\Fractal\TransformerAbstract
{
    const TYPE_UNKNOWN = 0;

    const TYPE_INTEGER = 1;
    const TYPE_FLOAT = 2;
    const TYPE_DOUBLE = 3;
    const TYPE_BOOLEAN = 4;
    const TYPE_STRING = 5;
    const TYPE_DATE = 6;
    const TYPE_JSON = 7;

    protected $modelClass;

    protected $modelDataTypes;
    protected $modelColumnMap;
    protected $modelAttributes;


    public function getModelClass()
    {
        return $this->modelClass;
    }

    public function setModelClass($modelClass)
    {
        $this->modelClass = $modelClass;
    }

    /**
     * @return array Properties to be included in the response
     */
    protected function includedProperties()
    {
        $attributes =  $this->getModelAttributes();
        $columnMap = $this->getModelColumnMap();

        if(!is_array($columnMap)){
            return $attributes;
        }

        return array_map(function($attribute) use ($columnMap){

            return array_key_exists($attribute, $columnMap) ? $columnMap[$attribute] : $attribute;

        }, $attributes);
    }

    /**
     * @return array Properties to be excluded in the response
     */
    protected function excludedProperties()
    {
        return [];
    }

    protected function additionalFields($item)
    {
        return [];
    }

    /**
     * Returns keyMap to be used.
     * Keys are the model properties, values are the response keys
     *
     * @return array
     */
    protected function keyMap()
    {
        return [];
    }

    protected function typeMap()
    {
        return [];
    }


    protected function getModel($item)
    {
        return $item;
    }

    protected function getFieldValue($item, $propertyName, $fieldName)
    {
        $dataType = array_key_exists($propertyName,
            $this->getModelDataTypes()) ? $this->getModelDataTypes()[$propertyName] : null;

        $model = $this->getModel($item);
        $value = $model->$propertyName;

        if($value === null){
            return null;
        }

        $typedValue = $value;

        switch ($dataType) {

            case self::TYPE_INTEGER: {

                $typedValue = (int)$value;
                break;
            }

            case self::TYPE_FLOAT: {

                $typedValue = (float)$value;
                break;
            }

            case self::TYPE_DOUBLE: {

                $typedValue = (double)$value;
                break;
            }

            case self::TYPE_BOOLEAN: {

                $typedValue = (bool)$value;
                break;
            }

            case self::TYPE_STRING: {

                $typedValue = (string)$value;
                break;
            }

            case self::TYPE_DATE: {

                $typedValue = strtotime($value);
                break;
            }

            case self::TYPE_JSON: {

                $typedValue = json_decode($value);
                break;
            }
        }

        return $typedValue;
    }


    public function transform($item)
    {
        if($item == null){
            return null;
        }

        $result = [];
        $keyMap = $this->keyMap();

        foreach ($this->getResponseProperties() as $property) {

            $fieldName = array_key_exists($property, $keyMap) ? $keyMap[$property] : $property;
            $result[$fieldName] = $this->getFieldValue($item, $property, $fieldName);
        }

        $combinedResult = array_merge($result, $this->additionalFields($item));

        return $combinedResult;
    }

    public function getResponseProperties()
    {
        return array_diff($this->includedProperties(), $this->excludedProperties());
    }

    protected function getMappedDatabaseType($type)
    {
        $responseType = null;

        switch ($type) {

            case Column::TYPE_INTEGER:
            case Column::TYPE_BIGINTEGER: {

                $responseType = self::TYPE_INTEGER;
                break;
            }

            case Column::TYPE_DECIMAL:
            case Column::TYPE_FLOAT: {

                $responseType = self::TYPE_FLOAT;
                break;
            }

            case Column::TYPE_DOUBLE: {

                $responseType = self::TYPE_DOUBLE;
                break;
            }

            case Column::TYPE_BOOLEAN: {

                $responseType = self::TYPE_BOOLEAN;
                break;
            }

            case Column::TYPE_VARCHAR:
            case Column::TYPE_CHAR:
            case Column::TYPE_TEXT:
            case Column::TYPE_BLOB:
            case Column::TYPE_MEDIUMBLOB:
            case Column::TYPE_LONGBLOB: {

                $responseType = self::TYPE_STRING;
                break;
            }

            case Column::TYPE_DATE:
            case Column::TYPE_DATETIME: {

                $responseType = self::TYPE_DATE;
                break;
            }

            case Column::TYPE_JSON:
            case Column::TYPE_JSONB: {

                $responseType = self::TYPE_JSON;
                break;
            }

            default:
                $responseType = self::TYPE_STRING;
        }

        return $responseType;
    }

    protected function getModelAttributes()
    {
        if (!$this->modelAttributes) {

            /** @var \Phalcon\Mvc\Model\MetaData $modelsMetaData */
            $modelsMetaData = Di::getDefault()->get(Services::MODELS_METADATA);

            $modelClass = $this->getModelClass();

            $this->modelAttributes = $modelsMetaData->getAttributes(new $modelClass);
        }

        return $this->modelAttributes;
    }

    public function getModelDataTypes()
    {
        if (!$this->modelDataTypes) {

            /** @var \Phalcon\Mvc\Model\MetaData $modelsMetaData */
            $modelsMetaData = Di::getDefault()->get(Services::MODELS_METADATA);

            $modelClass = $this->getModelClass();
            $columnMap = $this->getModelColumnMap();

            $dataTypes = $modelsMetaData->getDataTypes(new $modelClass);

            $mappedDataTypes = [];

            if(is_array($columnMap)) {

                foreach ($dataTypes as $attributeName => $dataType) {

                    $mappedAttributeName = array_key_exists($attributeName, $columnMap) ? $columnMap[$attributeName] : $attributeName;
                    $mappedDataTypes[$mappedAttributeName] = $this->getMappedDatabaseType($dataType);
                }
            }
            else {

                $mappedDataTypes = $dataTypes;
            }

            $this->modelDataTypes = array_merge($mappedDataTypes, $this->typeMap());
        }

        return $this->modelDataTypes;
    }

    protected function getModelColumnMap()
    {
        if (!$this->modelColumnMap) {

            /** @var \Phalcon\Mvc\Model\MetaData $modelsMetaData */
            $modelsMetaData = Di::getDefault()->get(Services::MODELS_METADATA);

            $modelClass = $this->getModelClass();
            $metaDataColumnMap = $modelsMetaData->getColumnMap(new $modelClass);

            $this->modelColumnMap = array_merge($metaDataColumnMap ? $metaDataColumnMap : [], $this->keyMap());
        }

        return $this->modelColumnMap;
    }
}