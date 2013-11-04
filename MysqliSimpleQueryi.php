<?php
namespace msq;

abstract class MysqliSimpleQueryi extends MysqliSimpleQuery {
    
    protected $limit;
    protected $offset;
    protected $order;
    protected $param;
    protected $where;
    
    public function from($table_name){
        $this->table_name = $table_name;
    }
    
    public function limit( $limit ){
        $this->limit = $limit;
    }
    
    public function order_by( $column, $order="ASC" ){
        if( $this->order === FALSE ){
            $this->order = [];
        }
        $this->order[] = [$column, $order];
    }
    
    protected function print_extra_sql(){
        $sql = '';
        if( $this->where !== FALSE ){
            $sql .= "\nWHERE\n\t";
            $first_run = TRUE;
            foreach( $this->where as $where ){
                if( $first_run === TRUE ){
                    $first_run = FALSE;
                } else {
                    $sql .= " AND ";
                }
                $sql .= ( is_array($where[0]) ) ?
                        sprintf("`%s`.`%s` %s ?", key($where[0]), 
                                $where[0][key($where[0])], $where[2]) :
                        sprintf("`%s` %s ?", $where[0], $where[2]);
                $this->param[] = [$where[1]];
            }
        }
        if( $this->order !== FALSE ){
            $sql .= "\nORDER BY\n\t";
            foreach( $this->order as $order ){
                $sql .= ( is_array( $order[0] ) ) ?
                        sprintf( "`%s`.`%s` %s, ", key($order[0]), 
                                $order[0][key($order[0])], $order[1] ) :
                        sprintf( "`%s` %s, ", $order[0], $order[1] );
            }
            $this->ctrim($sql);
        }
        if( $this->limit !== FALSE ){
            $sql .= "\nLIMIT ";
            if( $this->offset === FALSE ){
                $sql .= sprintf("%d", $this->limit);
            } else {
                $sql .= sprintf("%d, %d", $this->offset, $this->limit);
            }
        }
        return $sql;
    }
    
    public function table($table_name){
        $this->from($table_name);
    }
    
    public function where($column, $value, $evaluator = "=") {
        if( $this->where === FALSE ){
            $this->where = [];
        }
        $this->where[] = [$column, $value, $evaluator];
    }
    
}
