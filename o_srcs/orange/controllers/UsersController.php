<?php
class UsersController{
	function loginAction(){
		$form = $this->getLoginForm();
		if( $_POST ){
			$form->submit();

			if( $form->isValid() ){
				$fields = $form->getFields();

				$_POST['email'] = $fields['email']->default;
				$_POST['password'] = $fields['password']->default;

				if( SessionController::login() ){
					oRedirect( URL.'questionnaire' );
				}else{
					$form->addNotice( 'User or password is incorrect', 'danger' );
				}
			}
		}

		self::renderLogin( $form );
	}

	/**
	 * Genera el formulario usado en el login
	 */
	public function getLoginForm(){
		$form = new Form( 'login' );
		$form
			->add('email', 'TextType', array(
				'label' => 'Email Address',
				'attrs' => array(
					'placeholder' => 'Enter your Email'
				)
			))
			->add('password', 'PasswordPlainType', array(
				'label' => 'Password',
				'attrs' => array(
					'placeholder' => 'Enter your Password'
				)
			))
			->add('submit', 'SubmitType', array(
				'text' => 'Login'
			));
		return $form;
	}

	static public function makeLogin(){
		$userController = new UsersController();
		self::renderLogin( $userController->getLoginForm() );
	}

	static public function renderLogin( Form $loginForm ){
		$userController = new UsersController();
		self::makeLoginRegister( $loginForm, $userController->getRegisterForm() );
	}

	/**
	 * Comienzan las funciones de registro
	 */
	function registerAction(){
		$form = $this->getRegisterForm();
		if( $_POST ){
			$form->addFilter('post_validate_data', function( Form $form ){
				$fields = $form->getFields();
				$user = new Users();
				$user->email = $fields['email']->default;
				$user = $user->getByEmail();

				$isValid = true;
				if( isset($user[0]) ){
					$isValid = false;
					$fields['email']->addError('The email is already registered');
				}
				return $isValid;
			});

			$form->submit();

			if( $form->isValid() ){
				$fields = $form->getFields();
				$user = new Users();
				$password = new Password();
				$user->email = $fields['email']->default;
				$user->password = $password->make( $fields['password']->default );
				$userId = $user->add();

				if( $userId !== false ){
					$mail = new OrangeEmail;
					$mail->Subject = 'Please, confirm your email';
					$mail->Body = Template::getView( 'orange/emails/confirm_email.html.twig', array( 'token' => TokenController::get( '3', '100 years', $userId ) ) );
					$mail->addAddress( $fields['email']->default );
					$mail->AddEmbeddedImage( Template::$pathTemplate.'/images/email-banner.jpg', 'banner', Template::$urlTemplate.'/images/email-banner.jpg', 'base64', 'image/jpeg' );
					$r = $mail->send();

					if( $r ){
						NoticesController::add( 'account_was_created', 'success' );
						oRedirect( URL.'questionnaire' );
					}else{
						$form->addNotice( 'There was an error to send email to confirm email', 'danger' );
					}
				}else{
					$form->addNotice( 'There was an error to register user', 'danger' );
				}
			}
		}

		self::renderRegister( $form );
	}

	public function getRegisterForm(){
		$form = new Form( 'register' );
		$form
			->addToken()
			->add('email', 'EmailType', array(
				'label' => 'Email Address',
				'attrs' => array(
					'placeholder' => 'Enter your Email'
				)
			))
			->add('password', 'PasswordType', array(
				'label' => 'Password',
				'attrs' => array(
					'placeholder' => 'Enter your Password'
				)
			))
			->add('submit', 'SubmitType', array(
				'text' => 'Sign up'
			));
		return $form;
	}

	static public function makeRegister(){
		$userController = new UsersController();
		self::renderRegister( $userController->getRegisterForm() );
	}

	static public function renderRegister( Form $registerForm ){
		$userController = new UsersController();
		self::makeLoginRegister( $userController->getLoginForm(), $registerForm );
	}

	/**
	 * Envia los 2 formularios de login y registro a una sola vista
	 */
	static public function makeLoginRegister( $loginForm, $registerForm ){
		Template::render('orange/login.html.twig', array( 'login_form' => $loginForm, 'register_form' => $registerForm ));
		exit;
	}

	public function newAccountConfirmAction( $tokenKey = '' ){
		if( TokenController::check( $tokenKey, '3', '100 years' ) ){
			NoticesController::add( 'account_confirmed', 'success' );

			$token = new Token( 3, '100 years' );
			$token->tokenKey = $tokenKey;
			$userId = $token->getUserId();
			$token->delete();

			$user = new Users();
			$user->userId = $userId;
			$user->status = 1;
			$user->updateStatus();
		}else{
			NoticesController::add( 'error_account_confirmed', 'danger' );
		}

		oRedirect( URL.'login' );
	}

	public function logoutAction(){
		SessionController::close();
		oRedirect( URL.'questionnaire' );
	}
}
?>