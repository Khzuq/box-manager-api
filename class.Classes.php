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
		elseif ($_GET["a"] == "insert-classes") {
			$name = $this->db->real_escape_string($_GET['name']);
			$teacher = $this->db->real_escape_string($_GET['teacher']);
			$max_students = $this->db->real_escape_string($_GET['max_students']);
			$data = $this->db->real_escape_string($_GET['data']);
			$timer = $this->db->real_escape_string($_GET['timer']);
			$this->InsertClasses($name, $teacher, $max_students, $data, $timer);
		}
		elseif ($_GET["a"] == "get-classes-byday") {
			$data = $this->db->real_escape_string($_GET['data']);
			$this->GetClassesByDate($data);
		}
		elseif ($_GET["a"] == "entry-classes") {
			$id_student = $this->db->real_escape_string($_GET['id_student']);
			$id_classe = $this->db->real_escape_string($_GET['id_classe']);
			$this->EntryClasses($id_student, $id_classe);
		}
		elseif ($_GET["a"] == "get-students-class") {
			$id_classe = $this->db->real_escape_string($_GET['id_classe']);
			$this->GetStudentsClass($id_classe);
		}
	}

	public function EntryClasses($id_student, $id_classe)
	{
		if ($query = $this->db->prepare('INSERT INTO classes_checkin (id_classes, id_users) VALUES (?,?)')) {
			$query->bind_param("ii", $id_classe, $id_student);
			if ($query->execute()) {
				$this->GetClasses();
			} else {
				echo "failed";
			}
		} else {
			return false;
		}
	}

	public function GetStudentsClass($id_classe)
	{	
		$response["students-to-class"] = array();
		if ($query =  $this->db->prepare('SELECT users.nome FROM classes_checkin join users on classes_checkin.id_users = users.id and classes_checkin.id_classes=?')) {
			$query->bind_param("i",$id_classe);
			$query->execute();
			$result = $query->get_result();
			if (!empty($result->num_rows)) {
				while ($row = $result->fetch_object()) {
					$json[] = array("nome" => $row->nome);
				}	
				if(isset($json)){
					array_push($response["students-to-class"], $json);
					$response['type'] = "students-to-class";
					echo json_encode($response);
				}
				else{
					$response['students-to-class'] = "empty";
					$response['type'] = "students-to-class";
					echo json_encode($response);
				}				
			
			} else {
				$response['students-to-class'] = "empty";
				$response['type'] = "students-to-class";
				echo json_encode($response);
			}
		} else {
			return false;
		}
	}

	public function GetClasses()
	{
		$response["classes"] = array();		
		if ($query = $this->db->query('SELECT d.id as id, d.name as nome_aula, a.nome as nome_stor, d.max_students as maximo, d.data as data, d.timer as timer FROM classes d LEFT JOIN users a on a.id=d.teacher')) {
			if (empty($query)) {
				return null;
			} else {
				while ($row = $query->fetch_object()) {
					$json[] = array("id" => $row->id, "classe_name" => $row->nome_aula, "teacher" => $row->nome_stor, "max_students" => $row->maximo
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

	public function InsertClasses($name, $teacher, $max_students, $data, $timer)
	{
		if ($query = $this->db->prepare('INSERT INTO classes (name, teacher, max_students, data, timer) VALUES (?,?,?,?,?)')) {
			$query->bind_param("siiss", $name, ($this->GetID($teacher)['id']), $max_students, $data, $timer);
			if ($query->execute()) {
				$this->GetClasses();
			} else {
				echo "failed";
			}
		} else {
			return false;
		}
	}

	public function GetClassesByDate($data)
	{
		$response["classes"] = array();		
		if ($query = $this->db->prepare('SELECT d.id as id, d.name as nome_aula, a.nome as nome_stor, a.id as id_stor, d.max_students as maximo, d.data as data, d.timer as timer FROM classes d LEFT JOIN users a on a.id=d.teacher WHERE d.data =?')) {
			$query->bind_param("s",$data);
			$query->execute();
			$result = $query->get_result();
			if (empty($result->num_rows)) {
				return null;
			} else {
				while ($row = $result->fetch_object()) {
					$json[] = array("id" => $row->id, "classe_name" => $row->nome_aula, "teacher" => $row->nome_stor, "id_stor" => $row->id_stor, "max_students" => $row->maximo
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
}

$Classes = new Classes();
?>