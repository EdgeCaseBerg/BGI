<?php

class MetricsService {
	private static $instance =NULL;

	private $db;

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
		$s = strtotime('this monday');
		$e = strtotime('next monday');
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


}
