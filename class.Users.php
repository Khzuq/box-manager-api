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
		} elseif ($_GET["a"] == "change-pw") {
			$id = $this->db->real_escape_string($_GET['id']);
			$oldpw = $this->db->real_escape_string($_GET['oldpw']);
			$newpw = $this->db->real_escape_string($_GET['newpw']);
			$confpw = $this->db->real_escape_string($_GET['confpw']);
			$this->ChangePW($id, $oldpw, $newpw, $confpw);
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

	public function ChangePW($id, $oldpw, $newpw, $confpw) {
		if ($oldpw != "" && $newpw != "" && $confpw != "") {
			if ($newpw == $confpw) {
				if ($query = $this->db->prepare('SELECT pw FROM users WHERE pw = ?')) {
					$query->bind_param("s", $oldpw);
					$query->execute();
					$result = $query->get_result();
					if (empty($result->num_rows)) {
						echo 'Password antiga errada!';
					} else {
						if ($query = $this->db->prepare('UPDATE users SET pw = ? WHERE id = ?')) {
							$query->bind_param("si", $newpw, $id);
							if ($query->execute()) {
								echo "Alterada com sucesso!";
							} else {
								echo "failed";
							}
						}
					}
				}
			} else {
				echo "Confirmação de password errada!";
			}
		} else {
			echo "Preencha todos os campos!";
		}
	}
}

$Users = new Users();
?>