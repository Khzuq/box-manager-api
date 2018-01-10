<?php
require_once("class.Database.php");

/**
* 
*/
class Classes
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
		if ($_GET["a"] == "get-classes") {
			$this->GetClasses();
		}
		elseif ($_GET["a"] == "delete-classes") {
			$id = $this->db->real_escape_string($_GET['id']);
			$this->DeleteClasses($id);
		}
		elseif ($_GET["a"] == "edit-classes") {
			$id = $this->db->real_escape_string($_GET['id']);
			$nome = $this->db->real_escape_string($_GET['nome']);
			$teacher = $this->db->real_escape_string($_GET['teacher']);
			$max_students = $this->db->real_escape_string($_GET['max_students']);
			$data = $this->db->real_escape_string($_GET['data']);
			$timer = $this->db->real_escape_string($_GET['timer']);
			$this->EditClasses($id, $nome, $teacher, $max_students, $data, $timer);
		}
	}

	public function GetClasses()
	{
		$response["classes"] = array();		
		if ($query = $this->db->query('SELECT d.id as id, d.name as nome_aula, a.nome as nome_stor, d.students as estudantes, d.max_students as maximo, d.data as data, d.timer as timer FROM classes d LEFT JOIN users a on a.id=d.teacher')) {
			if (empty($query)) {
				return null;
			} else {
				while ($row = $query->fetch_object()) {
					$json[] = array("id" => $row->id, "classe_name" => $row->nome_aula, "teacher" => $row->nome_stor, "students" => $row->estudantes, "max_students" => $row->maximo
				, "data" => $row->data, "timer" => $row->timer);
				}

				if(isset($json)){
					array_push($response["classes"], $json);
					$response['type'] = "classes";
					echo json_encode($response);
				}
				else{
					$response['classes'] = "empty";
					$response['type'] = "classes";
					echo json_encode($response);
				}				
			}
		} else {
			return false;
		}
	}

	public function EditClasses($id, $nome, $teacher, $max_students, $data, $timer){
		if ($query = $this->db->prepare('UPDATE classes set name=?, teacher=?, max_students=?, data=?, timer=? where id=?')) {
			$query->bind_param("siissi", $nome, ($this->GetID($teacher)['id']), $max_students, $data, $timer, $id);
			if ($query->execute()) {
				$this->GetClasses();
				//echo "done";
			} else {
				echo "failed";
			}
		} else {
			return false;
		}
	}


	public function DeleteClasses($id)
	{
		if ($query = $this->db->prepare('DELETE FROM classes where id=?')) {
				$query->bind_param("i", $id);
			if ($query->execute()) {
				$this->GetClasses();
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
		if ($query =  $this->db->query('SELECT * FROM users where user_type=1 order by id ASC')) {
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

$Classes = new Classes();
?>