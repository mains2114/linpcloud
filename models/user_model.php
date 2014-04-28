<?php

class User_model extends CI_Model
{

	public function __construct()
	{
		$this->load->database();
	}

	public function get_info_by_name($username)
	{
		$sql = "SELECT * FROM `tb_user` WHERE `username`='$username'";
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

	public function get_info_by_id($userid)
	{
		$sql = "SELECT * FROM `tb_user` WHERE `id`='$userid'";
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

	public function check_pwd_by_name($username, $password)
	{
		$result = $this->get_info_by_name($username);
		if ($result === FALSE)
			return FALSE;
		else if ($result['password'] == md5(trim($password)))
			return $result['id'];
		else
			return FALSE;
	}

	public function check_pwd_by_id($userid, $password)
	{
		$result = $this->get_info_by_id($userid);
		if ($result === FALSE)
			return FALSE;
		else if ($result['password'] == md5(trim($password)))
			return $result['id'];
		else
			return FALSE;
	}

	public function generate_apikey($userid)
	{
		$result = $this->get_info_by_id($userid);
		if ($result === FALSE)
		{
			return FALSE;
		}
		else
		{
			$apikey = md5($userid . time());
			$data = array (
					'apikey' => $apikey
			);
			$where = "`id`='$userid'";
			$sql = $this->db->update_string('tb_user', $data, $where);
			$result = $this->db->query($sql);
			if($result == TRUE)
				return $apikey;
			else
				return FALSE;
		}
	}
}