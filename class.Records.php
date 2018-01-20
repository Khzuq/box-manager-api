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
			$id = $this->db->real_escape_string($_GET['id_user']);
			$this->GetRecords($id);
		} else if ($_GET["a"] == "delete-records") {
			$id = $this->db->real_escape_string($_GET['id']);
			$this->DeleteRecords($id);
		} elseif ($_GET["a"] == "edit-records") {
			$id = $this->db->real_escape_string($_GET['id']);
			$data = $this->db->real_escape_string($_GET['data']);
			$peso = $this->db->real_escape_string($_GET['peso']);
			$modalidade = $this->db->real_escape_string($_GET['modalidade']);
			$this->EditRecords($id, $data, $peso, $modalidade);
		} elseif ($_GET["a"] == "insert-records") {
			$id_user = $this->db->real_escape_string($_GET['id_user']);
			$data = $this->db->real_escape_string($_GET['data']);
			$peso = $this->db->real_escape_string($_GET['peso']);
			$modalidade = $this->db->real_escape_string($_GET['modalidade']);
			$this->InsertRecords($id_user, $data, $peso, $modalidade);
		}
	}

	public function GetRecords($id)
	{
		$response["records"] = array();
		if ($query = $this->db->prepare('SELECT records.id, modalities.name as modality_name, peso, id_students, data FROM records LEFT JOIN modalities ON records.id_modalities = modalities.id WHERE id_students = ?')) {
			$query->bind_param("i",$id);
			$query->execute();
			$result = $query->get_result();
			//
			if (empty($result->num_rows)) {
				return null;
			} else {
				while ($row = $result->fetch_object()) {
					$json[] = array("id" => $row->id, "modality" => $row->modality_name, "weight" => $row->peso, "student" => $row->id_students, "date" => $row->data);
				}
				
				if(isset($json)){
					array_push($response["records"], $json);
					$response['type'] = "records";
					echo json_encode($response);
				}
				else{
					$response['records'] = "empty";
					$response['type'] = "records";
					echo json_encode($response);
				}
			}
		} else {
			return false;
		}
	}

	public function DeleteRecords($id)
	{
		if ($query = $this->db->prepare('DELETE FROM records where id=?')) {
				$query->bind_param("i", $id);
			if ($query->execute()) {
				$this->GetRecords();
			} else {
				echo "failed";
			}
		} else {
			return false;
		}
	}

	public function EditRecords($id, $data, $peso, $modalidade){
		if ($query = $this->db->prepare('UPDATE records set data=?, peso=?, id_modalities=? where id=?')) {
			$query->bind_param("siii", $data, $peso, ($this->GetID($modalidade)['id']), $id);
			if ($query->execute()) {
				$this->GetRecords();
				//echo "done";
			} else {
				echo "failed";
			}
		} else {
			return false;
		}
	}

	public function InsertRecords($id_user, $data, $peso, $modalidade)
	{
		if ($query = $this->db->prepare('INSERT INTO records (id_modalities, peso, id_students, data) VALUES (?,?,?,?)')) {
				$query->bind_param("iiis", ($this->GetID($modalidade)['id']), $peso, $id_user, $data);
				if ($query->execute()) {
					$this->GetRecords();
				} else {
					echo "failed";
				}
			} else {
				return false;
			}
	}

	public function GetID($idx)
	{	
		$x = 0;
		if ($query =  $this->db->query('SELECT * FROM modalities order by id ASC')) {
				if (empty($query)) {
					return null;
				} else {
					while ($row = $query->fetch_object()) {
						$json[] = array("id" => $row->id);
					}	
					for($i =0; $i <= $idx; $i++){
						if($i==$idx){
							$x = $json[$idx];
						}
					}
					return $x;
				}
			} else {
				return false;
			}
			
	}
}

$Records = new Records();
?>