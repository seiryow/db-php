<?php

class DB {
	function __construct($dbtype, $dbname, $table, $user, $pass) {
		//		$con = new PDO("{$dbname}", $user, $pass);
		$this->type = $dbtype;
		$this->table = $table;
		$this->db = new PDO("{$dbtype}:{$dbname}", $user, $pass);
	}

	function connect($dbname, $user, $pass) {

		$con = new PDO("{$dbname}", $user, $pass);
		//$$con = new PDO( "{$dbkind}:dbname={$dbname};host={$host}",$user,$pass);
		return $con;
	}
	public function query($query) {

		return $this->db->query($query);
		$sth = $this->db->prepare($query);
		if ($sth->execute()) {
			return $sth->fetchAll();
		} else {
			return false;
		}
	}
	public function select() {
		$arg_num = func_num_args();
		$arg_list = func_get_args();
		$op = "";
		if (0 < $arg_num) {
			$warr = $arg_list[0];
		}
		if (1 < $arg_num) {
			$op = $arg_list[1];
		}
		return select_Data($this->db, $this->table, $warr, $op);
	}
	public function selectAll() {
		$arg_num = func_num_args();
		$arg_list = func_get_args();
		$arr = array ();
		$op = "";
		if (0 < $arg_num) {
			$op = $arg_list[0];
		}
		return select_Data($this->db, $this->table, array (), $op);
	}
	public function create($arr) {
		return create_table($this->db, $this->table, $this->type, $arr);
	}
	public function insert($arr) {
		return insert_Data($this->db, $this->table, $this->retrieve($arr));
	}
	public function update($arr, $warr) {
		return update_Data($this->db, $this->table, $this->retrieve($arr), $warr);
	}

	public function retrieve($arr) {
		$i = 0;
		$narr = array ();
		foreach ($arr as $k => $v) {

			if ($k === $i) {
				$i++;
				continue;
			}
			$narr[$k] = $v;
		}
		return $narr;
	}
}

function show_table_info($con, $table) {

	$query = "PRAGMA table_info('{$table}');";
	$sth = $con->prepare($query);
	if ($sth->execute()) {
		return $sth->fetchAll();
	} else {
		return false;
	}

}

function show_tables($con) {

	$query = "select name from sqlite_master where type = 'table';";
	$sth = $con->prepare($query);
	if ($sth->execute()) {
		return $sth->fetchAll();
	} else {
		return false;
	}

}

function insert_Data($con, $table, $arr) {

	$i = 0;
	foreach ($arr as $key => $value) {
		$colum[$i] = $key;
		$data[$i] = $value;
		$i++;
	}

	$colum = implode(",", $colum);
	$data = "'" . implode("','", $data) . "'";
	$q = "INSERT INTO {$table}({$colum}) VALUES ({$data})";
	//	echo $q;
	$result = $con->query($q);

	return $result;

}
function desc_table($con, $table) {

	//	$result = $con->query("DESC {$table};");
	//$query=".schema {$table};";
	$query = "PRAGMA table_info({$table});";

	$sth = $con->prepare($query);
	if ($sth->execute()) {
		return $sth->fetchAll();
	} else {
		return false;
	}

}

function select_Data() {
	$arg_num = func_num_args();
	$arg_list = func_get_args();
	$con = $arg_list[0];
	$table = $arg_list[1];

	$arr = array ();

	$op = "";
	if (2 < $arg_num) {

		$arr = $arg_list[2];

		if (3 < $arg_num) {
			$op = $arg_list[3];
		}
	}

	$query = "select * from {$table} ";

	$q = " 1=1 ";

	//	foreach ($arr as $key => $value) {
	//		$q .= " OR {$key} LIKE '{$value}' ";
	//	}
	foreach ($arr as $key => $value) {
		if (is_array($value)) {
			//			echo 23456;
			$q2 = " 1=0 ";
			foreach ($value as $key2 => $value2) {
				if ($key2 === "like") {
					//					echo $key2;
					//					echo "like";
					$q2 .= " OR {$key} LIKE '%{$value2}%' ";
					//					echo $q2;
				} else
					if ($key2 === "between") {
						//					echo $key2;
						//					echo "like";
						$q2 .= " OR {$key} BETWEEN {$value2} ";
						//					echo $q2;
					} else {
						if ($value2 == "") {
							$q2 .= " OR {$key} IS NULL ";
						} else {
							$q2 .= " OR {$key} = '{$value2}' ";
						}
					}

			}
			//			echo $q2;
			$q .= " AND ( $q2 ) ";
		}
		elseif (is_numeric($key)) {
			$q .= " AND {$value} ";
		} else {
			if ($value == "") {
				$q .= " AND {$key} IS NULL ";
			} else {
				$q .= " AND {$key} = '{$value}' ";
			}
		}
	}
	$query = "select * from {$table} WHERE  {$q} {$op} ";
	//					echo $query;
	$row = array ();
	$sth = $con->prepare($query);
	if ($sth->execute()) {
		return $sth->fetchAll();
	} else {
		return false;
	}
}

function count_Data() {
	$arg_num = func_num_args();
	$arg_list = func_get_args();
	$con = $arg_list[0];
	$table = $arg_list[1];

	$arr = array ();

	$op = "";
	if (2 < $arg_num) {

		$arr = $arg_list[2];

		if (3 < $arg_num) {
			$op = $arg_list[3];
		}
	}

	$query = "select * from {$table} ";

	$q = " 1=1 ";

	//	foreach ($arr as $key => $value) {
	//		if (is_array($value)) {
	//			$q2 = " 1=0 ";
	//			foreach ($value as $key2 => $value2) {
	//				$q2 .= " OR {$key} = '{$value2}' ";
	//			}
	//			$q .= " AND ( $q2 ) ";
	//		} else {
	//			$q .= " AND {$key} = '{$value}' ";
	//		}
	//	}

	foreach ($arr as $key => $value) {
		if (is_array($value)) {
			//			echo 23456;
			$q2 = " 1=0 ";
			foreach ($value as $key2 => $value2) {
				if ($key2 === "like") {
					//					echo $key2;
					//					echo "like";
					$q2 .= " OR {$key} LIKE '%{$value2}%' ";
					//					echo $q2;
				} else {
					if ($value2 == "") {
						$q2 .= " OR {$key} IS NULL ";
					} else {
						$q2 .= " OR {$key} = '{$value2}' ";
					}
				}

			}
			//			echo $q2;
			$q .= " AND ( $q2 ) ";
		}
		elseif (is_numeric($key)) {
			$q .= " AND {$value} ";
		} else {
			if ($value == "") {
				$q .= " AND {$key} IS NULL ";
			} else {
				$q .= " AND {$key} = '{$value}' ";
			}
		}
	}

	$query = "select COUNT(id) AS num from {$table} WHERE {$q} {$op} ";
	//$query = "SELECT COUNT(genre2id) AS num FROM result
	//WHERE genre2id=393 AND cityid='01202' ";
	//		echo $query;
	$row = array ();
	$sth = $con->prepare($query);
	if ($sth->execute()) {
		$res = $sth->fetchAll();
		return $res[0]["num"];
	} else {
		return false;
	}
}
function limit_order_select_Data($con, $table, $arr, $order, $st, $num) {

	$row = array ();
	$i = 0;
	foreach ($order as $key => $value) {
		$row[$i] = " {$key} {$value} ";
		$i++;
	}
	$op = " ORDER BY " . implode(",", $row) . " LIMIT " . $st . "," . $num;

	return select_Data($con, $table, $arr, $op);
}

function limit_select_Data($con, $table, $arr, $st, $num) {

	$row = array ();
	$i = 0;
	$op = " LIMIT " . $st . "," . $num;

	return select_Data($con, $table, $arr, $op);
}

function order_select_Data($con, $table, $arr, $order) {

	$row = array ();
	$i = 0;
	foreach ($order as $key => $value) {
		$row[$i] = " {$key} {$value} ";
		$i++;
	}
	$op = " ORDER BY " . implode(",", $row);

	return select_Data($con, $table, $arr, $op);
}

function like_select_Data($con, $table, $arr) {

	$query = "select * from {$table} ";

	$q = " 1=0 ";

	foreach ($arr as $key => $value) {
		$q .= " OR {$key} LIKE '{$value}' ";
	}
	$query = "select * from {$table} WHERE {$q}  ";

	$row = array ();
	$sth = $con->prepare($query);
	if ($sth->execute()) {
		return $sth->fetchAll();
	} else {
		return false;
	}
}

function update_Data() {
	$arg_num = func_num_args();
	$arg_list = func_get_args();
	$con = $arg_list[0];
	$table = $arg_list[1];

	$uparr = array ();
	if (2 < $arg_num) {
		$uparr = $arg_list[2];
	}

	$arr = array ();
	if (3 < $arg_num) {
		$arr = $arg_list[3];
	}
	$uq = array ();
	$i = 0;

	foreach ($uparr as $key => $value) {
		$uq[$i] = " {$key} = '{$value}'";
		$i++;
	}
	$uq = implode(",", $uq);
	$q = " 1=1 ";
	foreach ($arr as $key => $value) {
		$q .= " AND {$key} = '{$value}'";
	}
	//	echo "UPDATE {$table} SET {$uq}  WHERE {$q} ";
	$q = "UPDATE {$table} SET {$uq}  WHERE {$q} ";
	$result = $con->query($q);
	return $result;
}

function delete_Data($con, $table, $arr) {

	$q = " 1=1 ";
	foreach ($arr as $key => $value) {
		$q .= " AND {$key} = '{$value}'";
	}
	$query = "delete from {$table} where {$q}";
	$result = $con->query($query);
	return $result;

}

function connect($dbname, $user, $pass) {

	$con = new PDO("{$dbname}", $user, $pass);
	//$$con = new PDO( "{$dbkind}:dbname={$dbname};host={$host}",$user,$pass);
	return $con;
}

function close($con) {

}

function create_table($con, $table, $dbtype, $arr) {
	$qa = array ();

	if ($dbtype == "sqlite") {
		array_push($qa, "id INTEGER PRIMARY KEY AUTOINCREMENT");
	} else {
		array_push($qa, "id INTEGER PRIMARY KEY AUTO_INCREMENT ");
	}
	foreach ($arr as $key => $value) {
		array_push($qa, "{$key} {$value}");
	}
	if ($dbtype == "sqlite") {
		array_push($qa, "timestamp VARCHAR(19) DEFAULT (datetime())");
	} else {
		array_push($qa, "timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
	}
	$q = implode(",", $qa);

	$query = "DROP  TABLE {$table}";
	$result = $con->query($query);

	$query = "CREATE TABLE {$table}({$q}) ";

	//			echo $query;
	$result = $con->query($query);

	return $result;
}

function dump($arr) {

	echo "<pre>";
	var_dump($arr);

	echo "</pre>";

}

function drop_table($con, $table) {

	$query = "DROP  TABLE {$table}";

	$result = $con->query($query);

	return $result;
}

function join_AccountData($con, $table, $arr) {

	$arg_num = func_num_args();
	$arg_list = func_get_args();
	$con = $arg_list[0];
	$table = $arg_list[1];

	$arr = array ();

	$op = "";
	if (2 < $arg_num) {

		$arr = $arg_list[2];

		if (3 < $arg_num) {
			$op = $arg_list[3];
		}
	}

	$query = "select * from {$table} natural inner join Account ";

	$q = " 1=1 ";

	foreach ($arr as $key => $value) {
		$q .= " AND {$key} = '{$value}' ";
	}
	$query = "select * from {$table} natural inner join Account WHERE {$q} {$op} ";
	//	echo $query;
	$row = array ();
	$sth = $con->prepare($query);
	if ($sth->execute()) {
		return $sth->fetchAll();
	} else {
		return false;
	}
}

function join_Select_Data() {

	$arg_num = func_num_args();
	$arg_list = func_get_args();
	$con = $arg_list[0];
	$table = $arg_list[1];
	$table2 = $arg_list[2];

	$arr = array ();

	$op = "";
	if (3 < $arg_num) {

		$arr = $arg_list[3];

		if (4 < $arg_num) {
			$op = $arg_list[4];
		}
	}

	$query = "select * from {$table} natural inner join {$table2} ";

	$q = " 1=1 ";

	foreach ($arr as $key => $value) {
		$q .= " AND {$key} = '{$value}' ";
	}
	$query = "select * from {$table} natural inner join {$table2} WHERE {$q} {$op} ";
	//echo $query;
	$row = array ();
	$sth = $con->prepare($query);
	if ($sth->execute()) {
		return $sth->fetchAll();
	} else {
		return false;
	}
}
?>