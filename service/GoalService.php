<?php

class GoalService {
	private static $instance =NULL;
	private $db;

	public static function instance() {
		if (is_null(self::$instance)) {
			self::$instance = new GoalService();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->db = Database::instance();
	}

	public function createWeeklyGoal(Goal $goal) {
		/* Ensure goal fields set correctly */
		$goal->end_time = NULL;
		$goal->start_time = NULL;
		$goal->goal_type = GOAL_TYPE_WEEKLY;

		return $this->db->insert($goal);
	}

	public function createMonthlyGoal(Goal $goal) {
		$goal->end_time = NULL;
		$goal->start_time = NULL;
		$goal->goal_type = GOAL_TYPE_MONTHLY;

		return $this->db->insert($goal);
	}

	public function createTimedGoal(Goal $goal) {
		if (is_null($goal->end_time) || is_null($goal->start_time)) {
			return false;
		}

		$goal->goal_type = GOAL_TYPE_TIMED;
		return $this->db->insert($goal);	
	}

	public function getUserGoals(User $user) {
		/* i.o. first */
		$userGoals = $this->db->custom('SELECT * FROM goals WHERE user_id = :user_id ORDER BY goal_type', array(':user_id' => $user->id));
		return $userGoals === false ? array() : $userGoals;
	}

	public function deleteGoal(Goal $goal) {
		return $this->db->delete($goal);
	}

	/* Need to create link between accounts and goals  */
	public function linkAccountToGoal(Account $account, Goal $goal) {
		$sql = 'INSERT INTO account_goals (account_id, goal_id) VALUES (:aid, :gid)';
		$insertion = $this->db->custom($sql, array(':aid' => $account->id,':gid' => $goal->id));
		return $insertion; //T or F
	}

	public function removeAccountFromGoal(Account $account, Goal $goal) {
		$sql = 'DELETE FROM account_goals WHERE account_id = :aid AND goal_id = :gid';
		$deletion = $this->db->custom($sql, array(':aid' => $account->id, ':gid' => $goal->id));
		return $deletion; //T or F
	}
}

