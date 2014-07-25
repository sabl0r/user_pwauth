<?php
/*
            DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
                    Version 2, December 2004

 Copyright (C) 2004 Sam Hocevar <sam@hocevar.net>

 Everyone is permitted to copy and distribute verbatim or modified
 copies of this license document, and changing it is allowed as long
 as the name is changed.

            DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
   TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION

  0. You just DO WHAT THE FUCK YOU WANT TO.

*/

/**
 * User authentication via pwauth
 *
 * @author Clément Véret
 * @author Philip Taffner
 */
namespace OCA\user_pwauth;

class USER_PWAUTH extends \OC_User_Backend implements \OCP\UserInterface {
	protected $pwauth_bin_path;
	protected $pwauth_uid_list;

	public function __construct() {
		$this->pwauth_bin_path = \OC_Appconfig::getValue('user_pwauth', 'pwauth_path', OC_USER_BACKEND_PWAUTH_PATH);
		$list = explode(';', \OC_Appconfig::getValue('user_pwauth', 'uid_list', OC_USER_BACKEND_PWAUTH_UID_LIST));

		$r = array();
		foreach($list as $entry) {
			if(strpos($entry, '-') === FALSE) {
				$r[] = $entry;
			} else {
				$range = explode('-', $entry); 
				if($range[0] < 0) {
					$range[0] = 0;
				}

				if($range[1] < $range[0]) {
					$range[1] = $range[0];
				}

				for($i = $range[0]; $i <= $range[1]; $i++) {
					$r[] = $i;
				}
			}
		}

		$this->pwauth_uid_list = $r;
	}
	
	/**
	 * counts the users
	 *
	 * @return int | bool
	 */
	public function countUsers() {
		return count($this->getUsers());
	}

	/**
	 * @brief Check if a user list is available or not
	 * @return boolean if users can be listed or not
	 */
	public function hasUserListings() {
		return true;
	}

	/**
	* @brief delete a user
	* @param $uid The username of the user to delete
	* @returns true/false
	*
	* Deletes a user
	*/
	public function deleteUser($_uid) {
		// Can't delete user
		OC_Log::write('OC_USER_PWAUTH', 'Not possible to delete local users from web frontend using unix user backend', 3);
		return false;
	}

	/**
	 * @brief Check if the password is correct
	 * @param $uid The username
	 * @param $password The password
	 * @returns true/false
	 *
	 * Check if the password is correct without logging in the user
	 */
	public function checkPassword( $uid, $password ) {
		$uid = strtolower($uid);
		$unix_user = posix_getpwnam($uid);
		
		// Checks if the Unix UID number is allowed to connect
		if(empty($unix_user)){
			return false; //user does not exist
		}

		if(!in_array($unix_user['uid'], $this->pwauth_uid_list)){
			return false;
		}

		$handle = popen($this->pwauth_bin_path, 'w');
		if ($handle === false) {
			// Can't open pwauth executable
			OC_Log::write('OC_USER_PWAUTH', 'Cannot open pwauth executable, check that it is installed on server.',3);
			return false;
		}

		if (fwrite($handle, "$uid\n$password\n") === false) {
			// Can't pipe uid and password
			return false;
		}

		// Is the password valid?
		if (pclose($handle) === 0){
			return $uid;
		}

		return false;
	}
	
	/**
	* @brief check if a user exists
	* @param string $uid the username
	* @return boolean
	*/
	public function userExists($uid){
		return is_array(posix_getpwnam(strtolower($uid)));
	}
	
	/**
	* @brief Get a list of all users
	* @returns array with all uids
	*
	* Get a list of all users.
	*
	* This is a tricky one: There is no way to list all users which UID > 1000 directly in PHP so we just scan all UIDs from $pwauth_min_uid to $pwauth_max_uid
	*/
	public function getUsers($search = '', $limit = 10, $offset = 10){
		$returnArray = array();

		foreach($this->pwauth_uid_list as $f) {
			if(is_array($array = posix_getpwuid($f))) {
				$returnArray[] = $array['name'];
			}
		}

		if(!empty($search)) {
			$returnArray = array_filter($returnArray, function($user) use ($search){
				return strripos($user, $search) !== false || strripos($this->getDisplayName($user), $search) !== false;
			});
		} 

		if($limit = -1) {
			$limit = null;
		}

		return array_slice($returnArray, $offset, $limit);
	}

	/**
	 * @brief Get a list of all display names
	 * @returns array with  all displayNames (value) and the corresponding uids (key)
	 *
	 * Get a list of all display names and user ids.
	 */
	public function getDisplayNames($search = '', $limit = null, $offset = null) {
		$displayNames = array();
		$users = $this->getUsers($search, $limit, $offset);

		foreach ($users as $user) {
			$displayNames[$user] = $this->getDisplayName($user);
		}

		return $displayNames;
	}

	/**
	 * @brief get display name of the user
	 * @param $uid user ID of the user
	 * @return display name
	 */
	public function getDisplayName($uid) {
		$user = posix_getpwnam($uid);
		return trim($user['gecos']) != '' ? $user['gecos'] : $user['name'];
	}

}

