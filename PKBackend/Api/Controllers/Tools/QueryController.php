<?php


namespace PetaKami\Controllers\Tools;


class QueryController extends TablesController
{
    public $columns;

    public $data = [];

    private $_query;

    public function __construct()
    {
        parent::__construct();
    }

    public function getAction()
    {

    }

    public function insertAction()
    {
        $this->_validate();
        $this->_stringify();

        try{
            $result = $this->connection->execute($this->_query);
        } catch (\Exception $e) {
            $result = $e->getMessage();
        }

        $this->_query = '';
        return $result;
    }

    public function putAction()
    {

    }

    public function deleteAction()
    {

    }

    private function _stringify()
    {
        $columns = sprintf('"%s"', implode('","', $this->columns));
        $str = '';
        foreach ($this->data as $values) {
            foreach ($values as &$val) {
                $pos = strpos($val, 'ST_GeomFromText(');
                if (is_null($val)) {
                    $val = 'NULL';
                    continue;
                }
                if (is_string($val) && $pos === false) {
                    $val = "'".$val."'";
                }
            }
            $str .= sprintf('(%s),', implode(',', $values));
        }
        $str = rtrim($str, ',');
        $this->_query = sprintf("INSERT INTO %s (%s) VALUES %s",
            $this->table,
            $columns,
            $str
        );
    }

    private function _validate()
    {
        if ($this->table == null) {
            throw new \Exception('Table harus diisi dl');
        }
        if (count($this->columns) == 0) {
            throw new \Exception('Columns ga boleh null');
        }
        $required_count = count($this->columns);
        foreach ($this->data as $value) {
            if (count($value) !== $required_count) {
                throw new \Exception('Data dan Column harus sesuai' . $required_count);
            }
        }
    }
}