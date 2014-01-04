<?php
include ("DB.php");

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

$test->debugquery = true;

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
$r = $test->select();
//dump($r);

//SELECT
$where = array ();
$where["testint"] = 456;
$r = $test->select($where);
//dump($r);

//RAW QUERY (same result as above)
$r = $test->query("select * from test WHERE testint = '456' ");
//dump($r);

//UPDATE
$where = array ();
$update = array ();
$where["testint"] = 789;
$update["testtxt"] = "ghi";
$test->update($update, $where);
$r = $test->select();
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
$where["testtxt"] = "xyz";
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
