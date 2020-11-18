<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Content-Type: application/json');

class Terminal_api extends CI_Controller {
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
		$this->load->model('DeleteRequest_model');		
	}

	public function test()
	{
		// echo $curDate = date("Y-m-d H:i:s").'\n';

		// $date = new DateTime();
		// $date->add(new DateInterval('PT10H'));
		// echo $date->format('Y-m-d H:i:s');
		//$this->calcTerminalSummary(4,3,1,80,40,false);
		//$this->isSameBets($bets[0], $bets[1]);
		//$this->hhh($bets[0]);

		echo $this->calc_line(array(3,4,5,6), 10);
	}

	public function reply($result, $message, $data)
	{
		$result = array('result'=>$result, 'message'=>$message, 'data'=>$data);
		echo json_encode($result);
	}

	private function checkLogin()
	{
		$sn = $this->input->post('sn');
		if($sn=="") {
			$this->reply(1002, "sn required", null);
			return null;
		}

		$token = $this->input->post('token');
		if($token=="")
		{
			$this->reply(1002, "token required", null);
			return null;
		}

		$terminal = $this->Terminal_model->getRow(array('terminal_no'=>$sn));
		if($terminal==null) {
			$this->reply(1002, "sn does not exist", null);
			return null;
		}
		if($terminal->status!=1) 
			return $this->reply(1002, "terminal is not allowed", null);

		if($token !=$terminal->token){
			$this->reply(1002, "token mismatch", null);
			return null;
		}
		return $terminal;
	}


	public function ping()
	{
		echo "1";
	}


	private function checkMissedGames($gamelists, $bet)
	{
		$missed = array();		
		if($bet['type']=='Group')
		{
			foreach($bet['gamelist'] as $grp)
			{
				foreach($grp['list'] as $gameNo)
				{
					$bExist = false;
					foreach($gamelists as $game)
					{
						if($game->game_no==$gameNo)
						{
							$bExist = true;
							break;
						}
					}
					if($bExist==false) $missed[]=$gameNo;
				}
			}
		}
		else
		{
			foreach($bet['gamelist'] as $gameNo)
			{
				$bExist = false;
				foreach($gamelists as $game)
				{
					if($game->game_no==$gameNo)
					{
						$bExist = true;
						break;
					}
				}
				if($bExist==false) $missed[]=$gameNo;
			}
		}
		return $missed;
	}

	private function isSameBets($bet0, $bet1)	
	{
		if($bet0['type']!=$bet1['type']) return false;
		if($bet0['option']!=$bet1['option']) return false;		
		if(count($bet0['gamelist']) != count($bet1['gamelist'])) return false;

		$len = count($bet0['gamelist']);
		if($bet0['type']=='Group')
		{
			for($iGrp = 0; $iGrp < $len; $iGrp++)
			{
				$grp0 = $bet0['gamelist'][$iGrp];
				$grp1 = $bet1['gamelist'][$iGrp];

				if($grp0['under'][0] != $grp1['under'][0]) return false;
				if(count($grp0['list']) != count($grp1['list'])) return false;

				$len1 = count($grp0['list']);
				for($iGame=0; $iGame<$len1; $iGame++)
				{
					if($grp0['list'][$iGame] != $grp1['list'][$iGame]) return false;
				}
			}
		}
		else
		{
			for($iGame=0; $iGame<$len; $iGame++)
			{
				if($bet0['gamelist'][$iGame] != $bet1['gamelist'][$iGame]) return false;
			}
		}
		return true;
	}


	private function calc_line($unders, $nGame)
	{
		$line = 0;
		foreach($unders as $under)
		{
			$val = 	$nGame;
			$div = 1;
			for($i=2; $i<=$under; $i++)
			{
				$div *= $i;
				$val *= ($nGame - $i +1);
			}
			$line += ($val/$div);
		}
		return $line;
	}
	
	private function calcLine($bet)
	{
		$line = 1;
		if ($bet['type'] == "Nap/Perm") {
			$line = $this->calc_line($bet['under'], count($bet['gamelist']));
		}
		else if ($bet['type'] == "Group") {
			foreach($bet['gamelist'] as $grp)
			{
				$line *= $this->calc_line($grp['under'], count($grp['list']));
			}
		}
		return $line;
	}


	private function calcTerminalSummary($user_id, $agent_id, $option, $commission, $week_no, $bTerminal) 
	{
		$summaryId ="";
		$userId = 0;
		$terminalId = 0;

		if($bTerminal)
		{
			$summaryId = 't_'.$user_id.'_'.$option.'_'.$week_no;
			$terminalId = $user_id;
		}
		else
		{
			$summaryId = 'u_'.$user_id.'_'.$option.'_'.$week_no;		
			$userId = $user_id;
		}

		$sales = 0;
		$win = 0;
		$bets = null;

		if($bTerminal)
			$bets = $this->Bet_model->getDatas(array('terminal_id'=>$terminalId, 'option_id'=>$option, 'week'=>$week_no, 'status'=>1));
		else
			$bets = $this->Bet_model->getDatas(array('user_id'=>$user_id, 'option_id'=>$option, 'week'=>$week_no, 'status'=>1));

		foreach($bets as $bet)
		{
			$sales += $bet->stake_amount;
			$win += $bet->won_amount;
		}		

		$data = array('summary_id'=>$summaryId, 'terminal_id'=>$terminalId, 'user_id'=>$userId, 'agent_id'=>$agent_id, 
			'option_id'=>$option, 'commission'=>$commission, 'week_no'=>$week_no, 
			'sales'=>$sales, 'win'=>$win, 'payable'=>$sales * $commission/100);

		$row = $this->Summary_model->getRow(array('summary_id'=>$summaryId));

		if($row)
		{
			$this->Summary_model->updateData(array('Id'=>$row->Id),$data);
		}
		else
		{
			$this->Summary_model->insertData($data);
		}

	}

	private function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}	

	public function login() 
	{
		$_POST = json_decode(file_get_contents('php://input'), true);		
		$sn = $this->input->post('sn');
		if($sn=="")return $this->reply(1001, "sn required", null);
		$password = $this->input->post('password');
		if($password=="")return $this->reply(1001, "password required", null);

		$terminal = $this->Terminal_model->getRow(array('terminal_no'=>$sn));
		if($terminal==null) return $this->reply(1002, "sn dose not exist", null);
		if($terminal->status!=1) return $this->reply(1002, "terminal is not allowed", null);
		if($terminal->password!=$password)return $this->reply(1002, "wrong password", null);

		$token = $this->generateRandomString(32);
		$this->Terminal_model->updateData(array('Id'=>$terminal->Id), array('token'=>$token));

		$options = array();
		$datas = $this->TerminalOption_model->getDatas(array('terminal_id'=>$terminal->Id, 'status'=>1));
		foreach($datas as $data) {
			$row = $this->Option_model->getRow(array('Id'=>$data->option_id));
			if($row==null) continue;
			$options[] = $row->name;
		}

		$unders = array();
		if($terminal->unders & 1) $unders[]="U3";
		if($terminal->unders & 2) $unders[]="U4";
		if($terminal->unders & 4) $unders[]="U5";
		if($terminal->unders & 8) $unders[]="U6";

		$curWeekNo = $this->Setting_model->getCurrentWeekNo();
		if($curWeekNo==0) return $this->reply(-1, "no current weekno", null);

		$curWeek = $this->Week_model->getRow(array('week_no'=>$curWeekNo));
		if($curWeek==null) return $this->reply(-1, "week does not exist.", null);

		$games = array();
		$datas = $this->Game_model->getDatas(array('week_no'=>$curWeekNo, 'status'=>1), "game_no");
		foreach($datas as $data) {
			$games[] = $data->game_no;
		}

		$this->reply(1, "success", array(
			'sn'=>$sn,
			'token'=>$token,
			'default_type'=>$terminal->default_type,
			'default_sort'=>$terminal->default_option,
			'default_under'=>$terminal->default_under,
			'possible_sort'=>$options,
			'possible_under'=>$unders,
			'games'=>$games,
			'week'=>$curWeekNo,
			'start_at'=>$curWeek->start_at,
			'close_at'=>$curWeek->close_at,
			'validity'=>$curWeek->validity,
			'void_bet'=>$curWeek->void_bet,
			'credit_limit'=>$terminal->credit_limit
		));
	}

	public function reset() 
	{
		$_POST = json_decode(file_get_contents('php://input'), true);

		$terminal = $this->checkLogin();
		if($terminal==null) return;

		$options = array();
		$datas = $this->TerminalOption_model->getDatas(array('terminal_id'=>$terminal->Id, 'status'=>1));
		foreach($datas as $data) {
			$row = $this->Option_model->getRow(array('Id'=>$data->option_id));
			if($row==null) continue;
			$options[] = $row->name;
		}
	
		$unders = array();
		if($terminal->unders & 1) $unders[]="U3";
		if($terminal->unders & 2) $unders[]="U4";
		if($terminal->unders & 4) $unders[]="U5";
		if($terminal->unders & 8) $unders[]="U6";

		$curWeekNo = $this->Setting_model->getCurrentWeekNo();
		if($curWeekNo==0) return $this->reply(-1, "no current weekno", null);

		$curWeek = $this->Week_model->getRow(array('week_no'=>$curWeekNo));
		if($curWeek==null) return $this->reply(-1, "week does not exist.", null);

		$games = array();
		$datas = $this->Game_model->getDatas(array('week_no'=>$curWeekNo, 'status'=>1), "game_no");
		foreach($datas as $data) {
			$games[] = $data->game_no;
		}

		$this->reply(1, "success", array(
			'sn'=>$terminal->terminal_no,
			'token'=>$terminal->token,
			'default_type'=>$terminal->default_type,
			'default_sort'=>$terminal->default_option,
			'default_under'=>$terminal->default_under,
			'possible_sort'=>$options,
			'possible_under'=>$unders,
			'games'=>$games,
			'week'=>$curWeekNo,
			'start_at'=>$curWeek->start_at,
			'close_at'=>$curWeek->close_at,
			'validity'=>$curWeek->validity,
			'void_bet'=>$curWeek->void_bet,
			'credit_limit'=>$terminal->credit_limit
		));	
	
	}		

	public function make_bet()
	{
		$_POST = json_decode(file_get_contents('php://input'), true);		
		$terminal = $this->checkLogin();
		if($terminal==null) return;

		$reqBets = (object)$this->input->post('bets');		

		//return $this->reply(-1, "text process1", null);		
		$agent = $this->User_model->getRow(array('Id'=>$terminal->agent_id));
		if($agent==null)
			return $this->reply(-1, "no agent", null);

		//check current week
		$curWeekNo = $this->Setting_model->getCurrentWeekNo();
		if($curWeekNo==0)
			return $this->reply(-1, "no current week_no", null);
		$curWeek = $this->Week_model->getRow(array('week_no'=>$curWeekNo));
		if($curWeek==null)
			return $this->reply(-1, "no current week_no", null);

		$curDate = date("Y-m-d H:i:s");
		if ($curWeek->start_at > $curDate || $curWeek->close_at < $curDate) 
			return $this->reply(-1, "coming soon", null);
		//return $this->reply(-1, "text process2", null);


		//generate ticket no		
		srand(time()); 
		$ticketRealNo = rand(1000000,9999999);
		$ticketNo = $terminal->terminal_no.$ticketRealNo;

		//get gamelist
		$gamelists = $this->Game_model->getDatas(array('week_no'=>$curWeekNo, 'status'=>1));

		//get old bets
		$bets = $this->Bet_model->getBets(array('terminal_id'=>$terminal->Id, 'week'=>$curWeekNo));
		//return $this->reply(-1, "text process3", null);

		//check betting
		$resultBets = array();
		
		foreach($reqBets as $bet)
		{
			if(!isset($bet['option'])) $bet['option'] = $terminal->default_option;
			if(!isset($bet['under'])) $bet['under'] = array($terminal->default_under[1]);
			
			//check missed games			
			$missed = $this->checkMissedGames($gamelists, $bet);
			//return $this->reply(-1, "text process33", null);
			if(count($missed) > 0)
			{
				$resultBets[] = array(
					'result'=>1004,
					'message'=>implode(' ', $missed).' :mismatch games',
					'type'=>$bet['type'],
					'option'=>$bet['option'],
					'under'=>$bet['under'],
					'gamelist'=>$bet['gamelist'],
					'stake_amount'=>$bet['stake_amount']	
				);
				continue;	
			}

			//check stake
			if ($bet['stake_amount'] < $terminal->min_stake) {
				$resultBets[] = array(
					'result'=>1004,
					'message'=>'stake amount is less than min_stake',
					'type'=>$bet['type'],
					'option'=>$bet['option'],
					'under'=>$bet['under'],
					'gamelist'=>$bet['gamelist'],
					'stake_amount'=>$bet['stake_amount']	
				);
				continue;	
			}

			if ($bet['stake_amount'] > $terminal->max_stake) {
				$resultBets[] = array(
					'result'=>1004,
					'message'=>'stake amount is greater than max_stake',
					'type'=>$bet['type'],
					'option'=>$bet['option'],
					'under'=>$bet['under'],
					'gamelist'=>$bet['gamelist'],
					'stake_amount'=>$bet['stake_amount']	
				);
				continue;	
			}

			if ($bet['stake_amount'] > $terminal->credit_limit) {
				$resultBets[] = array(
					'result'=>1004,
					'message'=>'credit lack',
					'type'=>$bet['type'],
					'option'=>$bet['option'],
					'under'=>$bet['under'],
					'gamelist'=>$bet['gamelist'],
					'stake_amount'=>$bet['stake_amount']	
				);
				continue;	
			}
			//return $this->reply(-1, "text process4", $bet);
			
			//stake check again include old bets
			$totalStake = 0;
			foreach($bets as $oldBet)
			{	
			    //if($oldBet['status']==2)continue;
				if($this->isSameBets($oldBet,$bet))
					$totalStake += $oldBet['stake_amount'];
			}			
			//return $this->reply(-1, "text process44", $totalStake);
			//return;

			if($totalStake >0 && ($totalStake + $bet['stake_amount']) > $terminal->max_stake)
			{
				$resultBets[] = array(
					'result'=>1004,
					'message'=>'stake amount is greater than max_stake',
					'type'=>$bet['type'],
					'option'=>$bet['option'],
					'under'=>$bet['under'],
					'gamelist'=>$bet['gamelist'],
					'stake_amount'=>$bet['stake_amount']	
				);
				continue;
			}
			//return $this->reply(-1, "text process5", $bet);

			//line calc
			$line = $this->calcLine($bet);
			if($line==0)
			{
				$resultBets[] = array(
					'result'=>1003,
					'message'=>'apl is zero',
					'type'=>$bet['type'],
					'option'=>$bet['option'],
					'under'=>$bet['under'],
					'gamelist'=>$bet['gamelist'],
					'stake_amount'=>$bet['stake_amount']	
				);
				continue;				
			}

			//make new bet
			$newBet = array(
				'bet_id'=>$terminal->Id.rand(100000, 999999),
				'bet_time'=> $curDate,
				'ticket_no'=>$ticketNo,
				'terminal_id'=>$terminal->Id,
				'user_id'=>0,
				'agent_id'=>$terminal->agent_id,
				'stake_amount'=>$bet['stake_amount'],
				'gamelist'=>$bet['gamelist'],
				'week'=>$curWeekNo,
				'under'=>$bet['under'],
				'option'=>$bet['option'],
				'type'=>$bet['type'],
				'apl'=>$bet['stake_amount'] / $line,
			);

			//save new bet
			$option = $this->Option_model->getRow(array('name'=>$bet['option']));
			$this->Bet_model->addNewBet(0, $terminal->Id, $option,  $newBet);

			$commission = 0;
			if($option!=null) $commission = $option->commision;
			$this->calcTerminalSummary($terminal->Id,$terminal->agent_id, $option->Id, $commission, $curWeekNo, true); 
			//return $this->reply(-1, "text process6", $bet);

			//reduce user stake
			$terminal->credit_limit -= $bet['stake_amount'];
			$this->Terminal_model->updateData(array('Id'=>$terminal->Id), array('credit_limit'=>$terminal->credit_limit));


			$bets[]=$newBet;
			$resultBets[] = array(
				'result'=>1,
				'message'=>'success',
				'type'=>$bet['type'],
				'option'=>$bet['option'],
				'under'=>$bet['under'],
				'gamelist'=>$bet['gamelist'],
				'stake_amount'=>$bet['stake_amount'],
				'bet_id'=>$newBet['bet_id'],
				'apl'=>$newBet['apl']	
			);
		}

		$this->Terminal_model->updateData(array('Id'=>$terminal->Id), array('last_ticket_no'=>$ticketNo));
		$this->reply(1, "success", array(
			'ticket_no'=>$ticketRealNo,
			'bet_time'=>$curDate,
			'week'=>$curWeekNo,
			'agent_id'=>$agent->user_id,
			'user_id'=>0,
			'terminal_id'=>$terminal->terminal_no,
			'bets'=>$resultBets
		));
	}	

	public function results() 
	{
		$_POST = json_decode(file_get_contents('php://input'), true);

		$terminal = $this->checkLogin();
		if($terminal==null) return;

		$curWeekNo = $this->input->post('week');
		if($curWeekNo==0 || $curWeekNo=="")
		{
			$curWeekNo = $this->Setting_model->getCurrentWeekNo();
			if($curWeekNo==0) return $this->reply(-1, "no current weekno", null);	
		}

		$games = array();
		$datas = $this->Game_model->getDatas(array('week_no'=>$curWeekNo, 'status'=>1, 'checked'=>1), "game_no");
		foreach($datas as $data) {
			$games[] = $data->game_no;
		}
		$this->reply(1, "success", array('week'=>$curWeekNo, 'drawn'=>$games));
	}
	
	public function reprint() 
	{
		$_POST = json_decode(file_get_contents('php://input'), true);

		$terminal = $this->checkLogin();
		if($terminal==null) return;

		$ticketNo = $this->input->post('ticket_no');
		if($ticketNo=="")
			$ticketNo = $terminal->last_ticket_no;
		else if(strlen($ticketNo) <=7)
		{
			$ticketNo = $terminal->terminal_no.$ticketNo;
		}

		$bets = $this->Bet_model->getBets(array('ticket_no'=>$ticketNo));
		if(count($bets)==0)return $this->reply(1002, "ticket_no dose not exist", null);

		$term = $this->Terminal_model->getRow(array('Id'=>$bets[0]['terminal_id']));
		if($term==null) return $this->reply(1002, "terminal dose not exist", null);

		$agentId="";
		$agent=$this->User_model->getRow(array('Id'=>$term->agent_id));
		if($agent!=null) $agentId = $agent->user_id;

		$this->reply(1, "success", array(
			'ticket_no'=>$bets[0]['ticket_no'],
			'bet_time'=>$bets[0]['bet_time'],
			'week'=>$bets[0]['week'],
			'terminal_id'=>$term->terminal_no,
			'agent_id'=>$agentId,
			'bets'=>$bets
		));
	}
	
	public function win_list() 
	{
		$_POST = json_decode(file_get_contents('php://input'), true);

		$terminal = $this->checkLogin();
		if($terminal==null) return;

		$count_per_page = 20;
		$current_page = $this->input->post('current_page');
		if($current_page <= 0) return $this->reply(1002, 'current_page mismatch', null);
		
		$curWeekNo = $this->input->post('week');
		if($curWeekNo==0 || $curWeekNo =='') $curWeekNo = $this->Setting_model->getCurrentWeekNo();
		if($curWeekNo==0) return $this->reply(-1, 'no current weekno', null);

		$cond = array('terminal_id'=>$terminal->Id, 'win_result'=>'Win', 'week'=>$curWeekNo);
		$ticketNo = $this->input->post('ticket_no');
		if($ticketNo !="") $cond['ticket_no'] = $ticketNo;

		$win_list = array();
		$bets = $this->Bet_model->getDatas($cond, 'ticket_no');
		foreach($bets as $bet)
		{
			if( (!isset($win_list[$bet->ticket_no])))  
				$ticket = array('ticket_no'=>$bet->ticket_no, 'bet_id'=>array(), 'amount'=>array(), 'total_winning'=>0, 'bet_time'=>$bet->bet_time);
			else
				$ticket = $win_list[$bet->ticket_no];

			$ticket['bet_id'][]=$bet->bet_id;
			$ticket['amount'][]=floor($bet->won_amount);
			$ticket['total_winning'] += floor($bet->won_amount);

			$win_list[$bet->ticket_no] = $ticket;
		}

		$lastPage = count($win_list)/$count_per_page;
		if(count($win_list) % $count_per_page) $lastPage++;

		$totalWon = 0;
		$page_list = array();
		if($current_page <= $lastPage)
		{
			$lastNum =  $current_page * $count_per_page;
			if($lastNum > count($win_list)) $lastNum = count($win_list);
			
			$i = 0;
			foreach($win_list as $tickNo=>$data)
			{
				if($i >= (($current_page-1) * $count_per_page) && $i < $lastNum)
				{
					$page_list[] = $data;
					$totalWon += $data['total_winning'];
				}
				$i++;	
			}
		}

		$agentId = "";
		$agent = $this->User_model->getRow(array('Id'=>$terminal->agent_id));
		if($agent!=null) $agentId = $agent->user_id;

		$this->reply(1, "success", array(
			'week'=>$curWeekNo,
			'agent_id'=>$agentId,
			'current_page'=>$current_page,
			'last_page'=>$lastPage,
			'win_list'=>$page_list,
			'total'=>$totalWon
		));
	}	

	public function report() 
	{
		$_POST = json_decode(file_get_contents('php://input'), true);

		$terminal = $this->checkLogin();
		if($terminal==null) return;

		$curWeekNo = $this->input->post('week');
		if($curWeekNo==0 || $curWeekNo=="") $curWeekNo = $this->Setting_model->getCurrentWeekNo();
		if($curWeekNo==0) return $this->reply(-1, 'no current weekno', null);

		$curWeek = $this->Week_model->getRow(array('week_no'=>$curWeekNo));
		if($curWeek==null) return $this->reply(-1, 'week does not exist.', null);

		$summaries = $this->Summary_model->getDatas(array('week_no'=>$curWeekNo, 'terminal_id'=>$terminal->Id));
		$bets = $this->Bet_model->getDatas(array('terminal_id'=>$terminal->Id, 'week'=>$curWeekNo, 'status'=>1));

		$total_sale=0;
		$total_payable=0;
		$total_win= 0;
		$bal_agent="";
		$bal_company="";
		$status= "";
		$odd_summary = array();
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
			$odd_summary[]=array('option'=>$option->name, 
							'count'=>$count, 
							'sale'=>floor($summary->sales), 
							'payable'=>floor($summary->payable),
							'win'=>floor($summary->win)
						);

		}

		if ($total_payable > $total_win) {
			$bal_company = floor($total_payable - $total_win);
			$status = 'green';
		}
		else {
			$bal_agent = floor($total_win - $total_payable);
			$status = 'red';
		}		

		$agentId = "";
		$agent=$this->User_model->getRow(array('Id'=>$terminal->agent_id));
		if($agent!=null) $agentId= $agent->user_id;

		$this->reply(1, 'success', array(
			'week'=> $curWeekNo,
			'agent_id'=> $agentId,
			'terminal_summary'=> array(
				'total_sale'=>floor($total_sale),
				'total_payable'=>floor($total_payable),
				'total_win'=>floor($total_win),
				'bal_agent'=>$bal_agent,
				'bal_company'=>$bal_company,
				'status'=>$status
			),
			'odd_summary'=> $odd_summary,
			'close_at'=> $curWeek->close_at
		));
	}
	
	public function credit_limit() {
		$_POST = json_decode(file_get_contents('php://input'), true);

		$terminal = $this->checkLogin();
		if($terminal==null) return;
		$this->reply(1, 'success', array('credit_limit'=>$terminal->credit_limit));
	}	

	public function logout() {
		$_POST = json_decode(file_get_contents('php://input'), true);

		$terminal = $this->checkLogin();
		if($terminal==null) return;
		$token = $this->generateRandomString(32);
		$this->Terminal_model->updateData(array('Id'=>$terminal->Id), array('token'=>$token));
		$this->reply(1, 'success',null);
	}

	public function void_bet()
	{
		$_POST = json_decode(file_get_contents('php://input'), true);

		$terminal = $this->checkLogin();
		if($terminal==null) return;
		//check user
		$betId = $this->input->post('bet_id');
		
		$bets = $this->Bet_model->getBets(array('bet_id'=>$betId, 'terminal_id'=>$terminal->Id));
		if(count($bets)==0)
			return $this->reply(-1, "bet_id dose not exist", null);

		$bet = $bets[0];

		$week = $this->Week_model->getRow(array('week_no'=>$bet['week']));
		if($week==null)
			return $this->reply(-1, "week dose not exist", null);

		$curDate = new DateTime();
		if($curDate->format('Y-m-d H:i:s') > $week->close_at)
			return $this->reply(1004, "bet does not change in past week", null);

		$curDate->sub(new DateInterval('PT'.$week->void_bet.'H'));
		if($curDate->format('Y-m-d H:i:s') > $bet['bet_time'])
			return $this->reply(1003, "void time passed", null);

		$gamelists = $this->Game_model->getDatas(array('week_no'=>$week->week_no, 'status'=>1));
		if($gamelists==null)
			return $this->reply(-1, "game does not exist", null);

		$missed = $this->checkMissedGames($gamelists, $bet);
		if(count($missed)>0)
			return $this->reply(1003, "void failed", null);

		//save deelte request
		$row = $this->DeleteRequest_model->getRow(array('bet_id'=>$bet['Id']));
		if($row!=null)
			return $this->reply(1003, "already requested", null);

		$this->DeleteRequest_model->insertData(array('bet_id'=>$bet['Id'], 'terminal_id'=>$terminal->Id, 'agent_id'=>$terminal->agent_id));

		//update bet status
		$this->Bet_model->updateData(array('bet_id'=>$betId), array('status'=>2));

		
		$commision = 0 ;
		$opt = 	$this->TerminalOption_model->getRow(array('terminal_id'=>$terminal->Id, 'option_id'=>$bet['option_id']));
		if($opt!=null) $commision = $opt->commision;	

		//return $this->reply(1003, "kkk", null);															

		$this->calcTerminalSummary($terminal->Id, $terminal->agent_id, $bet['option_id'], $commision, $bet['week'], true);
		return $this->reply(1, "success", null);
	}

	public function password_change() 
	{
		$_POST = json_decode(file_get_contents('php://input'), true);

		$terminal = $this->checkLogin();
		if($terminal==null) return;

		$newPasswd = $this->input->post('new_password');
		if($newPasswd=="")
			return $this->reply(1001, 'enter new password', null);	
		$this->Terminal_model->updateData(array('Id'=>$terminal->Id), array('password'=>$newPasswd));
		return $this->reply(1, 'success', null);
	}
	
	public function void_list() 
	{
		$_POST = json_decode(file_get_contents('php://input'), true);

		$terminal = $this->checkLogin();
		if($terminal==null) return;

		$curWeekNo = $this->input->post('week');
		if($curWeekNo==0) $curWeekNo = $this->Setting_model->getCurrentWeekNo();
		if($curWeekNo==0) return $this->reply(-1, 'no current weekno', null);

		$bets = $this->Bet_model->getBets(array('terminal_id'=>$terminal->Id, 'week'=>$curWeekNo, 'status'=>2));

		$agentId = "";
		$agent=$this->User_model->getRow(array('Id'=>$terminal->agent_id));
		if($agent!=null) $agentId= $agent->user_id;

		$results = array();
		foreach($bets as $bet)
		{
			$results[] = array('bet_id'=> $bet['bet_id'], 'stake_amount'=>$bet['stake_amount']);
		}

		$this->reply(1, 'success', array('week'=>$curWeekNo, 'agent_id'=>$agentId, 'void_list'=>$results));
	}

	public function search() 
	{
		$_POST = json_decode(file_get_contents('php://input'), true);
				
		$terminal = $this->checkLogin();
		if($terminal==null) return;
		$curWeekNo = $this->input->post('week');
		if($curWeekNo==0) $curWeekNo = $this->Setting_model->getCurrentWeekNo();
		if($curWeekNo==0) return $this->reply(-1, 'no current weekno', null);

		$searchWord = $this->input->post('searchword');
		if($searchWord=="")return $this->reply(-1, 'no search word', null);

		$isTicket = $this->input->post('is_ticketid');

		$cond = array('terminal_id'=>$terminal->Id, 'week'=>$curWeekNo);
		// if($isTicket==1)$cond['ticket_no'] = $searchWord;
		// else $cond['bet_id'] = $searchWord;

		$restBets = array();
		$bets = $this->Bet_model->getBets($cond);
		for($i = 0; $i<count($bets); $i++)
		{
			if($isTicket==1)
			{
				if(stristr($bets[$i]['ticket_no'], $searchWord)===FALSE) continue;
			}
			else if(stristr($bets[$i]['bet_id'], $searchWord)===FALSE)continue;

// 			if($bets[$i]['win_result']=='')
// 			{
// 				unset($bets[$i]['win_result']);
// 				unset($bets[$i]['won_amount']);
// 			}

			if($bets[$i]['status']==2) 
				$bets[$i]['status'] = 'Void';
			else
			{
				if($bets[$i]['win_result']=='')
					$bets[$i]['status'] = 'Active';
				else
				{
					$bets[$i]['status'] = $bets[$i]['win_result'];
				}
			}
			
			$restBets[] = $bets[$i];
			if(count($restBets) >= 10) break;
		}
		$this->reply(1, 'success', array('week'=>$curWeekNo, 'search_result'=>$restBets));
	}	
	
	public function ticket_list() 
	{
		$_POST = json_decode(file_get_contents('php://input'), true);

		$terminal = $this->checkLogin();
		if($terminal==null) return;
		$curWeekNo = $this->input->post('week');
		if($curWeekNo==0) $curWeekNo = $this->Setting_model->getCurrentWeekNo();
		if($curWeekNo==0) return $this->reply(-1, 'no current weekno', null);

		$bets = $this->Bet_model->getDatas(array('terminal_id'=>$terminal->Id, 'week'=>$curWeekNo), 'bet_time', 'DESC');
		$ticketList = array();
		foreach($bets as $bet)
		{				
			if(substr($bet->ticket_no, 0, strlen($terminal->terminal_no))==$terminal->terminal_no)
				$ticketList[substr($bet->ticket_no, strlen($terminal->terminal_no))] = 1;
			else
				$ticketList[$bet->ticket_no] = 1;
				
			if(count($ticketList)>=7) break;
		}

		$tickets = array();
		foreach($ticketList as $tickno=>$val) $tickets[]=$tickno;
		$this->reply(1, 'success', array('ticket_list'=>$tickets));
	}


	public function bet_counts() 
	{
		$_POST = json_decode(file_get_contents('php://input'), true);
				
		$terminal = $this->checkLogin();
		if($terminal== null) return;
		$curWeekNo = $this->input->post('week');
		if($curWeekNo==0) $curWeekNo = $this->Setting_model->getCurrentWeekNo();
		if($curWeekNo==0) return $this->reply(-1, 'no current weekno', null);

		$bets = $this->Bet_model->getDatas(array('terminal_id'=>$terminal->Id, 'week'=>$curWeekNo, 'status'=>1));
		$this->reply(1, 'success', array('week'=>$curWeekNo, 'bets_counts'=>count($bets)));
	}

	




}