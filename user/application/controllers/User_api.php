<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_api extends CI_Controller {
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
		echo $this->calc_line(array(3), 15);

		//echo $this->calc_line(array(2), 4) * $this->calc_line(array(3), 5);
	}

	public function reply($result, $message, $data)
	{
		$result = array('result'=>$result, 'message'=>$message, 'data'=>$data);
		echo json_encode($result);
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

	public function make_bet()
	{
		$this->logonCheck();
		$reqBets = (object)$this->input->post('bets');

		//check user , agent
		$playerId = $this->session->userdata('player_id');
		$user = $this->User_model->getRow(array('Id'=>$playerId));
		if($user==null)
		{
			redirect('Cms/login', 'refresh');
			return;
		}

		//return $this->reply(-1, "text process1", null);		
		$agent = $this->User_model->getRow(array('Id'=>$user->agent_id));
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
		$ticketNo = $user->user_id.sprintf('%04d', $user->bet_count+1).rand(1000000,9999999);
		$this->User_model->updateData(array('Id'=>$user->Id), array('bet_count'=>$user->bet_count+1));

		//get gamelist
		$gamelists = $this->Game_model->getDatas(array('week_no'=>$curWeekNo, 'status'=>1));

		//get old bets
		$bets = $this->Bet_model->getBets(array('user_id'=>$user->Id, 'week'=>$curWeekNo));
		//return $this->reply(-1, "text process3", null);

		//check betting
		$resultBets = array();
		
		foreach($reqBets as $bet)
		{
			if(!isset($bet['option'])) $bet['option'] = $user->default_option;
			if(!isset($bet['under'])) $bet['under'] = array($user->default_under[1]);			
			
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
			if ($bet['stake_amount'] < $user->min_stake) {
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

			if ($bet['stake_amount'] > $user->max_stake) {
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

			if ($bet['stake_amount'] > $user->credit_limit) {
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
				if($this->isSameBets($oldBet,$bet))
					$totalStake += $oldBet['stake_amount'];
			}			
			//return $this->reply(-1, "text process44", $totalStake);
			//return;

			if($totalStake >0 && ($totalStake + $bet['stake_amount']) > $user->max_stake)
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
				'bet_id'=>$user->Id.rand(100000, 999999),
				'bet_time'=> $curDate,
				'ticket_no'=>$ticketNo,
				'terminal_id'=>0,
				'user_id'=>$user->Id,
				'agent_id'=>$user->agent_id,
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
			$this->Bet_model->addNewBet($user->Id, 0, $option,  $newBet);

			$commission = 0;
			if($option!=null) $commission = $option->commision;
			$this->calcTerminalSummary($user->Id,$user->agent_id, $option->Id, $commission, $curWeekNo, false/*for user*/); 
			//return $this->reply(-1, "text process6", $bet);

			//reduce user stake
			$user->credit_limit -= $bet['stake_amount'];
			$this->User_model->updateData(array('Id'=>$user->Id), array('credit_limit'=>$user->credit_limit));


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

		$this->reply(1, "success", array(
			'ticket_no'=>$ticketNo,
			'bet_time'=>$curDate,
			'week'=>$curWeekNo,
			'agent_id'=>$agent->user_id,
			'user_id'=>$user->user_id,
			'terminal_id'=>'',
			'bets'=>$resultBets
		));
	}	

	public function void_bet()
	{
		$this->logonCheck();
		//check user
		$playerId = $this->session->userdata('player_id');		
		$user = $this->User_model->getRow(array('Id'=>$playerId));
		if($user==null)
			return $this->reply(-1, "player dose not exist", null);

		$betId = $this->input->post('betId');
		
		$bets = $this->Bet_model->getBets(array('Id'=>$betId, 'user_id'=>$user->Id));
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
		$row = $this->DeleteRequest_model->getRow(array('bet_id'=>$betId));
		if($row!=null)
			return $this->reply(1003, "already requested", null);

		$this->DeleteRequest_model->insertData(array('bet_id'=>$betId, 'user_id'=>$user->Id, 'agent_id'=>$user->agent_id));

		//update bet status
		$this->Bet_model->updateData(array('bet_id'=>$betId), array('status'=>2));

		
		$commision = 0 ;
		$opt = 	$this->PlayerOption_model->getRow(array('player_id'=>$user->Id, 'option_id'=>$bet['option_id']));
		if($opt!=null) $commision = $opt->commision;	

		//return $this->reply(1003, "kkk", null);															

		$this->calcTerminalSummary($user->Id, $user->agent_id, $bet['option_id'], $commision, $bet['week'], false);

		return $this->reply(1, "success", null);
	}

}