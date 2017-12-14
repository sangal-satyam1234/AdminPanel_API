<?php

class GetUser
{
	private $db;

	public function set_db($con)
	{
	 $this->db=$con;
	}
	
	public function get_table($id)
	{
	
		
		$stmt="SELECT * from tbl_users WHERE user_id=:f1 " ;
		
		$data=array();
		try
		{ 
		
		$st=$this->db->prepare($stmt);
		
		$st->bindParam(':f1',$id,PDO::PARAM_INT);
		
		$st->execute();
		
		if($st->rowCount() == 0)
		{
			$data['message']="UserId does not exists";
			return $data;
		}
		
		$row=$st->fetch(PDO::FETCH_ASSOC);
		
		$data['id']=$id;
		$data['name']=$row['user_name'];
		$data['dob']=$row['user_dob'];
		$data['email']=$row['user_email'];
		$data['phone']=$row['user_phone'];
		$data['address']=$row['user_address'];
		$data['Verified']=$row['is_validated'];
		
		
		
		}
		catch(PDOException $e)
		{
		 $data['message']=$e;
		 return $data;
			
		}
			 
		$data['message']="Success";
		return $data;
	
	
	}



}




?>