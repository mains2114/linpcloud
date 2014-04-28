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
	public function apikey_get($userid = FALSE)
	{
		if(!is_numeric($userid))
			$this->response(array('msg'=>'Invalid userid'), 400);
		else
		{
			$this->load->model('user_model');
			$result = $this->user_model->generate_apikey($userid);
			if($result === FALSE)
				$this->response(array('msg'=>'Generate apikey fail'), 400);
			else
				$this->response(array('userid'=>$userid,'apikey'=>$result), 200);
		}
	}

}