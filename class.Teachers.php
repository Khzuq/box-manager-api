<?php
require_once("class.Database.php");

/**
* 
*/
class Teachers
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
		if ($_GET["a"] == "get-teachers") {
			$this->GetTeachers();
		}
	}

	public function GetTeachers() {
		$response["teachers"] = array();	
		if ($query = $this->db->query('SELECT id, nome FROM users WHERE user_type=1')) {	
			if (empty($query)) {
				return null;
			} else {
				while ($row = $query->fetch_object()) {
					$json[] = array("id" => $row->id, "nome" => $row->nome);
				}
				array_push($response["teachers"], $json);
				$response['type'] = "teachers";
				echo json_encode($response);
			}
		} else {
			return false;
		}
	}

	
}

$Teachers = new Teachers();
?>