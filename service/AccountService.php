<?php

class AccountService {
	private static $instance =NULL;

	private $db;

	public static function instance() {
		if (is_null(self::$instance)) {
			self::$instance = new AccountService();
		}
		return self::$instance;
	}

	private function __construct(){
		$this->db = Database::instance();
	}

	public function createAccount(Account $account) {
		return $this->db->insert($account);
	}

	public function deleteAccount(Account $account) { 
		return $this->db->delete($account);
	}

	public function getUserAccounts(User $user) {
		return $this->db->where(new Account(), 'user_id', $user->id);
	}

	public function setUpdatedTime(Account $account) {
		$account->last_updated = date('c');
		return $this->db->update($account);	
	}

}