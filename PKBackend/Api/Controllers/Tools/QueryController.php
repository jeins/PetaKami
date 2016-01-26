<?php


namespace PetaKami\Controllers\Tools;


class QueryController extends TablesController
{
    public $columns = [];

    public $data = [];

    public function selectAction()
    {

    }

    public function insertAction()
    {
        $this->_validate();

        try{
            $columns = sprintf('"%s"', implode('","', $this->columns));
            $str = '';
            foreach ($this->data as $values) {
                foreach ($values as &$val) {
                    $pos = strpos($val, 'ST_GeomFromText(');
                    if (is_null($val)) {
                        $val = 'NULL';
                        continue;
                    }
                    if (is_string($val) && $pos === false) $val = "'".$val."'";
                }
                $str .= sprintf('(%s),', implode(',', $values));
            }
            $str = rtrim($str, ',');

            $query = sprintf("INSERT INTO %s (%s) VALUES %s",
                $this->table,
                $columns,
                $str
            );

            $result = $this->connection->execute($query);
        } catch (\Exception $e) {
            $result = $e->getMessage();
        }
        return $result;
    }

    public function updateAction()
    {
        $this->_validate();

        try{
            foreach($this->data as $values){
                $str = '';
                for($i=1; $i<count($values); $i++){
                    $pos = strpos($values[$i], 'ST_GeomFromText(');

                    if(is_string($values[$i]) && $pos === false) $str .= $this->columns[$i]."='".$values[$i]."',";
                }
                $str = rtrim($str, ',');

                $query = sprintf("UPDATE %s SET %s WHERE %s;",
                    $this->table,
                    $str,
                    $this->columns[0].'='.$values[0]
                );

                $result = $this->connection->execute($query);
            }
        } catch(\Exception $e){
            $result = $e->getMessage();
        }
        return $result;
    }

    public function deleteAction()
    {

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