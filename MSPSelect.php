<?php
namespace msq;

final class MSPSelect extends MysqliSimpleQueryi {
    
    private $columns;
    private $join_table;
    private $on;
    private $result;
    
    protected function bind_result_array( ){
        static $bind_result_array = [];
        $i = 0;
        foreach( $this->result as $v ){
            $binder_name = 'param_' . $i;
            $$binder_name = $v;
            $bind_result_array[$v] =& $$binder_name;
            $i++;
        }
        return $bind_result_array;
    }
    
    public function execute() {
        $sql = "SELECT\n\t";
        foreach( $this->generate_column_list() as $v ){
            $sql .= ( $v[1] == $v[2] ) ?
                    sprintf("`%s`.`%s`, ", $v[0], $v[1]) :
                    sprintf("`%s`.`%s` AS `%s`, ", $v[0], $v[2], $v[1]);
        }
        $this->ctrim($sql);
        $sql .= sprintf("\nFROM\n\t`%s`", $this->table_name);
        if( $this->join_table !== FALSE ){
            $max = count( $this->join_table );
            for( $i = 0; $i < $max; $i++ ){
                $jt = $this->join_table[$i];
                $on = $this->on[$i];
                $sql .= ( $jt[2] === FALSE ) ?
                        sprintf("\nJOIN\n\t`%s`\nON\n\t`%s`.`%s` %s `%s`.`%s`",
                                $jt[0], key($on[0]), $on[0][key($on[0])], $on[2], 
                                key($on[1]), $on[1][key($on[1])]) :
                        sprintf("\n%s JOIN\n\t`%s`\nON\n\t`%s`.`%s` %s `%s`.`%s`",
                                $jt[2], $jt[0], key($on[0]), $on[0][key($on[0])], $on[2], 
                                key($on[1]), $on[1][key($on[1])]);
            }
        }
        $sql .= $this->print_extra_sql();
        $params = $this->bind_param_array( $this->param );
        return $this->query($sql, $params);
        
    }
    
    private function fix_columns( $columns ){
        foreach( $columns as $k => $v ){
            if( is_numeric( $k ) ){
                $out[$v] = $v;
            } else {
                $out[$k] = $v;
            }
        }
        return $out;
    }
    
    public function from($table_name, $columns){
        $this->table_name = $table_name;
        $this->columns = $this->fix_columns( $columns );
    }
    
    private function generate_column_list(){
        foreach( $this->columns as $k => $v ){
            $columns[] = [$this->table_name,$k,$v];
            $this->result[] = $k;
        }
        if( $this->join_table !== FALSE ){
            foreach( $this->join_table as $val ){
                foreach( $val[1] as $k => $v ){
                    $columns[] = [$val[0],$k,$v];
                    $this->result[] = $k;
                }
            }
        }
        return $columns;
    }
    
    public function join( $join_table, $join_columns, $join_type = FALSE ){
        $this->join_table[] = [$join_table, $this->fix_columns($join_columns),$join_type];
        return $this;
    }
    
    public function left_join( $join_table, $join_columns ){
        return $this->join( $join_table, $join_columns, "LEFT" );
    }
    
    public function limit($limit_or_offset, $limit = FALSE){
        if( $limit === FALSE ){
            $this->limit = $limit_or_offset;
        } else {
            $this->offset = $limit_or_offset;
            $this->limit = $limit;
        }
    }
    
    public function on( $column, $value, $evaluator = "=" ){
        $this->on[] = [$column,$value,$evaluator];
    }
    
    protected function query($sql, $params){
        $stmt = $this->mysqli->prepare($sql);
        call_user_func_array( array($stmt, 'bind_param'), $params );
        $stmt->execute();
        $result_array = $this->bind_result_array();
        call_user_func_array( array( $stmt, 'bind_result' ), $result_array );
        $i = 0;
        $results = array();
        while( $stmt->fetch() ){
            $results[$i] = array();
            foreach( $result_array as $k => $v ) $results[$i][$k] = $v;
            $i++;
        }
        return $results;
    }
    
    public function right_join( $join_table, $join_columns ){
        return $this->join( $join_table, $join_columns, "RIGHT" );
    }

}
