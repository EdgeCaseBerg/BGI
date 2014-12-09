<?php

class LineItemService {
	private static $instance =NULL;

	private $db;

	public static function instance() {
		if (is_null(self::$instance)) {
			self::$instance = new LineItemService();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->db = Database::instance();
	}

	/* This function is more for data aggregation than anything */
	public function getUserLineItems(User $user) {
		/* Yes we could join and do it all at once etc, but right now 
		 * it's not really that important and I'm not going to bother 
		 * caring that much right now, n+1 here I come. (mmmm prototype)
		 */
		$accounts = $this->db->where(new Account(), 'user_id', $user->id);
		$lineItems = array();
		foreach ($accounts as $account) {
			$lineItems = array_merge($lineItems, $this->getAccountLineItems($account));
		}

		return $lineItems;
	}

	public function getAccountLineItems(Account $account) {
		return $this->db->where(new LineItem(), 'account_id', $account->id);
	}

	public function addLineItem(LineItem $lineItem) {
		//todo: update account balance based on lineitem
		return $this->db->insert($lineItem);
	}

	public function deleteLineItem(LineItem $lineItem) {
		//todo: update account balance based on lineitem
		return $this->db->delete($lineItem);
	}
}
