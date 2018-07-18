<?php
class ConexionController{
	static function send( $url, $data = array(), $method = 'POST', $header = array(), $type = 'application/json' ){
		$r = array(
			'success' => false,
			'response' => '{"error":"Ocurrió un error al conectarse a '.$url.'."}',
			'errorMsg' => '',
			'httpCode' => 0,
			'info' => array()
		);

		$ch = curl_init( $url );

		$header[] = 'Content-Type: '.$type;

		if( !empty( $data ) ){
			if($type == 'application/json'){
				$data = json_encode( $data );
			}else if($type == 'application/x-www-form-urlencoded'){
				$data = http_build_query($data);
			}
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
			$header[] = 'Content-Length: '.strlen( $data );
		}
		 
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, $method );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $header );

		try {
			$r['response'] = curl_exec( $ch );
			$r['errorMsg'] = curl_error( $ch );
			$r['info'] = curl_getinfo( $ch );
			$r['httpCode'] = $r['info']['http_code'];
		}catch (Throwable $t){
			$r['errorMsg'] = $t->getMessage();
		}catch (Exception $e){
			$r['errorMsg'] = $e->getMessage();
		}

		if( $r['httpCode'] == 200 ){
			$r['success'] = true;
		}
		$r['response'] = json_decode( $r['response'], true );
		return $r;
	}
}
?>