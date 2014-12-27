<?php

class MetricsService {
	private static $instance =NULL;

	private $db;

	private static function weekRange() {
		$s = strtotime('monday this week');
		$e = strtotime('monday next week');
		if (strtotime('today') < $s) { 
			$s = strtotime('today');
		}
		return array($s,$e);
	}

	public static function instance() {
		if (is_null(self::$instance)) {
			self::$instance = new MetricsService();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->db = Database::instance();
	}

	public function spentThisWeek(User $user) {
		list($s,$e) = self::weekRange();
		return $this->spentBetweenTime($s,$e,$user->id);
	}

	public function spentThisMonth(User $user) { 
		$s = strtotime('midnight of first day of this month');
		$e = strtotime('last day of this month');
		return $this->spentBetweenTime($s);
	}

	private function spentBetweenTime($startTime, $endTime, $user_id) {
		$spent = array(
			'amount' => 0,
			'count' => 0
		);
		$results = $this->db->custom(
			'SELECT SUM(amount) as amount,COUNT(distinct account_id) as numAccounts FROM lineitems '.
			'JOIN accounts a ON a.id = account_id '.
			'WHERE user_id = :user_id AND created_time BETWEEN :start_time AND :end_time',
			array(
				':user_id' => $user_id,
				':start_time' => date('c',$startTime),
				':end_time' => date('c',$endTime)
			)
		);
		if ($results !== false) { //it's only 1 row, but loop anyway
			foreach ($results as $rowObj) {
				$spent['amount'] = $rowObj->amount;
				$spent['count'] = $rowObj->numAccounts;
			}
		}
		return (object) $spent;
	}

	public function amountSpentPerCategoryThisWeek(User $user) {
		list($s,$e) = self::weekRange();
		$results = $this->db->custom(
			'SELECT SUM(amount) as amount, account_id, a.name FROM lineitems '.
			'JOIN accounts a ON a.id = account_id '.
			'WHERE user_id = :user_id AND created_time BETWEEN :start_time AND :end_time ' .
			'GROUP BY account_id',
			array(
				':user_id' => $user->id,
				':start_time' => date('c',$s),
				':end_time' => date('c',$e)
			)
		);
	
		return $results === false ? array() : $results;
	}

	public function goalSpendingForTimePeriod(User $user, $start, $end) {
		$results = $this->db->custom(
			'SELECT ' . 
				'a.name as account_name, ' .
				'g.id as goal_id, ' .
				'g.name as goal_name, ' . 
				'g.amount as goal_amount, ' .
				'g.goal_type as goal_type, ' . 
				'ag.account_id as account_id, ' .
				'SUM(l.amount) as total ' .
			'FROM goals g JOIN account_goals ag ON ag.goal_id = g.id ' . 
			'JOIN lineitems l ON l.account_id = ag.account_id ' . 
			'JOIN accounts a ON a.id = ag.account_id ' .
			'WHERE g.user_id = :user_id AND created_time BETWEEN :start_time AND :end_time ' .
			'GROUP BY g.id, ag.account_id ' . 
			'ORDER BY g.id',
			array(
				':user_id' => $user->id,
				':start_time' => date('c',$start),
				':end_time' => date('c',$end)
			)	
		);
		if ($results === false) {
			return array();
		}
		/* Massage data for each individual goal to create the list of accounts */
		$goalData = array();
		$prev = null;
		$obj = null;
		foreach ($results as $row) {
			if (is_null($prev)) {
				$prev = $row;
				$prev->accounts = array($prev->account_name => $prev->total);
				continue;
			}
			if ($prev->goal_id != $row->goal_id) {
				$goalData[] = $prev; //save it
				$prev = $row;
				$prev->accounts = array($prev->account_name => $prev->total);
				$obj = $prev;
				continue;
			} else {
				if (!array_key_exists($row->account_name, $prev->accounts)) {
					$prev->accounts[$row->account_name] = $row->total;
				}
			}		
		}
		if (!in_array($obj, $goalData)) {
			$goalData[] = $obj;
		}
		foreach ($goalData as $goalInfo) {
			$goalInfo->accountsKeys = array_keys($goalInfo->accounts);
			$goalInfo->accounts = array_values($goalInfo->accounts);
		}
		return $goalData;
	}

	public function goalSpendingForThisWeek(User $user) {
		list($s,$e) = self::weekRange();
		return $this->goalSpendingForTimePeriod($user, $s, $e);
	}

	public function getLastYearOfData(User $user) {
		$s = strtotime('-1 year');
		$e = strtotime('monday next week');
		return $this->goalSpendingForTimePeriod($user, $s, $e);
	}


}
