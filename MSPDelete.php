<?php
namespace msq;

final class MSPDelete extends MysqliSimpleQueryi {
    
    public function execute(){
        $sql = sprintf( "DELETE FROM `%s`", $this->table_name );
        $sql .= $this->print_extra_sql();
        $params = $this->bind_param_array( $this->param );
        $this->query($sql, $params);
    }
    
}
