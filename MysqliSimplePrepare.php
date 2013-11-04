<?php

/**
 * Allows for easy generation of Mysqli Prepared Statements
 * 
 * MysqliSimplePrepare is a factory pattern class that returns objects that
 * handle delete, insert, select, and update queries.
 * Usage:
 * 
 * <?php
 * include 'MysqliSimplePrepare.php';
 * $mysqli = new mysqli('host', 'user', 'pw', 'db_name');
 * // select
 * $select = MysqliSimplePrepare::select($mysqli);
 * $select->from( "table_name", ["column1", "alias_for_column2" => "column2"] );
 * // join, left_join, and right_join are available
 * // notice that ->on() is chainable
 * $select->left_join( "join_table", ["join_col1", "alias_for_join_col2" => "join_col2"] )->on(
 * "table_name" => "id", "join_table" => "table_id");
 * $select->where( ["table_name" => "id"], ["%d" => 1] );
 * $select->order_by(["table_name" => "id"]);
 * $select->limit(1);
 * $results = $select->execute();
 * 
 * // delete
 * $delete = MysqliSimplePrepare::delete($mysqli);
 * $delete->from("table_name");
 * $delete->where("some_value", "2", "<");
 * $delete->limit(1);
 * $delete->execute();
 * 
 * // insert
 * $insert = MysqliSimplePrepare::insert($mysqli);
 * $insert->into("table_name", ["column1", "column2"]);
 * $insert->values(["row1_blah", "row2_foo"]);
 * $insert->values(["row2_asdf", "row2_bar"]);
 * $insert->execute();
 * 
 * // update
 * $update = MysqliSimplePrepare::update($mysqli);
 * $update->table("table_name");
 * $update->set("column1", ["%s" => "new_value"]);
 * $update->where("id", ["%d" => 1]);
 * $update->where("user_id" => ["%d" => 423]);
 * $update->execute();
 * 
 * @copyright (c) 2013, James Lucas
 * @license https://www.gnu.org/licenses/gpl-2.0.html GPL2
 * @author James Lucas <anderos@gmx.com>
 */
final class MysqliSimplePrepare {

    private function __construct() { }
    
    static public function delete(mysqli $mysqli){
        return new \msq\MSPDelete($mysqli);
    }

    static public function insert(mysqli $mysqli){
        return new \msq\MSPInsert($mysqli);
    }
    
    static public function select(mysqli $mysqli){
        return new \msq\MSPSelect($mysqli);
    }
    
    static public function update(mysqli $mysqli){
        return new \msq\MSPUpdate($mysqli);
    }
    
}

include_once dirname(__FILE__) . "/MysqliSimpleQuery.php";
include_once dirname(__FILE__) . "/MysqliSimpleQueryi.php";
include_once dirname(__FILE__) . "/MSPDelete.php";
include_once dirname(__FILE__) . "/MSPInsert.php";
include_once dirname(__FILE__) . "/MSPSelect.php";
include_once dirname(__FILE__) . "/MSPUpdate.php";