<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Helper extends REST_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('device_model');
		$this->load->model('sensor_model');
		$this->load->model('user_model');
	}

	function md5_get($sourse)
	{
		$data = array (
				'sourse' => $sourse,
				'md5' => md5($sourse)
		);
		$this->response($data, 200);
	}

	public function apikey_get($user_id = FALSE)
	{
		if (! is_numeric($user_id))
			$this->response(array (
					'msg' => 'Invalid user_id'
			), 400);
		else
		{
			$this->load->model('user_model');
			$result = $this->user_model->generate_apikey($user_id);
			if ($result === FALSE)
				$this->response(array (
						'msg' => 'Generate apikey fail'
				), 400);
			else
				$this->response(array (
						'user_id' => $user_id,
						'apikey' => $result
				), 200);
		}
	}

	function timestamp_get($timestamp = FALSE)
	{
		if($timestamp == FALSE)
			$timestamp = time();
		
		$data = array (
				'datetime' => date('Y-m-d H:i:s', $timestamp),
				'timestamp' => $timestamp
		);
		$this->response($data, 200);
	}
	
	function put_put()
	{
		$data = array (
				'datetime' => date('Y-m-d H:m:s', time()),
				'timestamp' => time(),
				'params' => $this->put()
		);
		$this->response($data, 200);
	}
	
	function post_post()
	{
		$data = array (
				'datetime' => date('Y-m-d H:m:s', time()),
				'timestamp' => time(),
				'params' => $this->post()
		);
		$this->response($data, 200);
	}
	
	function get_get()
	{
		$data = array (
				'datetime' => date('Y-m-d H:m:s', time()),
				'timestamp' => time(),
				'params' => $this->get()
		);
		$this->response($data, 200);
	}
	
	function delete_delete()
	{
		$data = array (
				'datetime' => date('Y-m-d H:m:s', time()),
				'timestamp' => time(),
				'params' => $this->delete()
		);
		$this->response($data, 200);
	}
}