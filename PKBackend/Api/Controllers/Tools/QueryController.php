<?php


namespace PetaKami\Controllers\Tools;


class QueryController extends TablesController
{
    public $columns;

    public $data;

    private $query;

    public function __construct()
    {
        parent::__construct();
    }

    public function getAction()
    {

    }

    public function postAction()
    {
        $this->_validate();
        $this->_stringify();

        try{
            $result = $this->connection->execute($this->query);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        $this->query = '';
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
                if($pos !== false){
                    $val = $val;
                } else if (is_string($val)) {
                    $val = "'".$val."'";
                }
            }
            $str .= sprintf('(%s),', implode(',', $values));
        }
        $str = rtrim($str, ',');
        $this->query = sprintf("INSERT INTO %s (%s) VALUES %s",
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