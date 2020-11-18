<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cms_api extends CI_Controller {
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
		$this->load->model('Bet_model');
		$this->load->model('Summary_model');		
	}

	public function reply($status, $message, $data)
	{
		$result = array('status'=>$status, 'message'=>$message, 'data'=>$data);
		echo json_encode($result);
	}	
	public function ajaxDel() {
		if($this->logonCheck()) {
			global $MYSQL;
			$Id = $this->input->post('Id');
			$tbl_Name = $this->input->post('tbl_Name');
			if($tbl_Name !='') {
				$conAry = array('Id' => $Id);
				$updateAry = array('isdeleted'=>'1');
				$this->Base_model->updateData($tbl_Name, $conAry, $updateAry);
				echo json_encode(array("status" => TRUE));	
			} else {
				echo json_encode(array("status" => FALSE));	
			}
		}
	}
	public function delUser() {
		if($this->logonCheck()) {
			global $MYSQL;
			$Id = $this->input->post('Id');
			$tbl_Name = $this->input->post('tbl_Name');
			if($tbl_Name !='') {
				$this->Base_model->deleteByField($tbl_Name, "Id", $Id);
				echo json_encode(array("status" => TRUE));	
			} else {
				echo json_encode(array("status" => FALSE));	
			}
		}
	}
	public function getDataById() {
		$this->logonCheck();

		$Id = $this->input->post("Id");
		$tableName = $this->input->post("tbl_Name");
		$ret = $this->Base_model->getRow($tableName, array('Id'=>$Id));
		echo json_encode($ret);
	}
	public function delData()
	{
		$this->logonCheck();
		$Id = $this->input->post("Id");
		$tableName = $this->input->post("tbl_Name");
		$ret = $this->Base_model->deleteRow($tableName, array('Id'=>$Id));
		echo "1";
	}

	public function get_bets($week=0, $userId=0, $terminalId=0)
	{
		$this->logonCheck();
		$cond = array();
		if($week !=0) $cond['week'] = $week;
		if($userId !=0) $cond['user_id'] = $userId;
		if($terminalId!=0)$cond['terminal_id'] = $terminalId;		

		$datas = $this->Bet_model->getDatas($cond);
		$resData = array();
		foreach($datas as $data)
		{
			$player = "";
			if($data->user_id!=0)
			{
				$user = $this->User_model->getRow(array('Id'=>$data->user_id));
				if($user==null) continue;
				$player = $user->user_id;
			}
			else if($data->terminal_id!=0)
			{
				$terminal = $this->Terminal_model->getRow(array('Id'=>$data->terminal_id));
				if($terminal==null) continue;
				$player = $terminal->terminal_no;
			}

			if($player=="")continue;
			$option = $this->Option_model->getRow(array('Id'=>$data->option_id));
			if($option==null) continue;

			$agent = $this->User_model->getRow(array('Id'=>$data->agent_id));
			if($agent== null) continue;

			$row = array();
			$row[] = $data->week;
			$row[] = $data->bet_id;
			$row[] = $player;
			$row[] = $data->type;
			$row[] = $option->name;
			$row[] = implode(',', json_decode($data->under));

			$gamelist = "";			
			if($data->type=='Group')
			{
				$groups = json_decode($data->gamelist, true);
				// for($iGrp=0; $iGrp<count($groups); $iGrp++)
				// {
				// 	$line = "<p>".chr(0x41 + $iGrp).'('.$groups[$iGrp]->under[0].'): '.implode(',', $groups[$iGrp]->list).'</p>';
				// 	$gamelist .= $line;
				// }
				for($iGrp=0; $iGrp<count($groups); $iGrp++)
				{
					$line = "<p>".chr(0x41 + $iGrp).'('.$groups[$iGrp]['under'][0].'):'.implode(',', $groups[$iGrp]['list']).'</p>';
					$gamelist .= $line;
				}
			}
			else
				$gamelist = implode(',',json_decode($data->gamelist));

			$row[] = $gamelist;
			$row[] = $data->score_list;
			$row[] = $data->apl;
			$row[] = $data->stake_amount;
			if($data->status==1)$row[] = "<label class='label label-success'>Active</label>";
			else if($data->status==2)$row[] = "<label class='label label-danger'>Void</label>";
			$row[] = $data->win_result;
			$row[] = $data->won_amount;
			$row[] = $data->ticket_no;
			$row[] = $agent->user_id;
			$row[] = $data->bet_time;

			$strAction = "";
			if($data->status==1)
				$strAction = '<a href="javascript:void(0)" class="on-default remove-row" '.
					'onclick="onVoid('.$data->Id.')" title="Void" ><i class="fa fa-trash-o text-danger">Void</i></a>';
			$row[]=$strAction;
			$resData[]=$row;
		}

		$output = array(
			"draw" => null,
			"recordsTotal" => count($resData),
			"recordsFiltered" => count($resData),
			"data" => $resData,
		);
		echo json_encode($output);
	}

	public function get_win_lists($week=0, $userId=0, $terminalId=0)
	{
		$this->logonCheck();
		$cond = array();
		if($week !=0) $cond['week'] = $week;
		if($userId !=0) $cond['user_id'] = $userId;
		if($terminalId!=0)$cond['terminal_id'] = $terminalId;

		$datas = $this->Bet_model->getDatas($cond);
		$resData = array();
		foreach($datas as $data)
		{
			if($data->won_amount==0) continue;

			$player = "";
			if($data->user_id!=0)
			{
				$user = $this->User_model->getRow(array('Id'=>$data->user_id));
				if($user==null) continue;
				$player = $user->user_id;
			}
			else if($data->terminal_id!=0)
			{
				$terminal = $this->Terminal_model->getRow(array('Id'=>$data->terminal_id));
				if($terminal==null) continue;
				$player = $terminal->terminal_no;
			}

			if($player=="")continue;
			$option = $this->Option_model->getRow(array('Id'=>$data->option_id));
			if($option==null) continue;

			$agent = $this->User_model->getRow(array('Id'=>$data->agent_id));
			if($agent== null) continue;

			$row = array();
			$row[] = $data->week;
			$row[] = $data->bet_id;
			$row[] = $player;
			$row[] = $data->type;
			$row[] = $option->name;
			$row[] = implode(',', json_decode($data->under));

			$gamelist = "";			
			if($data->type=='Group')
			{
				$groups = json_decode($data->gamelist, true);
				// for($iGrp=0; $iGrp<count($groups); $iGrp++)
				// {
				// 	$line = "<p>".chr(0x41 + $iGrp).'('.$groups[$iGrp]->under[0].'): '.implode(',', $groups[$iGrp]->list).'</p>';
				// 	$gamelist .= $line;
				// }
				for($iGrp=0; $iGrp<count($groups); $iGrp++)
				{
					$line = "<p>".chr(0x41 + $iGrp).'('.$groups[$iGrp]['under'][0].'):'.implode(',', $groups[$iGrp]['list']).'</p>';
					$gamelist .= $line;
				}
			}
			else
				$gamelist = implode(',',json_decode($data->gamelist));

			$row[] = $gamelist;
			$row[] = $data->score_list;
			$row[] = $data->apl;
			$row[] = $data->stake_amount;
			if($data->status==1)$row[] = "Active";
			else $row[] = "Disable";
			$row[] = $data->win_result;
			$row[] = $data->won_amount;
			$row[] = $data->ticket_no;
			$row[] = $agent->user_id;
			$row[] = $data->bet_time;
			$resData[]=$row;
		}

		$output = array(
			"draw" => null,
			"recordsTotal" => count($resData),
			"recordsFiltered" => count($resData),
			"data" => $resData,
		);
		echo json_encode($output);
	}

	public function get_check_bet_result($week=0)
	{
		$this->logonCheck();
		$cond = array();
		if($week !=0) $cond['week'] = $week;

		$resData = array();
		$output = array(
			"draw" => null,
			"recordsTotal" => count($resData),
			"recordsFiltered" => count($resData),
			"data" => $resData,
		);
		echo json_encode($output);		

	}

	public function get_report($week=0, $userId=0, $terminalId=0)
	{
		$this->logonCheck();

		$curWeekNo = $week;
		$curWeek = $this->Week_model->getRow(array('week_no'=>$curWeekNo));
		if($curWeek==null)
		{
			$output = array(
				"draw" => null,
				"recordsTotal" => 0,
				"recordsFiltered" => 0,
				"data" => [],
			);			
			echo json_encode($output);				
			return;
		}

		$summaries = null;
		if($terminalId > 0)
			$summaries = $this->Summary_model->getDatas(array('week_no'=>$curWeekNo, 'terminal_id'=>$terminalId));	
		else
			$summaries = $this->Summary_model->getDatas(array('week_no'=>$curWeekNo));

		if($terminalId > 0)	$bets = $this->Bet_model->getDatas(array('terminal_id'=>$terminalId, 'week'=>$curWeekNo, 'status'=>1));
		else $bets = $this->Bet_model->getDatas(array('week'=>$curWeekNo, 'status'=>1));

		$total_sale=0;
		$total_payable=0;
		$total_win= 0;
		$bal_agent="";
		$bal_company="";
		$status= "";

		if(count($summaries)==0)
		{
			$output = array(
				"draw" => null,
				"recordsTotal" => 0,
				"recordsFiltered" => 0,
				"data" => [],
			);
			echo json_encode($output);				
			return;
		}

		//$odd_summary = array();
		foreach($summaries as $summary)
		{
			$option = $this->Option_model->getRow(array('Id'=>$summary->option_id));
			if($option==null) continue;

			$total_sale += $summary->sales;
			$total_payable += $summary->payable;
			$total_win += $summary->win;			

			$count = 0;			
			if($summary->sales >0)
			{
				foreach($bets as $bet){
					if($summary->option_id== $bet->option_id) $count++;
				}	
			}
			// $odd_summary[]=array('option'=>$option->name, 'count'=>$count, 
			// 				'sale'=>$summary->sales, 'payable'=>$summary->payable,'win'=>$summary->win);

		}

		if ($total_payable > $total_win) {
			$bal_company = $total_payable - $total_win;
			$status = '<label class="label label-success">green</label>';
		}
		else {
			$bal_agent = $total_win - $total_payable;
			$status = '<label class="label label-danger">red</label>';
		}		

		// $agentId = "";
		// $agent=$this->User_model->getRow(array('Id'=>$terminal->agent_id));
		// if($agent!=null) $agentId= $agent->user_id;

		$resData = array();
		$row = array();
		$row[]=$total_sale;
		$row[]=$total_sale;
		$row[]=$total_payable;
		$row[]=$total_win;
		$row[]=$total_win;
		$row[]=$bal_agent;
		$row[]=$bal_company;
		$row[]=$status;
		$resData[]=$row;
		$output = array(
			"draw" => null,
			"recordsTotal" => count($resData),
			"recordsFiltered" => count($resData),
			"data" => $resData,
		);
		echo json_encode($output);
	}


}