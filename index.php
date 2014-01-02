<?php
//include ("DB.php");


//require 'vendor/DB.php';

//CONNECT DB
//SQLITE
$dbtype = "sqlite";
$dbname = "./test.db";
$table = "test";
$test = new DB($dbtype, $dbname, $table, null, null);

////MYSQL
//$dbtype = "mysql";
//$host = "localhost";
//$dbname = "test";
//$table = "testtbl";
//$user = "root";
//$pass = "";
//$test = new DB($dbtype, "host={$host};dbname={$dbname}", $table, $user, $pass);

//CREATE TABLE
$schema = array ();
$schema["testint"] = "INTEGER";
$schema["testtxt"] = "TEXT";
$test->create($schema);

//INSERT
$data = array ();
$data["testint"] = 123;
$test->insert($data);
$data["testint"] = 456;
$data["testtxt"] = "abc";
$test->insert($data);
$data["testint"] = 456;
$data["testtxt"] = "xyz";
$test->insert($data);
$data["testint"] = 789;
$data["testtxt"] = "def";
$test->insert($data);

//SELECT ALL
$r = $test->selectAll();
//dump($r);

//SELECT
$where = array ();
$where["testint"] = 456;
$r = $test->select($where);
//dump($r);

//UPDATE
$where = array ();
$update = array ();
$where["testint"] = 789;
$update["testtxt"] = "ghi";
$test->update($update, $where);

$r = $test->select($where);
//dump($r);

$test->insert($data);
$r = $test->selectAll();
//dump($r);
//SELECT by LIKE
$where = array ();
$where["testtxt"] = array (
	"like" => "b"
);
$r = $test->select($where);
//dump($r);

//SELECT with AND
$where = array ();
$where["testint"] = 456;
$update["testtxt"] = "xyz";
$r = $test->select($where);
//dump($r);

//SELECT with OR
$where = array ();
$where["testtxt"] = array (
	"" => "abc",
	"" => "xyz"
);
$r = $test->select($where);
//dump($r);

//todo
//COUNT
//DESC / PRAGMA
//DELETE
?>
