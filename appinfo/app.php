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
 * ownCloud - user_pwauth
 *
 * @author Clément Véret
 * @author Philip Taffner
 */

require_once('apps/user_pwauth/user_pwauth.php');

OCP\App::registerAdmin('user_pwauth','settings');

// define UID_LIST (first - last user;user;user)
define('OC_USER_BACKEND_PWAUTH_UID_LIST', '1000-1010');
define('OC_USER_BACKEND_PWAUTH_PATH', '/usr/sbin/pwauth');

//OC_User::registerBackend('PWAUTH');
OC_User::useBackend(new \OCA\user_pwauth\USER_PWAUTH());

// add settings page to navigation
$entry = array(
	'id' => 'user_pwauth_settings',
	'order'=> 1,
	'href' => OC_Helper::linkTo('user_pwauth', 'settings.php'),
	'name' => 'PWAUTH'
);
