<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function index()
	{
		//$this->load->model('oauthdb');
		
		$oauth = new OAuth2\Server();

		//$oauth->registerDbAbstractor($this->oauthdb);

		//$oauth->test();

	}



}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */