<?php
session_start();
ini_set('display_errors', 1);

class Action {
    private $db;

    public function __construct() {
        ob_start();
        include 'db_connect.php'; // Ensure db_connect.php contains your database connection logic
        $this->db = $conn;
    }

    function __destruct() {
        $this->db->close();
        ob_end_flush();
    }
	
	

	function login_faculty() {
		// Start the session if not already started
		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}
	
		// Extract POST variables
		extract($_POST);
	
		// Prepare the SQL statement to prevent SQL injection
		$stmt = $this->db->prepare("SELECT *, CONCAT(lastname, ', ', firstname, ' ', middlename) AS name FROM faculty WHERE id_no = ?");
		$stmt->bind_param("s", $id_no);  // "s" indicates that the parameter is a string
		$stmt->execute();
	
		// Get the result
		$result = $stmt->get_result();
	
		if ($result->num_rows > 0) {
			// Fetch the user data
			$user_data = $result->fetch_assoc();
			
			// Store relevant user data in the session
			foreach ($user_data as $key => $value) {
				if ($key != 'password' && !is_numeric($key)) {
					$_SESSION['login_' . $key] = $value;
				}
			}
			return 1;  // Successful login
		} else {
			return 3;  // Invalid ID number
		}
	}
	function logout(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:login.php");
	}
	function logout2(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:../index.php");
	}

	function save_user(){
		extract($_POST);
		$data = " name = '$name' ";
		$data .= ", username = '$username' ";
		if(!empty($password))
		$data .= ", password = '".md5($password)."' ";
		$data .= ", email = '$email' ";
		$data .= ", course = '$course' ";
		$data .= ", type = '$type' ";
		if($type == 1)
			$establishment_id = 0;
		//$data .= ", establishment_id = '$establishment_id' ";
		$chk = $this->db->query("Select * from users where username = '$username' and id !='$id' ")->num_rows;
		if($chk > 0){
			return 2;
			exit;
		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO users set ".$data);
		}else{
			$save = $this->db->query("UPDATE users set ".$data." where id = ".$id);
		}
		if($save){
			return 1;
		}
	}
	function delete_user(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM users where id = ".$id);
		if($delete)
			return 1;
	}
	function signup(){
		extract($_POST);
		$data = " name = '".$firstname.' '.$lastname."' ";
		$data .= ", username = '$email' ";
		$data .= ", password = '".md5($password)."' ";
		$chk = $this->db->query("SELECT * FROM users where username = '$email' ")->num_rows;
		if($chk > 0){
			return 2;
			exit;
		}
			$save = $this->db->query("INSERT INTO users set ".$data);
		if($save){
			$uid = $this->db->insert_id;
			$data = '';
			foreach($_POST as $k => $v){
				if($k =='password')
					continue;
				if(empty($data) && !is_numeric($k) )
					$data = " $k = '$v' ";
				else
					$data .= ", $k = '$v' ";
			}
			if($_FILES['img']['tmp_name'] != ''){
							$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
							$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
							$data .= ", avatar = '$fname' ";

			}
			$save_alumni = $this->db->query("INSERT INTO alumnus_bio set $data ");
			if($data){
				$aid = $this->db->insert_id;
				$this->db->query("UPDATE users set alumnus_id = $aid where id = $uid ");
				$login = $this->login2();
				if($login)
				return 1;
			}
		}
	}
	function update_account(){
		extract($_POST);
		$data = " name = '".$firstname.' '.$lastname."' ";
		$data .= ", username = '$email' ";
		if(!empty($password))
		$data .= ", password = '".md5($password)."' ";
		$chk = $this->db->query("SELECT * FROM users where username = '$email' and id != '{$_SESSION['login_id']}' ")->num_rows;
		if($chk > 0){
			return 2;
			exit;
		}
			$save = $this->db->query("UPDATE users set $data where id = '{$_SESSION['login_id']}' ");
		if($save){
			$data = '';
			foreach($_POST as $k => $v){
				if($k =='password')
					continue;
				if(empty($data) && !is_numeric($k) )
					$data = " $k = '$v' ";
				else
					$data .= ", $k = '$v' ";
			}
			if($_FILES['img']['tmp_name'] != ''){
							$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
							$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
							$data .= ", avatar = '$fname' ";

			}
			$save_alumni = $this->db->query("UPDATE alumnus_bio set $data where id = '{$_SESSION['bio']['id']}' ");
			if($data){
				foreach ($_SESSION as $key => $value) {
					unset($_SESSION[$key]);
				}
				$login = $this->login2();
				if($login)
				return 1;
			}
		}
	}

	function save_settings(){
		extract($_POST);
		$data = " name = '".str_replace("'","&#x2019;",$name)."' ";
		$data .= ", email = '$email' ";
		$data .= ", contact = '$contact' ";
		$data .= ", about_content = '".htmlentities(str_replace("'","&#x2019;",$about))."' ";
		if($_FILES['img']['tmp_name'] != ''){
						$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
						$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
					$data .= ", cover_img = '$fname' ";

		}
		
		
		// echo "INSERT INTO system_settings set ".$data;
		$chk = $this->db->query("SELECT * FROM system_settings");
		if($chk->num_rows > 0){
			$save = $this->db->query("UPDATE system_settings set ".$data);
		}else{
			$save = $this->db->query("INSERT INTO system_settings set ".$data);
		}
		if($save){
		$query = $this->db->query("SELECT * FROM system_settings limit 1")->fetch_array();
		foreach ($query as $key => $value) {
			if(!is_numeric($key))
				$_SESSION['settings'][$key] = $value;
		}

			return 1;
				}
	}

	function save_course(){
		extract($_POST);
		$data = " course = '$course' ";
		$data .= ", description = '$description' ";
		
		// Check for duplicate course
		$check_duplicate = $this->db->query("SELECT * FROM courses WHERE course = '$course' AND id != '$id'");
		if($check_duplicate->num_rows > 0){
			// Duplicate course found, return error
			return 0;
		}
		
		if(empty($id)){
			$save = $this->db->query("INSERT INTO courses set $data");
		}else{
			$save = $this->db->query("UPDATE courses set $data where id = $id");
		}
		if($save)
			return 1;
	}
	
	function delete_course(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM courses where id = ".$id);
		if($delete){
			return 1;
		}
	}
	function save_subject(){
		extract($_POST);
		$data = " subject = '$subject' ";
		$data .= ", description = '$description' ";
		$data .= ", Lec_units = '$lec_units' ";
		$data .= ", Lab_units = '$lab_units' ";
		$data .= ", hours = '$hours' ";
		$data .= ", total_units = '$units' ";
		$data .= ", course = '$course' ";
		$data .= ", year = '$cyear' ";
		$data .= ", semester = '$semester' ";
		$data .= ", specialization = '$specialization' ";
		
		// Check for duplicate subject
		$check_duplicate = $this->db->query("SELECT * FROM subjects WHERE subject = '$subject' AND id != '$id'");
		if($check_duplicate->num_rows > 0){
			// Duplicate subject found, return error
			return 0;
		}
		
		if(empty($id)){
			$save = $this->db->query("INSERT INTO subjects set $data");
		}else{
			$save = $this->db->query("UPDATE subjects set $data where id = $id");
		}
		if($save)
			return 1;
	}
	
	function save_fees(){
		extract($_POST);
		$data = " year = '$year' ";
		$data .= ", library = '$library' ";
		$data .= ", computer = '$computer' ";
		$data .= ", school_id = '$school_id' ";
		$data .= ", athletic = '$athletic' ";
		$data .= ", admission = '$admission' ";
		$data .= ", development = '$development' ";
		$data .= ", guidance = '$guidance' ";
		$data .= ", handbook = '$handbook' ";
		$data .= ", entrance = '$entrance' ";
		$data .= ", registration = '$registration' ";
		$data .= ", medical = '$medical' ";
		$data .= ", cultural = '$cultural' ";
		$data .= ", semester = '$semester' ";
		$data .= ", course = '$course' ";
			if(empty($id)){
				$save = $this->db->query("INSERT INTO fees set $data");
			}else{
				$save = $this->db->query("UPDATE fees set $data where id = $id");
			}
		if($save)
			return 1;
	}
	function save_room(){
		extract($_POST);
		$data = " room_name = '$room' ";
		$data .= ", room_id = '$room_id' ";
		
		// Check for duplicate room name or ID
		$check = $this->db->query("SELECT * FROM roomlist WHERE room_name = '$room' OR room_id = '$room_id'");
		if($check->num_rows > 0){
			return 3; // Return a specific code for duplicate entry
		}
		
		if(empty($id)){
			$save = $this->db->query("INSERT INTO roomlist set $data");
		} else {
			$save = $this->db->query("UPDATE roomlist set $data where id = $id");
		}
		if($save)
			return 1;
	}
	
	function save_timeslot(){
		extract($_POST);
		$data = " time_id = '$time_id' ";
		$data .= ", timeslot = '$timeslot' ";
		//$data .= ", hours = '$hours' ";
		$data .= ", schedule = '$schedule' ";
		$data .= ", specialization = '$specialization' ";
			if(empty($id)){
				$save = $this->db->query("INSERT INTO timeslot set $data");
			}else{
				$save = $this->db->query("UPDATE timeslot set $data where id = $id");
			}
		if($save)
			return 1;
	}	
	function save_section(){
		extract($_POST);
		$data = " course = '$course' ";
		$data .= ", year = '$cyear' ";
		$data .= ", section = '$section' ";
	
		// Check for duplicate section
		if(empty($id)){
			$check = $this->db->query("SELECT * FROM section WHERE course = '$course' AND year = '$cyear' AND section = '$section'");
		} else {
			$check = $this->db->query("SELECT * FROM section WHERE course = '$course' AND year = '$cyear' AND section = '$section' AND id != '$id'");
		}
		if($check->num_rows > 0){
			return 3; // Return a specific code for duplicate entry
		}
	
		if(empty($id)){
			$save = $this->db->query("INSERT INTO section set $data");
		} else {
			$save = $this->db->query("UPDATE section set $data where id = $id");
		}
	
		if($save){
			return empty($id) ? 1 : 2; // Return 1 for insert and 2 for update
		}
		return 0; // Return 0 if the save operation fails
	}
	
	
	function delete_subject(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM subjects where id = ".$id);
		if($delete){
			return 1;
		}
	}
	function delete_fees(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM fees where id = ".$id);
		if($delete){
			return 1;
		}
	}
	function delete_room(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM roomlist where id = ".$id);
		if($delete){
			return 1;
		}
	}
	function delete_timeslot(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM timeslot where id = ".$id);
		if($delete){
			return 1;
		}
	}
	function delete_load(){
		extract($_POST);
		$scode = "";
		$lquery = $this->db->query("SELECT * FROM loading WHERE id = ".$id);
		foreach ($lquery as $key) {
			$scode = $key['subjects'];
		}
		$query = $this->db->query("SELECT * FROM subjects WHERE subject='$scode'");
		foreach ($query as $key) {
		$status = $key['status'];
		$newstats = $status + 1;
		$subjectStats = "status =".$newstats;
		$update = $this->db->query("UPDATE subjects set ".$subjectStats." where subject='$scode'");
		}
		$delete = $this->db->query("DELETE FROM loading where id = ".$id);
		if($delete){
			return 1;
		}
	}
	function delete_MW(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM loading where days ='$days'");
		if($delete){
			return 1;
		}
	}
	function delete_TTh(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM loading where days ='$days'");
		if($delete){
			return 1;
		}
	}
	function delete_Fri(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM loading where days ='$days'");
		if($delete){
			return 1;
		}
	}
	function delete_Sat(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM loading where days ='$days'");
		if($delete){
			return 1;
		}
	}
	function delete_section(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM section where id = ".$id);
		if($delete){
			return 1;
		}
	}
	function save_faculty(){
		extract($_POST);
		$data = '';
		foreach($_POST as $k=> $v){
			if(!empty($v)){
				if($k !='id'){
					if(empty($data))
					$data .= " $k='{$v}' ";
					else
					$data .= ", $k='{$v}' ";
				}
			}
		}
			if(empty($id_no)){
				$i = 1;
				while($i == 1){
					$rand = mt_rand(1,99999999);
					$rand =sprintf("%'08d",$rand);
					$chk = $this->db->query("SELECT * FROM faculty where id_no = '$rand' ")->num_rows;
					if($chk <= 0){
						$data .= ", id_no='$rand' ";
						$i = 0;
					}
				}
			}

		if(empty($id)){
			if(!empty($id_no)){
				$chk = $this->db->query("SELECT * FROM faculty where id_no = '$id_no' ")->num_rows;
				if($chk > 0){
					return 2;
					exit;
				}
			}
			$save = $this->db->query("INSERT INTO faculty set $data ");
		}else{
			if(!empty($id_no)){
				$chk = $this->db->query("SELECT * FROM faculty where id_no = '$id_no' and id != $id ")->num_rows;
				if($chk > 0){
					return 2;
					exit;
				}
			}
			$save = $this->db->query("UPDATE faculty set $data where id=".$id);
		}
		if($save)
			return 1;
	}
	function delete_faculty(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM faculty where id = ".$id);
		if($delete){
			return 1;
		}
	}
	function save_roomschedule(){
		extract($_POST);
		$data = " timeslot_id = '$timeslot_id' ";
		$data .= ", timeslot = '$timeslot' ";
		$data .= ", rooms = '$room' ";
		$data .= ", faculty = '$faculty' ";
		$data .= ", course = '$yrsection' ";
		$data .= ", subjects = '$subject' ";
		$data .= ", semester = '$semester' ";
		//$rdata = implode($dow);
		$data .= ", days = '$days' ";
		$data .= ", sub_description = '$description' ";
		$data .= ", total_units = '$total_units' ";
		$data .= ", lec_units = '$lec_units' ";
		$data .= ", lab_units = '$lab_units' ";
		$data .= ", coursedesc = '$course' ";
		$data .= ", hours = '$hours' ";
		$data .= ", timeslot_sid = '$timeslot_sid' ";
		$data .= ", room_name = '$room_name' ";
		
		if(empty($id)){
			$query = $this->db->query("SELECT * FROM subjects WHERE subject='$subject'");
			foreach ($query as $key) {
			$status = $key['status'];
			$newstats = $status - 1;
			$subjectStats = "status =".$newstats;
			$update = $this->db->query("UPDATE subjects set ".$subjectStats." where subject='$subject'");
			}				
			$sql = "SELECT * FROM loading WHERE timeslot_id ='$timeslot_id' AND rooms='$room' AND days='$days'";
			$query = $this->db->query($sql);

			if($query->num_rows == 0){
				$save = $this->db->query("INSERT INTO loading set ".$data);
			}else{
				return 2;
			}
			
		}else{
			$query = $this->db->query("SELECT * FROM subjects WHERE subject='$subject'");
			foreach ($query as $key) {
			$status = $key['status'];
			$newstats = $status - 1;
			$subjectStats = "status =".$newstats;
			$update = $this->db->query("UPDATE subjects set ".$subjectStats." where subject='$subject'");
			}
			$save = $this->db->query("UPDATE loading set ".$data." where id=".$id);
		}
		if($save){
			return 1;
		}
	}
	function save_roomscheduletth(){
		extract($_POST);
		$data = " timeslot_id = '$timeslot_id' ";
		$data .= ", timeslot = '$timeslot' ";
		$data .= ", rooms = '$room' ";
		$data .= ", faculty = '$faculty' ";
		$data .= ", semester = '$semester' ";
		$data .= ", course = '$course' ";
		$data .= ", subjects = '$subject' ";
		//$rdata = implode($dow);
		$data .= ", days = '$days' ";
		if(empty($id)){
			$query = $this->db->query("SELECT * FROM subjects WHERE subject='$subject'");
			foreach ($query as $key) {
			$status = $key['status'];
			$newstats = $status - 1;
			$subjectStats = "status =".$newstats;
			$update = $this->db->query("UPDATE subjects set ".$subjectStats." where subject='$subject'");
			}
			$save = $this->db->query("INSERT INTO tthloading set ".$data);
		}else{
			$query = $this->db->query("SELECT * FROM subjects WHERE subject='$subject'");
			foreach ($query as $key) {
			$status = $key['status'];
			$newstats = $status - 1;
			$subjectStats = "status =".$newstats;
			$update = $this->db->query("UPDATE subjects set ".$subjectStats." where subject='$subject'");
			}
			$save = $this->db->query("UPDATE tthloading set ".$data." where id=".$id);
		}
		if($save){
			return 1;
		}
	}
	function save_schedule(){
		extract($_POST);
		$data = " faculty_id = '$faculty_id' ";
		$data .= ", course_code = '$subject' ";
		$data .= ", subject_description = '$description' ";
		$data .= ", units = '$units' ";
		$data .= ", room = '$room' ";
		$data .= ", course = '$course' ";
		$data .= ", year = '$year' ";
		$data .= ", section = '$section' ";
		$status = " status = 1 ";
		if(isset($is_repeating)){
			$data .= ", is_repeating = '$is_repeating' ";
			$rdata = array('dow'=>implode(',', $dow),'start'=>$month_from.'-01','end'=>(date('Y-m-d',strtotime($month_to .'-01 +1 month - 1 day '))));
			$data .= ", repeating_data = '".json_encode($rdata)."' ";
		}else{
			$data .= ", is_repeating = 0 ";
			$data .= ", schedule_date = '$schedule_date' ";
		}
		$data .= ", timeslot = '$timeslot' ";
		$data .= ", time_from = '$time_from' ";
		$data .= ", time_to = '$time_to' ";

		if(empty($id)){
			$saveroom = $this->db->query("UPDATE rooms set ".$status." where id=".$room);
				if($saveroom){

					$save = $this->db->query("INSERT INTO schedules set ".$data);
						
					}
					else{
						return 2;
					}
		}else{
			$save = $this->db->query("UPDATE schedules set ".$data." where id=".$id);
		}
		if($save){
			return 1;
		}else{
			return 0;
		}
			
	}
	function delete_schedule(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM schedules where id = ".$id);
		if($delete){
			return 1;
		}
	}
	function get_schecdule(){
		extract($_POST);
		$data = array();
		$qry = $this->db->query("SELECT * FROM schedules where faculty_id = 0 or faculty_id = $faculty_id");
		while($row=$qry->fetch_assoc()){
			if($row['is_repeating'] == 1){
				$rdata = json_decode($row['repeating_data']);
				foreach($rdata as $k =>$v){
					$row[$k] = $v;
				}
			}
			$data[] = $row;
		}
			return json_encode($data);
	}
	function get_year(){
		extract($_POST);
		$data = array();
		$qry = $this->db->query("SELECT * FROM section where id = 0 or id = $course_id");
		while($row=$qry->fetch_assoc()){
			if($row['is_repeating'] == 1){
				$rdata = json_decode($row['repeating_data']);
				foreach($rdata as $k =>$v){
					$row[$k] = $v;
				}
			}
			$data[] = $row;
		}
			return json_encode($data);
	}
	function delete_forum(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM forum_topics where id = ".$id);
		if($delete){
			return 1;
		}
	}
	function save_comment(){
		extract($_POST);
		$data = " comment = '".htmlentities(str_replace("'","&#x2019;",$comment))."' ";

		if(empty($id)){
			$data .= ", topic_id = '$topic_id' ";
			$data .= ", user_id = '{$_SESSION['login_id']}' ";
			$save = $this->db->query("INSERT INTO forum_comments set ".$data);
		}else{
			$save = $this->db->query("UPDATE forum_comments set ".$data." where id=".$id);
		}
		if($save)
			return 1;
	}
	function delete_comment(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM forum_comments where id = ".$id);
		if($delete){
			return 1;
		}
	}
	function save_event(){
		extract($_POST);
		$data = " title = '$title' ";
		$data .= ", schedule = '$schedule' ";
		$data .= ", content = '".htmlentities(str_replace("'","&#x2019;",$content))."' ";
		if($_FILES['banner']['tmp_name'] != ''){
						$_FILES['banner']['name'] = str_replace(array("(",")"," "), '', $_FILES['banner']['name']);
						$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['banner']['name'];
						$move = move_uploaded_file($_FILES['banner']['tmp_name'],'assets/uploads/'. $fname);
					$data .= ", banner = '$fname' ";

		}
		if(empty($id)){

			$save = $this->db->query("INSERT INTO events set ".$data);
		}else{
			$save = $this->db->query("UPDATE events set ".$data." where id=".$id);
		}
		if($save)
			return 1;
	}
	function delete_event(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM events where id = ".$id);
		if($delete){
			return 1;
		}
	}
	
	function participate(){
		extract($_POST);
		$data = " event_id = '$event_id' ";
		$data .= ", user_id = '{$_SESSION['login_id']}' ";
		$commit = $this->db->query("INSERT INTO event_commits set $data ");
		if($commit)
			return 1;

	}
}
