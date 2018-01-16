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
		} elseif ($_GET["a"] == "insert-trainings") {
			$warmup = $this->db->real_escape_string($_GET['warmup']);
			$skill = $this->db->real_escape_string($_GET['skill']);
			$wod = $this->db->real_escape_string($_GET['wod']);
			$data = $this->db->real_escape_string($_GET['data']);
			$this->InsertTrainings($warmup, $skill, $wod, $data);
		} elseif ($_GET["a"] == "delete-trainings") {
			$id = $this->db->real_escape_string($_GET['id']);
			$this->DeleteTrainings($id);
		} elseif ($_GET["a"] == "edit-trainings") {
			$id = $this->db->real_escape_string($_GET['id']);
			$warmup = $this->db->real_escape_string($_GET['warmup']);
			$skill = $this->db->real_escape_string($_GET['skill']);
			$wod = $this->db->real_escape_string($_GET['wod']);
			$data = $this->db->real_escape_string($_GET['data']);
			$this->EditTrainings($id, $warmup, $skill, $wod, $data);
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
				echo json_encode($json);
			}
		} else {
			return false;
		}
	}

	public function InsertTrainings($warmup, $skill, $wod, $data)
	{
		if ($query = $this->db->prepare('INSERT INTO trainings (warmup, skill, wod, data) VALUES (?,?,?,?)')) {
			$query->bind_param("ssss", $warmup, $skill, $wod, $data);
			if ($query->execute()) {
				$this->GetTrainings();
			} else {
				echo "failed";
			}
		} else {
			return false;
		}
	}

	public function DeleteTrainings($id)
	{
		if ($query = $this->db->prepare('DELETE FROM trainings where id=?')) {
				$query->bind_param("i", $id);
			if ($query->execute()) {
				$this->GetTrainings();
			} else {
				echo "failed";
			}
		} else {
			return false;
		}
	}

	public function EditTrainings($id, $warmup, $skill, $wod, $data){
		if ($query = $this->db->prepare('UPDATE trainings set warmup=?, skill=?, wod=?, data=? where id=?')) {
			$query->bind_param("ssssi", $warmup, $skill, $wod, $data, $id);
			if ($query->execute()) {
				$this->GetTrainings();
				//echo "done";
			} else {
				echo "failed";
			}
		} else {
			return false;
		}
	}
}

$Trainings = new Trainings();
?>