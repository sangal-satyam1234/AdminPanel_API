<?php

class Login
{

	private $db;
	
	public function set_db($con)
	{
	 $this->db=$con;
	}
	
	public function check_login($info)
	{
		//$u_name=$info['name'];
		//$u_dob=$info['dob'];
		//$u_addr=$info['addr'];
		$u_email=$info['email'];
		//$u_phone=$info['phone'];
		$u_pass=$info['password'];
		
		if(!isset($u_email,$u_pass))
		{
			$data['message']="Insufficient Parameters";
			return $data;
		}
		
		
		//$u_name = filter_var($u_name, FILTER_SANITIZE_STRING);
		//$u_dob = filter_var($u_dob, FILTER_SANITIZE_STRING);
		//$u_addr = filter_var($u_addr, FILTER_SANITIZE_STRING);
		$u_pass = filter_var($u_pass, FILTER_SANITIZE_STRING);
		$u_email= filter_var($u_email, FILTER_SANITIZE_EMAIL);
		//$u_phone=filter_var($u_phone, FILTER_SANITIZE_NUMBER_INT);
		
		//$hashed_password = password_hash($u_pass, PASSWORD_DEFAULT);
		
		if (!filter_var($u_email, FILTER_VALIDATE_EMAIL)) {
			$data['message']="Invalid email address";
			return $data;
			//return "Invalid email address";
		}

		//if (!filter_var($u_phone, FILTER_VALIDATE_INT)) {
		//	return "Invalid phone number";
		//}

			
		$data=array();
		
		$stmt1="SELECT * FROM tbl_users WHERE user_email=:em";
		
		try
		{
		$st=$this->db->prepare($stmt1);
		
		
		$st->bindParam(':em',$u_email,PDO::PARAM_STR);
		//$st->bindParam(':ph',$u_phone,PDO::PARAM_INT);
		//$st->bindParam(':pwd',$hashed_password,PDO::PARAM_STR);
		//$st->bindParam(':nm',$u_name,PDO::PARAM_STR);
		//$st->bindParam(':dob',$u_dob,PDO::PARAM_STR);
		//$st->bindParam(':ad',$u_addr,PDO::PARAM_STR);
		
		$st->execute();
		
		$rows=$st->fetch(PDO::FETCH_ASSOC);
		if($rows['user_email'] == NULL){
			$data['message']="Email Id is not registered";
			return $data;
		//	return "Email-Id is not registered";
			
		}
		
		$hashed_password=$rows['user_password'];
		$user_id=$rows['user_id'];
		
		if(!password_verify($u_pass,$hashed_password))
			{
				$data['message']="Invalid Password";
			return $data;
			//return "Invalid Password"; 
		}
		
		
		}
		catch(PDOException $e)
		{
			
			if($e->errorInfo[1] == 1062)
			{
				$data['message']="Unexpected Error";
				return $data;
				//return "Phone Number/Email-Id is already used";
			}
			$data['message']=$e;
			return $data;
			//return $e;
			
		}
		
		
		$data['id']=$user_id;
		$data['message']="Success";
		return $data;
	
	}



}


?>