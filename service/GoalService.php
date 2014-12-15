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

	public function getGoal(Goal $goal) {
		if (is_null($goal->id)) {
			return false;
		}
		return $this->db->get($goal);
	}

	public function getUserGoals(User $user) {
		/* i.o. first */
		$userGoals = $this->db->custom('SELECT * FROM goals WHERE user_id = :user_id ORDER BY goal_type', array(':user_id' => $user->id));
		return $userGoals === false ? array() : $userGoals;
	}

	/* Returns an array indexed by the text: "id:<goal_id>", the text is there to make sure
	 * php doesn't do anything funny with the numeric indexes being copied around
	*/
	public function getUserGoalAccounts(User $user) {
		$goalLinks = $this->db->custom('SELECT * FROM account_goals JOIN goals g ON goal_id = g.id WHERE g.user_id = :user_id ORDER BY goal_id', array(':user_id' => $user->id));
		if ($goalLinks === false) return array();

		$links = array();
		foreach ($goalLinks as $row) {
			$gid = 'id:' . $row->goal_id;
			$aid = $row->account_id;
			if (isset($links[$gid])) $links[$gid][] = $aid;
			else $links[$gid] = array($aid);
		}

		return $links;
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

