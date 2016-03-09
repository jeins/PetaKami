<?php


namespace PetaKami\Mvc;

use PetaKami\Constants\PKConst;
use Phalcon\Mvc\Model;
use PhalconRest\Constants\ErrorCodes;
use PhalconRest\Exceptions\UserException;
use PhalconRest\Validation\Validator;

class BaseModel extends Model
{

    public $createdAt;
    public $updatedAt;

    /**
     * @var Validator
     */
    protected $validator;

    public function initialize()
    {
        $this->setConnectionService(PKConst::DB_PK);
    }

    /**
     * @param array $data
     * @param null $dataColumnMap
     * @param null $whiteList
     * @return Model
     */
    public function assign(array $data, $dataColumnMap = null, $whiteList = null)
    {
        return parent::assign($data, $dataColumnMap, $whiteList === null ? $this->whiteList() : $whiteList);
    }

    public function beforeValidationOnCreate()
    {
        $this->createdAt = date('Y-m-d H:i:s');
        $this->updatedAt = date('Y-m-d H:i:s');
    }

    public function beforeValidationOnUpdate()
    {
        $this->updatedAt = date('Y-m-d H:i:s');
    }

    public function getValidator()
    {
        if (!$this->validator) {
            $this->validator = Validator::make($this, $this->validateRules());
        }

        return $this->validator;
    }

    public function validation()
    {
        $this->validator = $this->getValidator();
        return $this->validator->passes();
    }

    public function onValidationFails()
    {
        $message = null;

        if ($this->validator) {
            $message = $this->validator->getFirstMessage();
        }

        if ($messages = $this->getMessages()) {
            $message = $messages[0]->getMessage();
        }

        if (is_null($message)) {

            $message = 'Could not validate data';
        }

        throw new UserException(ErrorCodes::DATA_INVALID, $message);
    }


    public function whiteList()
    {
        return null;
    }

    public function validateRules()
    {
        return [];
    }


    public static function existsById($id)
    {
        return self::count(array(
            'id = ?0',
            'bind' => array($id)
        )) > 0;
    }

    public function columnMap()
    {
        return [
            'created_at' => 'createdAt',
            'updated_at' => 'updatedAt',
        ];
    }

    public function beforeCreate()
    {
        $this->createdAt = date('Y-m-d H:i:s');
        $this->updatedAt = $this->createdAt;
    }

    public function beforeUpdate()
    {
        $this->updatedAt = date('Y-m-d H:i:s');
    }
}