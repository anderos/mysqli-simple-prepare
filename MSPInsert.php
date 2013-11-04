<?php
namespace msq;

final class MSPInsert extends MysqliSimpleQuery {
    
    private $columns;
    public $insert_id;
    public $insert_ids;
    private $values;
    
    public function execute() {
        $sql = sprintf("INSERT INTO `%s`\n(", $this->table_name );
        $val = '';
        foreach( $this->columns as $col ){
            $sql .= sprintf("`%s`, ", $col );
            $val .= "?, ";
        }
        $this->ctrim($sql);
        $this->ctrim($val);
        $sql .= ")\nVALUES";
        $max = count( $this->values );
        for( $i = 0; $i < $max; $i++ ){
            $sql .= "\n($val), ";
        }
        $this->ctrim($sql);
        $params = $this->bind_param_array( $this->values );
        $this->query($sql, $params);
        $this->find_ids();
    }
    
    private function find_ids(){
        $this->insert_id = $this->mysqli->insert_id;
        $max = count( $this->values );
        for( $i = 0; $i < $max; $i++ ){
            $this->insert_ids[] = $this->insert_id + $i;
        }
    }
    
    public function into($table_name, $columns){
        $this->table_name = $table_name;
        $this->columns = $columns;
    }
    
    public function values($values){
        if( $this->table_name === FALSE ){
            throw new Exception("Table and columns must be set with MSPInsert::into() first! MSPInsert::values()");
        }
        elseif( count($this->columns) != count($values) ){
            throw new Exception("Count of values array must match count of columns array! MSPInsert::values()");
        }
        else {
            if( $this->values === FALSE ) {
                $this->values = array();
            }
            $this->values[] = $values;
        }
        
    }

}
