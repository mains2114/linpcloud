<?php
/*
description of table `tb_device`
id 			int
name 		varchar(20)
tags 		varchar(50)
about 		varchar(100)
locate 		varchar(50)
user_id		int
create_time int
update_time int
status 		tinyint
*/
class Device_model extends CI_Model
{

	public function __construct()
	{
		$this->load->database();
	}

	/**
	 * create a device
	 * $data is an array organized by controller
	 * @return boolean FALSE or last insert ID
	 */
	public function create($data)
	{
		$sql = $this->db->insert_string('tb_device', $data);
		$result = $this->db->query($sql);
		if ($result === TRUE)
		{
			return $this->db->insert_id();
		}
		else
		{
			return FALSE;
		}
	}

	public function get($device_id)
	{
		$sql = "SELECT * FROM `tb_device` WHERE `id`='$device_id'";
		$result = $this->db->query($sql);
		if ($result->num_rows > 0)
		{
			return $result->first_row('array');
		}
		else
		{
			return FALSE;
		}
	}

	public function update($device_id)
	{

	}

	public function delete($device_id)
	{
		if($device_id == FALSE)
			return FALSE;
		
		$where = "`id`='$device_id'";
		$data = array(
			'update_time' => time(),
			'status' => 0
		);
		$sql = $this->db->update_string('tb_device', $data, $where);
		$this->db->query($sql);
		if($this->db->affected_rows() > 0)
			return TRUE;
		else
			return FALSE;
	}

	public function get_devices($user_id)
	{
		$sql = "SELECT * FROM `tb_device` WHERE `userid`='$user_id' AND `status`=1";
		$result = $this->db->query($sql);
		if ($result->num_rows() > 0)
		{
			return $result->result_array();
		}
		else
		{
			return FALSE;
		}
	}
}