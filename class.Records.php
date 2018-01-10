<?php
require_once("class.Database.php");

/**
* 
*/
class Records
{
	private $db;
	use Database;
	//private $mainArray = array();
	
	function __construct()
	{
		if (!$this->db_connect()) {
			die("Falha na ligação");
		}

		$this->Actions();
	}

	public function Actions()
	{
		if ($_GET["a"] == "get-records") {
			$this->GetRecords();
		}
	}

	public function GetRecords()
	{
		if ($query = $this->db->query('SELECT records.id, modalities.name as modality_name, peso, id_students, data FROM records LEFT JOIN modalities ON records.id_modalities = modalities.id')) {
			if (empty($query)) {
				return null;
			} else {
				while ($row = $query->fetch_object()) {
					$json[] = array("id" => $row->id, "modality" => $row->modality_name, "weight" => $row->peso, "student" => $row->id_students, "date" => $row->data);
				}
				echo json_encode($json);
			}
		} else {
			return false;
		}
	}
}

$Records = new Records();
?>