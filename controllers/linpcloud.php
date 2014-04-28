<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Linpcloud extends REST_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('device_model');
		$this->load->model('sensor_model');
		$this->load->model('user_model');
	}

	function device_get($device_id)
	{
		$data = $this->device_model->get($device_id);
		if ($data === FALSE)
		{
			$this->response(array (
					'error' => 'device not found!'
			), 400);
		}
		else
		{
			$this->response($data, 200);
		}
	}

	function device_post()
	{
		if ($this->post('name') == FALSE)
		{
			$this->response(array (
					'error' => 'Field `name` required'
			), 400);
		}
		if ($this->post('userid') == FALSE)
		{
			$this->response(array (
					'error' => 'Field `userid` required'
			), 400);
		}
		// we should also check whether userid is valid
		
		$input = array (
				'id' => NULL,
				'name' => $this->post('name'),
				'tags' => ($this->post('tags') == FALSE) ? NULL : $this->post('tags'),
				'about' => ($this->post('about') == FALSE) ? NULL : $this->post('about'),
				'locate' => ($this->post('locate') == FALSE) ? NULL : $this->post('locate'),
				'userid' => $this->post('userid'),
				'create_time' => time(),
				'update_time' => time(),
				'status' => 1
		);
		$result = $this->device_model->create($input);
		if ($result === FALSE)
		{
			$this->response(NULL, 400);
		}
		else
		{
			$data = array (
					'id' => $result
			);
			$this->response($data, 200);
		}
	}

	function device_put($device_id)
	{
	}

	function device_delete($device_id)
	{
		$result = $this->device_model->delete($device_id);
		if ($result === FALSE)
			$this->response(array (
					'error' => 'delete fail'
			), 400);
		else
			$this->response(array (
					'result' => 'delete success',
					'deviceid' => $device_id
			), 200);
	}

	function devices_get($user_id)
	{
		$data = $this->device_model->get_devices($user_id);
		if ($data === FALSE)
			$this->response(array (
					'error' => 'no device found'
			), 400);
		else
			$this->response($data, 200);
	}

	function sensor_get($sensor_id)
	{
		$sensor_id = $this->get('sensorid');
		$data = $this->sensor_model->get($sensor_id);
		if ($data === FALSE)
		{
			$this->response(array (
					'error' => 'sensor not found!'
			), 400);
		}
		else
		{
			$this->response($data, 200);
		}
	}

	function sensor_post()
	{
		if ($this->post('name') == FALSE)
		{
			$this->response(array (
					'error' => 'Field `name` required'
			), 400);
		}
		if ($this->post('type') == FALSE)
		{
			$this->response(array (
					'error' => 'Field `type` required'
			), 400);
		}
		if ($this->post('deviceid') == FALSE)
		{
			$this->response(array (
					'error' => 'Field `deviceid` required'
			), 400);
		}
		// we should also check whether deviceid is valid
		
		$input = array (
				'id' => NULL,
				'name' => $this->post('name'),
				'type' => $this->post('type'),
				'tags' => ($this->post('tags') == FALSE) ? NULL : $this->post('tags'),
				'about' => ($this->post('about') == FALSE) ? NULL : $this->post('about'),
				'deviceid' => $this->post('deviceid'),
				'last_update' => time(),
				'last_data' => NULL,
				'status' => 1
		);
		$result = $this->sensor_model->create($input);
		if ($result === FALSE)
		{
			$this->response(NULL, 400);
		}
		else
		{
			$data = array (
					'id' => $result
			);
			$this->response($data, 200);
		}
	}

	function sensor_put($sensor_id)
	{
	}

	function sensor_delete($sensor_id)
	{
		$result = $this->sensor_model->delete($sensor_id);
		if ($result == FALSE)
			$this->response(array (
					'error' => 'delete fail'
			), 400);
		else
			$this->response(array (
					'result' => 'delete success',
					'sensorid' => $sensor_id
			), 200);
	}

	function sensors_get($device_id)
	{
		$data = $this->sensor_model->get_sensors($device_id);
		if ($data === FALSE)
			$this->response(array (
					'error' => 'no sensor found',
					'deviceid' => $device_id
			), 400);
		else
			$this->response($data, 200);
	}

	public function user_get($user_id)
	{
		if (is_numeric($user_id))
			$result = $this->user_model->get_info_by_id($user_id);
		
		if ($result === FALSE)
			$this->response(array (
					'msg' => 'User not found'
			), 400);
		else
		{
			$this->response($result, 200);
		}
	}

	public function user_post()
	{
		$username = $this->post('username');
		$password = $this->post('password');
		
		$data = array (
				'info' => 'Login Success',
				'username' => $username,
				'password' => $password
		);
		$this->response($data, 200);
	}

	public function login_post()
	{
		$username = $this->post('username');
		$password = $this->post('password');
		$result = $this->user_model->check_pwd($username, $password);
		
		if ($result !== FALSE)
		{
			$data = array (
					'info' => 'Login Success',
					'userid' => $result,
					'apikey' => '1234567890' // for now
						);
			$this->response($data, 200);
		}
		else
		{
			$this->response(array (
					'info' => 'Login Fail'
			), 400);
		}
	}
}