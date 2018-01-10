<?php
require_once("class.Database.php");

/**
* 
*/
class Trainings
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
		if ($_GET["a"] == "get-trainings") {
			$this->GetTrainings();
		}
	}

	public function GetTrainings()
	{
		if ($query = $this->db->query('SELECT id, warmup, skill, wod, data FROM trainings')) {
			if (empty($query)) {
				return null;
			} else {
				while ($row = $query->fetch_object()) {
					$json[] = array("id" => $row->id, "warmup" => $row->warmup, "skill" => $row->skill, "wod" => $row->wod, "data" => $row->data);
				}

				/*$mainArray['mensagem'] = "TESTE";
				$mainArray['alunos'] = $json;*/
				echo json_encode($json);
			}
		} else {
			return false;
		}
	}

	
}

$Trainings = new Trainings();
?>