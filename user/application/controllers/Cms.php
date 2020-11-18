<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include("assets/global/admin.global.php");
class Cms extends CI_Controller {

	public function __construct()
	{
		 parent::__construct();
		 date_default_timezone_set('Africa/Lagos');
				  
		 $this->load->model('Admin_model');	
		 $this->load->model('Base_model');		 	 
		 $this->load->model('User_model');
		 $this->load->model('Game_model');
		 $this->load->model('Option_model');
		 $this->load->model('PlayerOption_model');
		 $this->load->model('Setting_model');
		 $this->load->model('Terminal_model');
		 $this->load->model('TerminalOption_model');
		 $this->load->model('Week_model');		 
		 $this->load->model('Prize_model');
		 $this->load->model('FundRequest_model');					 
	}
	public function index()
	{
		if($this->logonCheck()) {
			redirect('Cms/stake/', 'refresh');
		} 
	}
	public function login(){
		$this->load->view("user/view_login");
	}

	public function logout(){
		$this->session->sess_destroy();
		redirect('Cms/', 'refresh');
	}

	public function auth_user() {
		global $MYSQL;
		$email = $this->input->post('email');
		$password = $this->input->post('password');

		$row = $this->User_model->getRow(array('user_id'=>$email));
		if($row==null)
			$row = $this->User_model->getRow(array('email'=>$email));

		if($row==null)
		{
			redirect( 'Cms/login', 'refresh');
			return;
		}

		if($password != $row->password)
		{
			redirect( 'Cms/login', 'refresh');
			return;
		}

		$sess_data = array('player_id'=>$row->Id, 'is_login'=>true);
		$this->session->set_userdata($sess_data);
		redirect('Cms/stake/', 'refresh');
	}

	public function updateAccount() {
		if($this->logonCheck()){
			global $MYSQL;
			$email = $this->input->post('email');
			$password = $this->input->post('password');
			$id = $this->session->userdata('player_id');
			$npass = password_hash($password, PASSWORD_DEFAULT);
			$updateAry = array('email'=>$email,
				'password'=>$npass,
				'modified'=>date('Y-m-d'));
			$ret = $this->User_model->updateData($MYSQL['_adminDB'], array('Id'=>$id), $updateAry);
			if($ret > 0) 
				$this->session->set_flashdata('messagePr', 'Update Account Successfully..');
			else
				$this->session->set_flashdata('messagePr', 'Unable to Update Account..');
			redirect('Cms/dashboard/', 'refresh');
		}
	}

	public function stake()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'stake';
		$param['kind'] = '';
		$param['table'] = '';
		$param['options'] = $this->Option_model->getDatas(array('status'=>1));

		
		$curWeekNo = $this->Setting_model->getCurrentWeekNo();
		$param['curWeekNo'] = $curWeekNo;
		$param['curWeek'] = $this->Week_model->getRow(array('week_no'=>$curWeekNo));
		$param['games'] = $this->Game_model->getDatas(array('status'=>1, 'week_no'=>$curWeekNo), 'game_no');

		$this->load->view("user/include/header", $param);
		$this->load->view("user/view_stake",$param);
		$this->load->view("user/include/footer",$param);
	}

	public function fund_account()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'fund_account';
		$param['kind'] = '';
		$param['table'] = '';


		$this->load->view("user/include/header", $param);
		$this->load->view("user/view_fund_account",$param);
		$this->load->view("user/include/footer",$param);
	}

	public function bet_history()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'bet_history';
		$param['kind'] = 'table';
		$param['table'] = '';
		$param['curWeekNo'] = $this->Setting_model->getCurrentWeekNo();
		$param['weeks'] = $this->Week_model->getDatas(null);
		$param['terminals'] = $this->Terminal_model->getDatas(array('status'=>1));
		$param['players'] = $this->User_model->getDatas(array('status'=>1, 'type'=>'player'));

		$this->load->view("user/include/header", $param);
		$this->load->view("user/view_bet_history",$param);
		$this->load->view("user/include/footer",$param);
	}

	public function win_list()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'win_list';
		$param['kind'] = 'table';
		$param['table'] = '';		
		$param['curWeekNo'] = $this->Setting_model->getCurrentWeekNo();		
		$param['weeks'] = $this->Week_model->getDatas(null);
		$param['terminals'] = $this->Terminal_model->getDatas(array('status'=>1));
		$param['players'] = $this->User_model->getDatas(array('status'=>1, 'type'=>'player'));


		$this->load->view("user/include/header", $param);
		$this->load->view("user/view_win_list",$param);
		$this->load->view("user/include/footer",$param);
	}	
	public function bet_result()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'bet_result';
		$param['kind'] = 'table';
		$param['table'] = '';	
		$param['weeks'] = $this->Week_model->getDatas(null);			

		$this->load->view("user/include/header", $param);
		$this->load->view("user/view_bet_result",$param);
		$this->load->view("user/include/footer",$param);
	}	



	
	public function personal_details()
	{
		if(!$this->logonCheck()) return;
		$param['uri'] = 'personal_details';
		$param['kind'] = 'table';
		$param['table'] = 'tbl_bets';

		$this->load->view("user/include/header", $param);
		$this->load->view("user/view_personal_details",$param);
		$this->load->view("user/include/footer",$param);
	}	
}
