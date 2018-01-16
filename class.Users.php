<?php
require_once("class.Database.php");

/**
* 
*/
class Users
{
	private $db;
	use Database;
	private $response = array();
	
	function __construct()
	{
		if (!$this->db_connect()) {
			die("Falha na ligação");
		}

		$this->Actions();
	}

	public function Actions()
	{
		if ($_GET["a"] == "login") {
			$username = $this->db->real_escape_string($_GET['username']);
			$password = $this->db->real_escape_string($_GET['password']);
			$this->Login($username, $password);
		}
	}

	public function Login($username, $password)
	{	
		$response["login"] = array();
		if ($query =  $this->db->prepare('SELECT * FROM users WHERE email=? and pw=?')) {
				$query->bind_param("ss",$username, $password);
				$query->execute();
				$result = $query->get_result();
				if (empty($result->num_rows)) {
						$response['login'] = false;
						echo json_encode($response);	
				} else {
					$response['login'] = true;
					while ($row = $result->fetch_object()) {
						$json[] = array("id" => $row->id, "usertype" => $row->user_type, "nome" => $row->nome);
						$response['dados'] = $json;
					}	
					echo json_encode($response);	
				}
			}
	 	 else {
			return false;
		}
		
	}
}

$Users = new Users();
?>