<?php
	defined('BASEPATH') or exit('No direct script access allowed');

	class Api extends CI_Controller
	{
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
			
			ini_set('memory_limit', '1024M'); // or you could use 1G			
		}

		public function reply($status, $message, $data)
		{
			$result = array('status' => $status, 'message' => $message, 'data' => $data);
			echo json_encode($result);
		}

		public function test()
		{
			$amount = 25000;
			$line = $this->calc_line(array(3), 3);
			echo $line . ', apl=' . $amount / $line;
		}

		private function calc_line($unders, $nGame)
		{
			$line = 0;
			foreach ($unders as $under) {
				$val = 	$nGame;
				$div = 1;
				for ($i = 2; $i <= $under; $i++) {
					$div *= $i;
					$val *= ($nGame - $i + 1);
				}
				$line += ($val / $div);
			}
			return $line;
		}

		private function calcLine($bet)
		{
			$line = 1;
			if ($bet['type'] == "Nap/Perm") {
				$line = $this->calc_line($bet['under'], count($bet['gamelist']));
			} else if ($bet['type'] == "Group") {
				foreach ($bet['gamelist'] as $grp) {
					$line *= $this->calc_line($grp['under'], count($grp['list']));
				}
			}
			return $line;
		}


		private function calcTerminalSummary($user_id, $agent_id, $option, $commission, $week_no, $bTerminal)
		{
			$summaryId = "";
			$userId = 0;
			$terminalId = 0;

			if ($bTerminal) {
				$summaryId = 't_' . $user_id . '_' . $option . '_' . $week_no;
				$terminalId = $user_id;
			} else {
				$summaryId = 'u_' . $user_id . '_' . $option . '_' . $week_no;
				$userId = $user_id;
			}

			$sales = 0;
			$win = 0;
			$bets = null;

			if ($bTerminal)
				$bets = $this->Bet_model->getDatas(array('terminal_id' => $terminalId, 'option_id' => $option, 'week' => $week_no, 'status' => 1));
			else
				$bets = $this->Bet_model->getDatas(array('user_id' => $user_id, 'option_id' => $option, 'week' => $week_no, 'status' => 1));

			if (count($bets) == 0) return;

			foreach ($bets as $bet) {
				$sales += $bet->stake_amount;
				$win += $bet->won_amount;
			}

			$data = array(
				'summary_id' => $summaryId, 'terminal_id' => $terminalId, 'user_id' => $userId, 'agent_id' => $agent_id,
				'option_id' => $option, 'commission' => $commission, 'week_no' => $week_no,
				'sales' => $sales, 'win' => $win, 'payable' => $sales * $commission / 100
			);

			$row = $this->Summary_model->getRow(array('summary_id' => $summaryId));

			if ($row) {
				$this->Summary_model->updateData(array('Id' => $row->Id), $data);
			} else {
				$this->Summary_model->insertData($data);
			}
		}

		private function classify_scores($games, $scores)
		{
			$scored = array();
			$unscored = array();
			foreach ($games as $game) {
				$exist = false;
				foreach ($scores as $score) {
					if ($game == $score) {
						$exist = true;
						break;
					}
				}
				if ($exist) $scored[] = $game;
				else $unscored[] = $game;
			}
			return array('good' => $scored, 'bad' => $unscored);
		}
		private function find_data($list, $id)
		{
			foreach ($list as $data) {
				if ($data->Id == $id) return $data;
			}
			return null;
		}

		private function getPrizeValue($prizes, $underId, $optionId)
		{
			foreach ($prizes as $prize) {
				if ($prize->under != $underId) continue;
				if ($prize->option_id != $optionId) continue;
				return $prize->prize;
			}
			return 0;
		}

		public function apply_game_result()
		{
			$this->logonCheck();

			$options = $this->Option_model->getDatas(null);
			$curWeekNo = $this->Setting_model->getCurrentWeekNo();
			$prizes = $this->Prize_model->getDatas(array('week_no' => $curWeekNo));
			$scores = $this->Game_model->getDatas(array('status' => 1, 'week_no' => $curWeekNo, 'checked' => 1));
			$scorelists = array();
			foreach ($scores as $game) $scorelists[] = $game->game_no;

			// $terminals = $this->Terminal_model->getDatas(array('status' => 1));
			// $players = $this->User_model->getDatas(array('status' => 1, 'type'=>'player'));
			$terminals = $this->Terminal_model->getDatas(null);
			$players = $this->User_model->getDatas(array('type'=>'player'));
			//clear all summary
			$this->Summary_model->deleteRow(array('week_no'=>$curWeekNo));

			foreach($terminals as $terminal)
			{
				$updateBetArray = array();
				$updateSummaryArray = array();	
				$bets = $this->Bet_model->getBets(array('status' => 1, 'week' => $curWeekNo, 'terminal_id'=>$terminal->Id));

				foreach ($bets as $bet) {
					//classify scoredlist
					$scoreList = null;
					$unscoreList = null;
					if ($bet['type'] == "Nap/Perm") {
					    if(count($bet['gamelist']) > 30)
					    {
					        echo $bet['Id'];
					        return;
					    }
						$classifiedData = $this->classify_scores($bet['gamelist'], $scorelists);
						$scoreList = $classifiedData['good'];
						$unscoreList = $classifiedData['bad'];
					} else if ($bet['type'] == "Group") {
						$scoreList = array();
						$unscoreList = array();
						foreach ($bet['gamelist'] as $grp) {
							$classifiedData = $this->classify_scores($grp['list'], $scorelists);
							$scoreList[] = array('under' => $grp['under'], 'list' => $classifiedData['good']);
							$unscoreList[] = array('under' => $grp['under'], 'list' => $classifiedData['bad']);
						}
					}
	
					$lines = $this->calcLine($bet);
					$apl = $bet['stake_amount'] / $lines;
					$won_amount = 0;
					$win_result = "Lost";
					if ($bet['type'] == "Nap/Perm") {
						foreach ($bet['under'] as $under) {
							$winLine = $this->calc_line(array($under), count($scoreList));
							$prizeVal = $this->getPrizeValue($prizes, $under, $bet['option_id']);
							$won_amount += ($apl * $winLine * $prizeVal);
						}
					} else {
						$winLine = 1;
						for ($i = 0; $i < count($bet['gamelist']); $i++) {
							$grp = $bet['gamelist'][$i];
							$nScored = count($scoreList[$i]['list']);
							if ($nScored < $grp['under'][0]) {
								$winLine = 0;
								break;
							}
							$winLine *= $this->calc_line($grp['under'], $nScored);
						}
						$prizeVal = $this->getPrizeValue($prizes, $bet['under'][0], $bet['option_id']);
						$won_amount = ($apl * $winLine * $prizeVal);
					}
	
					if ($won_amount > 0)
						$win_result = "Win";

					$updateBetArray[] = array('Id' => $bet['Id'],
							'score_list' => json_encode($scoreList),
							'unscore_list' => json_encode($unscoreList),
							'apl' => $apl,
							'win_result' => $win_result,
							'won_amount' => $won_amount);

					$option =  $this->find_data($options, $bet['option_id']);
					if($option == null) continue;
	
					//summary making	
					$summaryId = 't_' . $terminal->Id . '_' . $option->Id . '_' . $curWeekNo;
					if(isset($updateSummaryArray[$summaryId]))
						$summary = $updateSummaryArray[$summaryId];
					else 
						$summary = array('summary_id'=>$summaryId, 'terminal_id' => $terminal->Id, 'user_id'=>0, 'agent_id'=>$terminal->agent_id, 
						'option_id'=>$option->Id, 'commission'=>$option->commision, 'week_no'=>$curWeekNo, 
						'sales'=>0, 'win'=>0, 'payable'=>0);
	
					$summary['sales'] += $bet['stake_amount'];
					$summary['win'] += $won_amount;
					$summary['payable'] += $bet['stake_amount'] * $option->commision/100;
					$updateSummaryArray[$summaryId] = $summary;	
					
				}
				//update bets 
				if(count($updateBetArray) >0)
					$this->Bet_model->updateBatch($updateBetArray);

				//insert sumarries
				$summaryArr = array();
				foreach($updateSummaryArray as $summaryId=>$summary)
					$summaryArr[] = $summary;
				if(count($summaryArr) >0)
					$this->Summary_model->insertBatch($summaryArr);					
			}

			foreach($players as $player)
			{
				$updateBetArray = array();
				$updateSummaryArray = array();	
				$bets = $this->Bet_model->getBets(array('status' => 1, 'week' => $curWeekNo, 'user_id'=>$player->Id));

				foreach ($bets as $bet) {
					//classify scoredlist
					$scoreList = null;
					$unscoreList = null;
					if ($bet['type'] == "Nap/Perm") {
						$classifiedData = $this->classify_scores($bet['gamelist'], $scorelists);
						$scoreList = $classifiedData['good'];
						$unscoreList = $classifiedData['bad'];
					} else if ($bet['type'] == "Group") {
						$scoreList = array();
						$unscoreList = array();
						foreach ($bet['gamelist'] as $grp) {
							$classifiedData = $this->classify_scores($grp['list'], $scorelists);
							$scoreList[] = array('under' => $grp['under'], 'list' => $classifiedData['good']);
							$unscoreList[] = array('under' => $grp['under'], 'list' => $classifiedData['bad']);
						}
					}
	
					$lines = $this->calcLine($bet);
					$apl = $bet['stake_amount'] / $lines;
					$won_amount = 0;
					$win_result = "Lost";
					if ($bet['type'] == "Nap/Perm") {
						foreach ($bet['under'] as $under) {
							$winLine = $this->calc_line(array($under), count($scoreList));
							$prizeVal = $this->getPrizeValue($prizes, $under, $bet['option_id']);
							$won_amount += ($apl * $winLine * $prizeVal);
						}
					} else {
						$winLine = 1;
						for ($i = 0; $i < count($bet['gamelist']); $i++) {
							$grp = $bet['gamelist'][$i];
							$nScored = count($scoreList[$i]['list']);
							if ($nScored < $grp['under'][0]) {
								$winLine = 0;
								break;
							}
							$winLine *= $this->calc_line($grp['under'], $nScored);
						}
						$prizeVal = $this->getPrizeValue($prizes, $under, $bet['option_id']);
						$won_amount = ($apl * $winLine * $prizeVal);
					}
	
					if ($won_amount > 0)
						$win_result = "Win";

					$updateBetArray[] = array('Id' => $bet['Id'],
							'score_list' => json_encode($scoreList),
							'unscore_list' => json_encode($unscoreList),
							'apl' => $apl,
							'win_result' => $win_result,
							'won_amount' => $won_amount);

					$option =  $this->find_data($options, $bet['option_id']);
					if($option == null) continue;
	
					//summary making	
					$summaryId = 'u_' . $player->Id . '_' . $option->Id . '_' . $curWeekNo;
					if(isset($updateSummaryArray[$summaryId]))
						$summary = $updateSummaryArray[$summaryId];
					else 
						$summary = array('summary_id'=>$summaryId, 'terminal_id' => 0, 'user_id'=>$player->Id, 'agent_id'=>$player->agent_id, 
						'option_id'=>$option->Id, 'commission'=>$option->commision, 'week_no'=>$curWeekNo, 
						'sales'=>0, 'win'=>0, 'payable'=>0);
	
					$summary['sales'] += $bet['stake_amount'];
					$summary['win'] += $won_amount;
					$summary['payable'] += $bet['stake_amount'] * $option->commision/100;
					$updateSummaryArray[$summaryId] = $summary;	
					
				}
				//update bets 
				if(count($updateBetArray) >0)
					$this->Bet_model->updateBatch($updateBetArray);

				//insert sumarries
				$summaryArr = array();
				foreach($updateSummaryArray as $summaryId=>$summary)
					$summaryArr[] = $summary;
				if(count($summaryArr) >0)
					$this->Summary_model->insertBatch($summaryArr);					
			}

			
			// foreach ($terminals as $term) {
			// 	foreach ($options as $opt) {
			// 		$this->calcTerminalSummary($term->Id, $term->agent_id, $opt->Id, $opt->commision, $curWeekNo, true);
			// 	}
			// }

			
			// foreach ($players as $player) {
			// 	foreach ($options as $opt) {
			// 		$this->calcTerminalSummary($player->Id, $player->agent_id, $opt->Id, $opt->commision, $curWeekNo, false);
			// 	}
			// }

			$this->reply(200, "ok", null);
		}

		private function checkMissedGames($gamelists, $bet)
		{
			$missed = array();
			if ($bet['type'] == 'Group') {
				foreach ($bet['gamelist'] as $grp) {
					foreach ($grp['list'] as $gameNo) {
						$bExist = false;
						foreach ($gamelists as $game) {
							if ($game->game_no == $gameNo) {
								$bExist = true;
								break;
							}
						}
						if ($bExist == false) $missed[] = $gameNo;
					}
				}
			} else {
				foreach ($bet['gamelist'] as $gameNo) {
					$bExist = false;
					foreach ($gamelists as $game) {
						if ($game->game_no == $gameNo) {
							$bExist = true;
							break;
						}
					}
					if ($bExist == false) $missed[] = $gameNo;
				}
			}
			return $missed;
		}
		private function void_bet_for_user($bet, $betId)
		{
			$betId = $bet['Id'];
			$user = $this->User_model->getRow(array('Id' => $bet['user_id']));
			if ($user == null)
				return $this->reply(-1, "user dose not exist", null);

			$week = $this->Week_model->getRow(array('week_no' => $bet['week']));
			if ($week == null)
				return $this->reply(-1, "week dose not exist", null);

			$curDate = new DateTime();
			if ($curDate->format('Y-m-d H:i:s') > $week->close_at)
				return $this->reply(1004, "bet does not change in past week", null);

			$curDate->sub(new DateInterval('PT' . $week->void_bet . 'H'));
			if ($curDate->format('Y-m-d H:i:s') > $bet['bet_time'])
				return $this->reply(1003, "void time passed", null);

			$gamelists = $this->Game_model->getDatas(array('week_no' => $week->week_no, 'status' => 1));
			if ($gamelists == null)
				return $this->reply(-1, "game does not exist", null);

			$missed = $this->checkMissedGames($gamelists, $bet);
			if (count($missed) > 0)
				return $this->reply(1003, "void failed", null);

			//save deelte request
			$row = $this->DeleteRequest_model->getRow(array('bet_id' => $betId));
			if ($row != null)
				return $this->reply(1003, "already requested", null);

			$this->DeleteRequest_model->insertData(array('bet_id' => $betId, 'user_id' => $user->Id, 'agent_id' => $user->agent_id));

			//update bet status
			$this->Bet_model->updateData(array('Id' => $betId), array('status' => 2));

			$commision = 0;
			$opt = 	$this->PlayerOption_model->getRow(array('player_id' => $user->Id, 'option_id' => $bet['option_id']));
			if ($opt != null) $commision = $opt->commision;

			//return $this->reply(1003, "kkk", null);															

			$this->calcTerminalSummary($user->Id, $user->agent_id, $bet['option_id'], $commision, $bet['week'], false);
			return $this->reply(200, "success", null);
		}

		public function void_bet()
		{
			$this->logonCheck();
			$id = $this->input->post('Id');	
			$bets = $this->Bet_model->getBets(array('Id' => $id));

			if (count($bets) == 0)
				return $this->reply(-1, "bet_id dose not exist", null);
			$bet = $bets[0];
			$betId = $bet['Id'];

			if ($bet['terminal_id'] == 0 && $bet['user_id'] > 0) {
				$this->void_bet_for_user($bet, $betId);
				return;
			}

			$terminal = $this->Terminal_model->getRow(array('Id' => $bet['terminal_id']));
			if ($terminal == null)
				return $this->reply(-1, "terminal dose not exist", null);

			$week = $this->Week_model->getRow(array('week_no' => $bet['week']));
			if ($week == null)
				return $this->reply(-1, "week dose not exist", null);

			$curDate = new DateTime();
			if ($curDate->format('Y-m-d H:i:s') > $week->close_at)
				return $this->reply(1004, "bet does not change in past week", null);

			$curDate->sub(new DateInterval('PT' . $week->void_bet . 'H'));
			if ($curDate->format('Y-m-d H:i:s') > $bet['bet_time'])
				return $this->reply(1003, "void time passed", null);

			$gamelists = $this->Game_model->getDatas(array('week_no' => $week->week_no, 'status' => 1));
			if ($gamelists == null)
				return $this->reply(-1, "game does not exist", null);

			$missed = $this->checkMissedGames($gamelists, $bet);
			if (count($missed) > 0)
				return $this->reply(1003, "void failed", null);

			//save deelte request
			$row = $this->DeleteRequest_model->getRow(array('bet_id' => $betId));
			if ($row != null)
				return $this->reply(1003, "already requested", null);

			$this->DeleteRequest_model->insertData(array('bet_id' => $betId, 'terminal_id' => $terminal->Id, 'agent_id' => $terminal->agent_id));

			//update bet status
			$this->Bet_model->updateData(array('Id' => $betId), array('status' => 2));

			$commision = 0;
			$opt = 	$this->TerminalOption_model->getRow(array('terminal_id' => $terminal->Id, 'option_id' => $bet['option_id']));
			if ($opt != null) $commision = $opt->commision;

			//return $this->reply(1003, "kkk", null);

			$this->calcTerminalSummary($terminal->Id, $terminal->agent_id, $bet['option_id'], $commision, $bet['week'], true);
			return $this->reply(200, "success", null);
		}

		private function unVoid_bet_for_user($bet, $betId)
		{
			$betId = $bet['Id'];
			$user = $this->User_model->getRow(array('Id' => $bet['user_id']));
			if ($user == null)
				return $this->reply(-1, "user dose not exist", null);

			$week = $this->Week_model->getRow(array('week_no' => $bet['week']));
			if ($week == null)
				return $this->reply(-1, "week dose not exist", null);

			$curDate = new DateTime();
			if ($curDate->format('Y-m-d H:i:s') > $week->close_at)
				return $this->reply(1004, "bet does not change in past week", null);

			$curDate->sub(new DateInterval('PT' . $week->void_bet . 'H'));
			if ($curDate->format('Y-m-d H:i:s') > $bet['bet_time'])
				return $this->reply(1003, "void time passed", null);

			$gamelists = $this->Game_model->getDatas(array('week_no' => $week->week_no, 'status' => 1));
			if ($gamelists == null)
				return $this->reply(-1, "game does not exist", null);

			$missed = $this->checkMissedGames($gamelists, $bet);
			if (count($missed) > 0)
				return $this->reply(1003, "void failed", null);

			$this->DeleteRequest_model->deleteRow(array('bet_id' => $betId));

			//update bet status
			$this->Bet_model->updateData(array('Id' => $betId), array('status' => 1));

			$commision = 0;
			$opt = 	$this->PlayerOption_model->getRow(array('player_id' => $user->Id, 'option_id' => $bet['option_id']));
			if ($opt != null) $commision = $opt->commision;

			//return $this->reply(1003, "kkk", null);															

			$this->calcTerminalSummary($user->Id, $user->agent_id, $bet['option_id'], $commision, $bet['week'], false);
			return $this->reply(200, "success", null);
		}


		public function unVoid_bet()
		{
			$this->logonCheck();
			$id = $this->input->post('Id');
			$bets = $this->Bet_model->getBets(array('Id' => $id));
			if (count($bets) == 0)
				return $this->reply(-1, "bet_id dose not exist", null);
			$bet = $bets[0];
			$betId = $bet['Id'];

			if ($bet['terminal_id'] == 0 && $bet['user_id'] > 0) {
				$this->unVoid_bet_for_user($bet, $betId);
				return;
			}

			$terminal = $this->Terminal_model->getRow(array('Id' => $bet['terminal_id']));
			if ($terminal == null)
				return $this->reply(-1, "terminal dose not exist", null);

			$week = $this->Week_model->getRow(array('week_no' => $bet['week']));
			if ($week == null)
				return $this->reply(-1, "week dose not exist", null);

			$curDate = new DateTime();
			if ($curDate->format('Y-m-d H:i:s') > $week->close_at)
				return $this->reply(1004, "bet does not change in past week", null);

			$curDate->sub(new DateInterval('PT' . $week->void_bet . 'H'));
			if ($curDate->format('Y-m-d H:i:s') > $bet['bet_time'])
				return $this->reply(1003, "void time passed", null);

			$gamelists = $this->Game_model->getDatas(array('week_no' => $week->week_no, 'status' => 1));
			if ($gamelists == null)
				return $this->reply(-1, "game does not exist", null);

			$missed = $this->checkMissedGames($gamelists, $bet);
			if (count($missed) > 0)
				return $this->reply(1003, "void failed", null);

			$this->DeleteRequest_model->deleteRow(array('bet_id' => $betId));

			//update bet status
			$this->Bet_model->updateData(array('Id' => $betId), array('status' => 1));

			$commision = 0;
			$opt = 	$this->TerminalOption_model->getRow(array('terminal_id' => $terminal->Id, 'option_id' => $bet['option_id']));
			if ($opt != null) $commision = $opt->commision;

			//return $this->reply(1003, "kkk", null);

			$this->calcTerminalSummary($terminal->Id, $terminal->agent_id, $bet['option_id'], $commision, $bet['week'], true);
			return $this->reply(200, "success", null);
		}
	}
