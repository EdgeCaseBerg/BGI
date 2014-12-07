<?php 

class UserService {
	private static $instance =NULL;
	private $db;

	public static function instance() {
		if (is_null(self::$instance)) {
			self::$instance = new UserService();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->db = Database::instance();
	}

	public function createUser(User $user) {
		/* Does the user already exist? */
		$matchedUsers = $this->db->where($user, 'nickname', $user->nickname);
		if ($matchedUsers !== false && count($matchedUsers) == 0) {
			global $cryptOptions;

			$user->hash = password_hash($user->ident, PASSWORD_BCRYPT, $cryptOptions);
			$user->last_seen = date('c');
			$user->ident = '';

			if ( $this->db->insert($user) !== false) {
				return true;
			}
		}
		return false;
	}

	public function deleteUser(User $user) {
		if (!is_null($user->id)) {
			return $this->db->delete($user);
		}
		return false;
	}
}