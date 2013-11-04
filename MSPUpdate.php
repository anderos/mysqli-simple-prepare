<?php
namespace msq;

final class MSPUpdate extends MysqliSimpleQueryi {
    
    private $set;
    
    public function execute() {
        $sql = sprintf("UPDATE `%s`\nSET\n\t", $this->table_name);
        foreach( $this->set as $set ){
            $sql .= sprintf("`%s`=?, ", $set);
        }
        $this->ctrim($sql);
        $sql .= $this->print_extra_sql();
        $params = $this->bind_param_array( $this->param );
        $this->query($sql, $params);
    }
    
    public function set( $column, $value ){
        $this->param[] = [$value];
        $this->set[] = $column;
    }

}
