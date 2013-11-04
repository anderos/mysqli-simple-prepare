<?php
namespace msq;

interface MysqliSimpleQueryInterface {
    public function execute();
}

abstract class MysqliSimpleQuery implements MysqliSimpleQueryInterface {
    
    protected $mysqli;
    protected $table_name;
    
    public function __construct(\mysqli $mysqli){
        $this->mysqli = $mysqli;
        $this->clear();
    }
    
    protected function bind_param_array( $params ){
        static $bind_param_array = [];
        $bind_param_array[] =& $this->get_param_markers($params);
        $i = 0;
        foreach( $params as $arr ){
            foreach( $arr as $v ){
                $binder_name = 'param_' . $i;
                $$binder_name = implode("", $v);
                $bind_param_array[] =& $$binder_name;
                $i++;
            }
        }
        return $bind_param_array;
    }
    
    public function clear(){
        foreach( $this as $k => &$v ){
            if ($k == 'mysqli') {
                continue;
            } else {
                $v = FALSE;
            }
        }
    }
    
    protected function ctrim( &$string ){
        $string = rtrim( $string, ', ' );
    }
    
    private function get_param_markers($arr){
        $out = '';
        foreach( $arr as $ar ){
            foreach( $ar as $v ){
                $out .= str_replace( "%", "", key($v) );
            }
        }
        return $out;
    }
    
    protected function query($sql, $params){
        $stmt = $this->mysqli->prepare($sql);
        call_user_func_array( array($stmt, 'bind_param'), $params );
        $stmt->execute();
    }
    
}