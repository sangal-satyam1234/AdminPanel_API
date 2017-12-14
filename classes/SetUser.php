<?php

class SetUser
{

	private $db;
	
	public function set_db($con)
	{
	 $this->db=$con;
	}
	
	public function set_data($info)
	{
		$u_name=$info['name'];
		$u_dob=$info['dob'];
		$u_addr=$info['addr'];
		$u_email=$info['email'];
		$u_phone=$info['phone'];
		$u_pass=$info['password'];
		
		if(!isset($u_name,$u_dob,$u_addr,$u_email,$u_phone,$u_pass))
		{
			return "Insufficient Parameters";
		}
		
		
		$u_name = filter_var($u_name, FILTER_SANITIZE_STRING);
		$u_dob = filter_var($u_dob, FILTER_SANITIZE_STRING);
		$u_addr = filter_var($u_addr, FILTER_SANITIZE_STRING);
		$u_pass = filter_var($u_pass, FILTER_SANITIZE_STRING);
		$u_email= filter_var($u_email, FILTER_SANITIZE_EMAIL);
		$u_phone=filter_var($u_phone, FILTER_SANITIZE_NUMBER_INT);
		
		$hashed_password = password_hash($u_pass, PASSWORD_DEFAULT);
		
		if (!filter_var($u_email, FILTER_VALIDATE_EMAIL)) {
			return "Invalid email address";
		}

		if (!filter_var($u_phone, FILTER_VALIDATE_INT)) {
			return "Invalid phone number";
		}

			
		
		
		$stmt="INSERT INTO `tbl_users` (`user_id`, `user_email`, `user_phone`, `user_password`, `user_name`, `user_dob`, `user_address`) VALUES (NULL, :em, :ph, :pwd, :nm, :dob,:ad )";
		
		try
		{
		$st=$this->db->prepare($stmt);
		
		
		$st->bindParam(':em',$u_email,PDO::PARAM_STR);
		$st->bindParam(':ph',$u_phone,PDO::PARAM_INT);
		$st->bindParam(':pwd',$hashed_password,PDO::PARAM_STR);
		$st->bindParam(':nm',$u_name,PDO::PARAM_STR);
		$st->bindParam(':dob',$u_dob,PDO::PARAM_STR);
		$st->bindParam(':ad',$u_addr,PDO::PARAM_STR);
		
		$st->execute();
		
		
		
		
		
		}
		catch(PDOException $e)
		{
			
			if($e->errorInfo[1] == 1062)
			{
				return "Phone Number/Email-Id is already used";
			}
			return $e;
			
		}
		
		return "Success";
	
	}



}


?>