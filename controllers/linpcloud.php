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
	public function device_get($device_id)
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
	public function device_post()
	{
		$user_id = $this->_check_apikey();
		
		if ($this->post('name') == FALSE)
		{
			$this->response(array (
					'info' => 'Field `name` required'
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

	/**
	 * update device info
	 *
	 * @param int $device_id        	
	 */
	public function device_put($device_id)
	{
		$user_id = $this->_check_apikey();
		
		if ($user_id != $this->put('user_id'))
		{
			$this->response(array (
					'info' => 'out of your permission'
			), 400);
		}
		
		if ($this->put('name') == FALSE)
		{
			$this->response(array (
					'info' => 'Field `name` required'
			), 400);
		}
		
		$input = array (
				'name' => $this->put('name'),
				'tags' => ($this->put('tags') == FALSE) ? NULL : $this->put('tags'),
				'about' => ($this->put('about') == FALSE) ? NULL : $this->put('about'),
				'locate' => ($this->put('locate') == FALSE) ? NULL : $this->put('locate'),
				'update_time' => time()
		);
		$result = $this->device_model->update($device_id, $input);
		if ($result === TRUE)
		{
			$this->response(array (
					'info' => 'update info success'
			), 200);
		}
		else
		{
			$this->response(array (
					'info' => 'update info fail'
			), 400);
		}
	}

	/**
	 * delete a device by method DELETE
	 *
	 * @param int $device_id        	
	 */
	public function device_delete($device_id)
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
	public function devices_get()
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

	/**
	 * get info of a specific sensor
	 * check whether this sensor belong to the user
	 *
	 * @param int $sensor_id        	
	 */
	public function sensor_get($sensor_id)
	{
		$data = $this->_check_sensor($sensor_id);
		
		$this->response($data, 200);
	}

	/**
	 * create a new sensor
	 */
	public function sensor_post()
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
		
		$device_id = $this->post('device_id');
		$this->_check_device($device_id);
		
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
			$this->response(array (
					'info' => 'sensor created fail'
			), 400);
		}
		else
		{
			$data = array (
					'id' => $result
			);
			$this->response($data, 200);
		}
	}

	/**
	 * update sensor info
	 *
	 * @param int $sensor_id        	
	 */
	public function sensor_put($sensor_id)
	{
		if ($this->put('name') == FALSE)
		{
			$this->response(array (
					'error' => 'Field `name` required'
			), 400);
		}
		if ($this->put('type') == FALSE)
		{
			$this->response(array (
					'error' => 'Field `type` required'
			), 400);
		}
		if ($this->put('device_id') == FALSE)
		{
			$this->response(array (
					'error' => 'Field `device_id` required'
			), 400);
		}
		
		$device_id = $this->put('device_id');
		$this->_check_sensor($sensor_id, $device_id);
		
		$input = array (
				'name' => $this->put('name'),
				'type' => $this->put('type'),
				'tags' => ($this->put('tags') == FALSE) ? NULL : $this->put('tags'),
				'about' => ($this->put('about') == FALSE) ? NULL : $this->put('about'),
				'device_id' => $this->put('device_id'),
				'last_update' => time()
		);
		
		$result = $this->sensor_model->update($sensor_id, $input);
		if ($result === FALSE)
		{
			$this->response(array (
					'info' => "sensor `$sensor_id` update fail"
			), 400);
		}
		else
		{
			$this->response(array (
					'info' => "sensor `$sensor_id` update success"
			), 200);
		}
	}

	/**
	 * logically delete a sensor by change status to '0'
	 * 
	 * @param int $sensor_id        	
	 */
	public function sensor_delete($sensor_id)
	{
		$this->_check_sensor($sensor_id);
		
		$result = $this->sensor_model->delete($sensor_id);
		if ($result == FALSE)
			$this->response(array (
					'info' => "delete sensor `$sensor_id` fail"
			), 400);
		else
			$this->response(array (
					'info' => "delete sensor `$sensor_id` success"
			), 200);
	}

	/**
	 * get all sensors under the specific device
	 * 
	 * @param int $device_id        	
	 */
	public function sensors_get($device_id)
	{
		$data = $this->sensor_model->get_sensors($device_id);
		if ($data === FALSE)
			$this->response(array (
					'info' => "no sensors found in device `$device_id`"
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

	/**
	 * check apikey in the header, and get user_id
	 *
	 * @return int user_id
	 */
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

	/**
	 * check if device belongs to the specific user
	 *
	 * @param int $device_id        	
	 * @param int $user_id        	
	 * @return boolean TRUE | exit
	 */
	function _check_device($device_id)
	{
		$user_id = $this->_check_apikey();
		
		$result = $this->device_model->get($device_id);
		if ($result === FALSE)
		{
			$this->response(array (
					'info' => "device `$device_id` not found"
			), 400);
		}
		
		if ($result['user_id'] != $user_id)
		{
			$this->response(array (
					'info' => "device `$device_id` out of your permission"
			), 400);
		}
		
		return TRUE;
	}

	/**
	 * check if sensor belong to the specific user
	 * or check if this sensors belong to the specific device
	 *
	 * @param int $sensor_id        	
	 * @return boolean array | exit
	 */
	function _check_sensor($sensor_id, $device_id = FALSE)
	{
		$result = $this->sensor_model->get($sensor_id);
		if ($result === FALSE)
		{
			$this->response(array (
					'info' => "sensor `$sensor_id` not found!"
			), 400);
		}
		
		if ($device_id !== FALSE && $device_id != $result['device_id'])
		{
			$this->response(array (
					'info' => "sensor `$sensor_id` do not belong to device `$device_id`!"
			), 400);
		}
		
		$this->_check_device($result['device_id']);
		
		return $result;
	}
}