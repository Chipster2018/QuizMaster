<?php
// If we are the main, exit
hideIfMain(__FILE__);


class QuizDatabase {
	function __construct($host,$dbname,$username,$pwd) {
		$dsn = "mysql:host=$host;dbname=$dbname";
		try {
			$this->db = new PDO($dsn,$username,$pwd);
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		} catch (PDOException $e) {
			$err_msg = $e->getMessage();
			include("error.php");
			exit();
		}
	}
	
	function queryDB($query, $params=array()) {
		$statement = $this->db->prepare($query);
		
		
//		echo $query . "<br>\n";
			
		foreach($params as $key => $value) {
//			echo ":$key\t$value<br>\n";
			$statement->bindValue(":$key",$value);
		}
		$statement->execute();
		$result = $statement->fetchAll();
			
		$statement->closeCursor();
			
		return $result;
			
	}
	
	function insert($table, $cols=array(), $values=array(), $params=array()) {
		$cols = implode(", ", $cols);	
		$values = implode(", ", $values);
		
		$query = "INSERT INTO $table ($cols) VALUES ($values)";

		return $this->queryDB($query,$params);
	}

	function update($table, $values=array(), $where="", $params=array()) {
		
		$query = "UPDATE $table SET ";
		foreach($values as $key => $value) {
			$query .= "$key=$value ";
			
		}
		$query .= "WHERE $where";
		
		return $this->queryDB($query,$params);
	}
	
	
	function delete($table, $where="", $params=array()) {
		
		$query = "DELETE FROM $table WHERE $where";
		
		return $this->queryDB($query,$params);
	}
	
	function select($table, $what=array("*"), $where="", $params=array()) {
		$what = implode(", ", $what);	
		
		$query = "SELECT $what FROM $table";
		if($where != "") {
			$query .= " WHERE $where";
		}

		return $this->queryDB($query,$params);
	}

	
};
$db = new QuizDatabase("localhost","quizmaster_db","QMaster","QL|izM3");
//$db = new QuizDatabase("localhost","my_guitar_shop1","mgs_user","pa55word");
?>