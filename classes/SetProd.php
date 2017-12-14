<?php

class SetProd
{

	private $db;
	
	public function set_db($con)
	{
	 $this->db=$con;
	}
	
	public function set_data($data,$fn,$dir)
	{
		
		$pn=$data['ProductName'];
		$cn=$data['CategName'];
		
		$pn = filter_var($pn, FILTER_SANITIZE_STRING);
		$cn = filter_var($cn, FILTER_SANITIZE_STRING);
		
		
		
		$stmt="INSERT INTO `tbl_product` (`product_id`, `fk_category_id`, `product_name`, `product_details`, `product_image_name`, `product_image_path`) VALUES (NULL, :f1, :f2, NULL, :f3, :f4)";
		
		try
		{
		$st=$this->db->prepare($stmt);
		
		
	
		
		$st->bindParam(':f1',$cn,PDO::PARAM_STR);
		$st->bindParam(':f2',$pn,PDO::PARAM_STR);
		$st->bindParam(':f3',$fn,PDO::PARAM_STR);
		$st->bindParam(':f4',$dir,PDO::PARAM_STR);
		
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