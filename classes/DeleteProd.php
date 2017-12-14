<?php

class DeleteProd
{
	private $db;

	public function set_db($con)
	{
	 $this->db=$con;
	}
	
	public function delete_data($id)
	{
		
		
		$stmt="DELETE FROM `tbl_product` WHERE product_id=:f1";
		
		try
		{
		$st=$this->db->prepare($stmt);
		
		
	
		
		$st->bindParam(':f1',$id,PDO::PARAM_INT);
		
		
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