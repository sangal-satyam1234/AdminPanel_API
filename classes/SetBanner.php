<?php

class SetBanner
{

	private $db;
	
	public function set_db($con)
	{
	 $this->db=$con;
	}
	
	public function set_data($data,$fn,$dir)
	{
		
		$bn=$data['BannerName'];
		//$cn=$data['CategName'];
		
		$bn = filter_var($bn, FILTER_SANITIZE_STRING);
		//$cn = filter_var($cn, FILTER_SANITIZE_STRING);
		
		
		
		$stmt="INSERT INTO `tbl_banners` (`banner_id`, `banner_name`, `banner_ipath`, `banner_iname`) VALUES (NULL, :f1, :f2, :f3)";
		
		try
		{
		$st=$this->db->prepare($stmt);
		
		
	
		
		$st->bindParam(':f1',$bn,PDO::PARAM_STR);
		$st->bindParam(':f2',$dir,PDO::PARAM_STR);
		$st->bindParam(':f3',$fn,PDO::PARAM_STR);
		//$st->bindParam(':f4',$dir,PDO::PARAM_STR);
		
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