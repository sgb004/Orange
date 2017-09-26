<?php
require_once ABSPATH.O_LIBRARIES.'PHPMailer-master/PHPMailerAutoload.php';

class OrangeEmail extends PHPMailer{
	protected $addressList = array();
	protected $ccList = array();
	protected $bccList = array();
	protected $sendSeparately = false;

    public function __construct($exceptions = false){
        $this->exceptions = (boolean)$exceptions;
        $this->From = '';
		$this->FromName = '';
    }

	public function addAddress( $addresses, $names = '' ){
		$addresses = explode(',', $addresses);
		$names = explode(',', $names);
		foreach ($addresses as $i => $address) {
			$address = trim( $address );
			$name = isset( $names[$i] ) ? trim( $names[$i] ) : '';
			if( $this->sendSeparately ){
				$this->addressList[ $address ] = $name;
			}else{
				parent::addAddress( $address, $name );
			}
		}
	}

	public function addCC( $addresses, $names = '' ){
		$addresses = explode(',', $addresses);
		$names = explode(',', $names);
		foreach ($addresses as $i => $address) {
			$address = trim( $address );
			$name = isset( $names[$i] ) ? trim( $names[$i] ) : '';
			if( $this->sendSeparately ){
				$this->ccList[ $address ] = $name;
			}else{
				parent::addCC( $address, $name );
			}
		}
	}

	public function addBCC( $addresses, $names = '' ){
		$addresses = explode(',', $addresses);
		$names = explode(',', $names);
		foreach ($addresses as $i => $address) {
			$address = trim( $address );
			$name = isset( $names[$i] ) ? trim( $names[$i] ) : '';
			if( $this->sendSeparately ){
				$this->bccList[ $address ] = $name;
			}else{
				parent::addBCC( $address, $name );
			}
		}
	}

	public function send(){
		$result = true;

		if( $this->From == '' ){ $this->From = NO_REPLY_MAIL; }
		if( $this->FromName == '' ){ $this->FromName = NO_REPLY_MAIL_NAME; }
		$this->From = NO_REPLY_MAIL;
		$this->FromName = NO_REPLY_MAIL_NAME;
		$this->FromName = utf8_decode( $this->FromName );
		$this->isHTML(true);
		$this->CharSet = 'UTF-8';

		if( $this->sendSeparately ){
			foreach ($this->addressList as $address => $name) {
				$this->ClearAllRecipients();
				$this->addAddress($address, $name);
				if( $result ){
					$result = parent::send();
				}
			}
			foreach ($this->ccList as $address => $name) {
				$this->ClearAllRecipients();
				$this->addCC($address, $name);
				if( $result ){
					$result = parent::send();
				}
			}
			foreach ($this->bccList as $address => $name) {
				$this->ClearAllRecipients();
				$this->addBCC($address, $name);
				if( $result ){
					$result = parent::send();
				}
			}
		}else{
			$result = parent::send();
		}
		return $result;
	}

}
?>