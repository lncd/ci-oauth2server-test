<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function index()
	{
		$this->load->model('oauthdb');
		$oauth = new oauth2server\Server();

		echo '<pre>';
		var_dump(get_declared_classes());
		echo '</pre>';
		
		//$oauth->registerDbAbstractor($this->oauthdb);


	}



}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */