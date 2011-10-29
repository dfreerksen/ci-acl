<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function index()
	{
		//if ( ! $this->acl->has_access())
		//{
			//show_error('You do not have access to this section');
		//}

		$this->load->view('welcome_message');

		$this->output->enable_profiler(TRUE);
	}
}
