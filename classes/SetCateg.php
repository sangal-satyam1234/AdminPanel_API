<?php

class SetCateg
{

	private $db;
	
	public function set_db($con)
	{
	 $this->db=$con;
	}
	
	public function set_data($ln)
	{
		$ln = filter_var($ln, FILTER_SANITIZE_STRING);
		
		
		
		$stmt="INSERT INTO `tbl_category` (`category_id`, `category_name`, `category_details`) VALUES (NULL, :field1, NULL)";
		
		try
		{
		$st=$this->db->prepare($stmt);
		
		
	
		
		$st->bindParam(':field1',$ln,PDO::PARAM_STR);
		
		$st->execute();
		
		
		
		
		
		}
		catch(PDOException $e)
		{
			
		
			return $e;
			
		}
		
		return "Success";
	
	}



}


?>