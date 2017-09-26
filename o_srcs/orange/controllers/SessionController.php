<?php
class SessionController{
	public static $userId = 0;
	public static $email = '';
	public static $username = '';
	public static $name = '';
	public static $lastname = '';
	public static $fullname = '';

	static function check(){
		$login = isset( $_SESSION[SESSION_NAME.'_user_token'] );

		if( $login ){
			$token = new Token( 1, '-1 day' );
			$token->tokenKey = $_SESSION[SESSION_NAME.'_user_token'];
			$token->ip = $_SERVER['REMOTE_ADDR'];
			$userId = $token->getByIp();
			if( $userId === 0 ){
				//$login = self::login();
				UsersController::makeLogin();				
			}else{
				$user = json_decode( base64_decode( $_SESSION[SESSION_NAME.'_user_data'] ) );
				$user->{'user_id'} = $userId;
				self::setUserData( $user );
				Template::$twig->addGlobal( 'session', true );
			}
		}else{
			//$login = self::login();
			UsersController::makeLogin();
		}
		return $login;
	}

	static function login(){
		$login = false;
		$login = isset($_POST['email']);
		if ( $login ) {
			$users = new Users();
			$users->email = $_POST['email'];
			$user = $users->getByEmail();

			$login = isset( $user[0] );

			if( $login ){
				$user = $user[0];
				$password = new Password();
				$login = $password->check( $_POST['password'], $user->password );
				if( $login ){
					$user->{'username'} = $user->email;
					self::setUserData( $user );

					$_SESSION[SESSION_NAME.'_user_data'] = base64_encode(json_encode( array(
						'email' => $user->email,
						'username' => $user->email
					) ) );

					$token = new Token( 1, '-1 day' );
					$token->userId = $user->user_id;
					$token->ip = $_SERVER['REMOTE_ADDR'];

					do{
						$token->tokenKey = $password->make( date('Y-m-d H:i:s').' '.uniqid() );
						$token->tokenKey = md5( $token->tokenKey );
					} while( $token->check() );
					$token->add();

					$_SESSION[SESSION_NAME.'_user_token'] = $token->tokenKey;

					$users->userId = $user->user_id;
					$users->updateLastLoginDate();
				}
			}
		}

		return $login;
	}

	static function setUserData( $user ){
		self::$userId = $user->user_id;
		self::$username = $user->username;
		self::$email = $user->email;
		//self::$name = $user->name;
		//self::$lastname = $user->lastname;
		//self::$fullname = $user->name.' '.$user->lastname;

		Template::$twig->addGlobal( 'user_id', $user->user_id );
		Template::$twig->addGlobal( 'email', $user->email );
		Template::$twig->addGlobal( 'username', $user->username );
		//Template::$twig->addGlobal( 'name', $user->name );
		//Template::$twig->addGlobal( 'lastname', $user->lastname );
		//Template::$twig->addGlobal( 'fullname', self::$fullname );
	}

	static function close(){
		if( isset($_SESSION[SESSION_NAME.'_user_token']) ){
			TokenController::delete( $_SESSION[SESSION_NAME.'_user_token'] );
			unset( $_SESSION[SESSION_NAME.'_user_token'] );
			unset( $_SESSION[SESSION_NAME.'_user_data'] );
			unset( $_SESSION );
			session_destroy();
		}
	}
}
?>