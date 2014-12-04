<?php
class AuthenticationService { 
	private static $instance =NULL;

	private $db;

	public static function instance() {
		if (is_null(self::$instance)) {
			self::$instance = new AuthenticationService();
		}
		return self::$instance;
	}

	private function __construct(){
		$this->db = Database::instance();
	}

	public function checkPassword(User $user, $password) {
		return password_verify($password, $user->hash);
	}

	private function rehashIfNeccesary(User $user) {
		global $cryptOptions;
		if (password_needs_rehash($user->hash, PASSWORD_BCRYPT, $cryptOptions)) {
			$user->hash = password_hash($password, PASSWORD_BCRYPT, $cryptOptions);
			if( ! $this->db->update($user) ) {
				logMessage('Could not rehash user\'s password [id:' . $user->id . ']');
				return false;
			}
		}
		return true;
	}

	/* User passes an entity with nickname and ident filled out 
	 * we then check that the ident matches the nickname
	 * the ident is the plain text password, nickname is the username
	 * after calling this function, $user will be replaced with database 
	 * if it was successful
	*/
	public function canLogin(User &$user) {
		$matchingUsers = $this->db->where($user, 'nickname', $user->nickname);
		if ($matchingUsers === false || count($matchingUsers) != 1) {
			return false;
		}

		$storedUser = $matchingUsers[0];

		if (!$this->checkPassword($storedUser, $user->ident)) {
			return false;
		}

		/* Check if the password needs to be rehashed, 
		 * if so do so prior to login, if it fails don't login
		*/
		if (!$this->rehashIfNeccesary($storedUser)) {
			return false;
		}

		$user = $storedUser;
		return true;
	}

	public function login(User $user) {
		if($this->canLogin($user)) {
			$user->last_seen = date('c');
			@$this->db->update($user); //not critical

			session_start();
			$_SESSION['loggedIn'] = true;
			$_SESSION['userId'] = $user->id;
		}
	}

	public function logout(User $user) { 	
		$_SESSION = array();
		if (ini_get("session.use_cookies")) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000,
				$params["path"], $params["domain"],
		    	$params["secure"], $params["httponly"]
		    );
		}
		session_destroy();
	}

}