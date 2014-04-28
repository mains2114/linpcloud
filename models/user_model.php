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
			if ($result == TRUE)
				return $apikey;
			else
				return FALSE;
		}
	}

	public function get_token($userid)
	{
		$sql = "SELECT * FROM `tb_user_token` WHERE `userid`='$userid'";
		$result = $this->db->query($sql);
		if ($result->num_rows > 0)
		{
			$row = $result->first_row('array');
			$token = $row['token'];
			return $token;
		}
		else
		{
			return FALSE;
		}
	}

	public function generate_token($userid)
	{
		$data = array (
				'token' => sha1($userid . time()),
				'deadline' => time() + 30 * 60 * 1000
		);
		if ($this->get_token($userid) === FALSE)
		{
			$data['user_id'] = $userid;
			$sql = $this->db->insert_string('tb_user', $data);
			$result = $this->db->query($sql);
			if ($result === TRUE)
				return TRUE;
			else
				return FALSE;
		}
		else
		{
			$where = "`user_id`='$userid'";
			$sql = $this->db->update_string('tb_user', $data, $where);
			$result = $this->db->query($sql);
			if ($result === TRUE)
				return TRUE;
			else
				return FALSE;
		}
	}

	public function check_token($userid, $token)
	{
		$result = $this->get_token($userid);
		if ($result === FALSE)
		{
			return FALSE;
		}
		else if ($result == $token)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
}