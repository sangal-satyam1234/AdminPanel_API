<?php

class ChangePass
{

 private $db;
 
 public function set_db($con)
 {
	$this->db=$con;
 }
 
 public function set_pass($pass,$id)
 {
 
	$pass=filter_var($pass,FILTER_SANITIZE_STRING);
	
	$hashed_password = password_hash($pass, PASSWORD_DEFAULT);
	
	$stmt="UPDATE authentication SET auth_pass=:field1 WHERE auth_id=:field2 ; ";
	
	try
	{
		$st=$this->db->prepare($stmt);
		$this->db->beginTransaction();
			
			$st->bindParam(':field1',$hashed_password,PDO::PARAM_STR);
			$st->bindParam(':field2',$id,PDO::PARAM_INT);
	
		$st->execute();
		
		if($st->rowCount() > 0)
		{
			$this->db->commit();
			return "success";
		}
		else
		{
			$this->db->rollBack();
			return "No Such User";
		}
		
	}
	catch(PDOException $e)
	{
			$this->db->rollBack();	
			return $e;
	}
	
 
 
 }





}


?>