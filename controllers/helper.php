<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

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
		$data = array(
		'sourse'=>$sourse,
		'md5'=>md5($sourse)
		);
		$this->response($data,200);
	}

}