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

	/**
	 * get info of a specific device by device_id
	 * we should also check user permission and whether this device belong to the user
	 *
	 * @param int $device_id        	
	 */
	function device_get($device_id)
	{
		$user_id = $this->_check_apikey();
		
		$data = $this->device_model->get($device_id);
		if ($data === FALSE)
		{
			$this->response(array (
					'error' => 'device not found!'
			), 400);
		}
		else if ($user_id == $data['user_id'])
		{
			$this->response($data, 200);
		}
		else
		{
			$this->response(array (
					'error' => 'out of your permission'
			), 400);
		}
	}

	/**
	 * create a new device with info posted
	 * we should check some columns to ensure the data validation
	 * `name` required
	 * `user_id` valid
	 * of course, we should check apikey first
	 */
	function device_post()
	{
		$user_id = $this->_check_apikey();
		
		if ($this->post('name') == FALSE)
		{
			$this->response(array (
					'error' => 'Field `name` required'
			), 400);
		}
		
		$input = array (
				'id' => NULL,
				'name' => $this->post('name'),
				'tags' => ($this->post('tags') == FALSE) ? NULL : $this->post('tags'),
				'about' => ($this->post('about') == FALSE) ? NULL : $this->post('about'),
				'locate' => ($this->post('locate') == FALSE) ? NULL : $this->post('locate'),
				'user_id' => $user_id,
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

	/**
	 * delete a device by method DELETE
	 * 
	 * @param int $device_id        	
	 */
	function device_delete($device_id)
	{
		$user_id = $this->_check_apikey();
		
		$info = $this->device_model->get($device_id);
		if ($info === FALSE)
		{
			$this->response(array (
					'info' => 'device not found'
			), 400);
		}
		
		if ($info['user_id'] != $user_id)
		{
			$this->response(array (
					'info' => 'out of your permission'
			), 400);
		}
		
		$result = $this->device_model->delete($device_id);
		if ($result === FALSE)
		{
			$this->response(array (
					'info' => 'delete fail'
			), 400);
		}
		else
		{
			$this->response(array (
					'info' => 'delete success',
					'device_id' => $device_id
			), 200);
		}
	}

	/**
	 * Get devices list owned by the user
	 * get userid by apikey
	 */
	function devices_get()
	{
		$user_id = $this->_check_apikey();
		
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
		if ($this->post('device_id') == FALSE)
		{
			$this->response(array (
					'error' => 'Field `device_id` required'
			), 400);
		}
		// we should also check whether device_id is valid
		
		$input = array (
				'id' => NULL,
				'name' => $this->post('name'),
				'type' => $this->post('type'),
				'tags' => ($this->post('tags') == FALSE) ? NULL : $this->post('tags'),
				'about' => ($this->post('about') == FALSE) ? NULL : $this->post('about'),
				'device_id' => $this->post('device_id'),
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
					'device_id' => $device_id
			), 400);
		else
			$this->response($data, 200);
	}

	/**
	 * get user information
	 */
	public function user_get()
	{
		$apikey = $this->input->get_request_header('Apikey');
		if ($apikey === FALSE)
		{
			$this->response(array (
					'info' => 'header `apikey` lost, please login agian'
			), 400);
		}
		
		$result = $this->user_model->get_info_by_apikey($apikey);
		if ($result === FALSE)
		{
			$this->response(array (
					'info' => '`apikey` not match, please login agian'
			), 400);
		}
		
		$this->response($result, 200);
	}

	/**
	 * update user info
	 * (not ready)
	 */
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

	/**
	 * user login interface
	 * return apikey and userid
	 */
	public function login_post()
	{
		$username = $this->post('username');
		$password = $this->post('password');
		$result = $this->user_model->check_pwd_by_name($username, $password);
		
		if ($result === FALSE)
		{
			$this->response(array (
					'info' => 'Login Fail: password or username incorrect'
			), 400);
		}
		
		$data = array (
				'info' => 'Login Success',
				'userid' => $result['id'],
				'username' => $result['username'],
				'apikey' => $result['apikey']
		);
		$this->response($data, 200);
	}

	function _check_apikey()
	{
		$apikey = $this->input->get_request_header('Apikey');
		if ($apikey === FALSE)
		{
			$this->response(array (
					'info' => 'header `apikey` lost, please login agian'
			), 400);
		}
		
		$result = $this->user_model->get_info_by_apikey($apikey);
		if ($result === FALSE)
		{
			$this->response(array (
					'info' => '`apikey` not match, please login agian'
			), 400);
		}
		
		return $result['id'];
	}
}