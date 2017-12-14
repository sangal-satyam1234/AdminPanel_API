<?php

class GetBanner
{
	private $db;

	public function set_db($con)
	{
	 $this->db=$con;
	}
	
	public function get_table()
	{
	
		$stmt="SELECT * from tbl_banners" ;
		
		try
		{ 
		
		$result=$this->db->query($stmt);
		
		$output = $result->fetchAll();
		$no=$result->rowCount();
		$data=json_encode($output);
		$res=array();
		
		$res['draw'] = 1;
		$res['recordsTotal']=$no;
		$res['recordsFiltered']= $no;
		$res['data']= $output;
		}
		catch(PDOException $e)
		{
		 return $e;
			
		}
			 
		return json_encode($res);
	
	
	}



}




?>