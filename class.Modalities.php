<?php
require_once("class.Database.php");

/**
* 
*/
class Modalities
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
		if ($_GET["a"] == "get-modalities") {
			$this->GetModalities();
		}
	}

	public function GetModalities() {
		$response["modalities"] = array();	
		if ($query = $this->db->query('SELECT id, name FROM modalities')) {	
			if (empty($query)) {
				return null;
			} else {
				while ($row = $query->fetch_object()) {
					$json[] = array("id" => $row->id, "modality" => $row->name);
				}
				array_push($response["modalities"], $json);
				$response['type'] = "modalities";
				echo json_encode($response);
			}
		} else {
			return false;
		}
	}

	
}

$Modalities = new Modalities();
?>