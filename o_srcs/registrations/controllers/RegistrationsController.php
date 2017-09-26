<?php
class RegistrationsController{
	function indexAction(){
		Template::render( 'registrations/home.html.twig', array( 'form' => $this->registerForm() ) );
	}

	private function registerForm(){
		$form = new Form( 'registration' );
		$form
			//->addToken()
			->add( 'name', 'TextType', array(
				'attrs' => array( 'placeholder' => 'Nombre' )
			))
			->add( 'email' , 'EmailType', array(
				'attrs' => array( 'placeholder' => 'Correo electrónico' )
			))
			->add( 'phone', 'NumberType', array(
				'attrs' => array( 'placeholder' => 'Teléfono' )
			))
			->add( 'comments' , 'TextAreaType', array(
				'attrs' => array( 'placeholder' => 'Mensaje' )
			))
			->add( 'submit', 'SubmitType', array(
				'text' => 'ENVIAR'
			));
		return $form;
	}

	function registerAction(){
		$form = $this->registerForm();

		if( $_POST ){
			$r = array( 'success' => false, 'fields' => array(), 'notice' => '' );

			$form->submit();

			if( $form->isValid() ){
				$fields = $form->getFields();

				$data = array(
					'name' =>  $fields['name']->default,
					'email' =>  $fields['email']->default,
					'phone' =>  $fields['phone']->default,
					'comments' =>  $fields['comments']->default
				);

				$records = new Registrations();
				$r = $records->add( $data );

				$r = array( 'success' => true, 'fields' => '', 'notice' => 'Gracias, su mensaje ha sido enviado.' );
				
				//Envia correo al usuario
				$mail = new OrangeEmail;
				/*/
				$mail->IsSMTP();
				$mail->SMTPAuth = true;
				$mail->Host = ''; // SMTP a utilizar. Por ej. smtp.elserver.com
				$mail->Username = ''; // Correo completo a utilizar
				$mail->Password = ''; // Contraseña
				$mail->Port = 25; // Puerto a utilizar
				/*/
				$mail->Subject = 'Gracias por registrarse en el sitio';
				$mail->Body = Template::getView( 'mailing/new_user.html.twig' );
				$mail->addAddress( $data['email'] );
				$mail->AddEmbeddedImage( ABSPATH.'images/mailing.jpg', 'header', URL.'images/mailing.jpg', 'base64', 'image/jpeg' );
				$mail->send();


				//Envia correo al adminsitrador
				$mail = new OrangeEmail;
				/*/
				$mail->IsSMTP();
				$mail->SMTPAuth = true;
				$mail->Host = ''; // SMTP a utilizar. Por ej. smtp.elserver.com
				$mail->Username = ''; // Correo completo a utilizar
				$mail->Password = ''; // Contraseña
				$mail->Port = 25; // Puerto a utilizar
				/*/
				$mail->Subject = 'Nuevo registro en el sitio';
				$mail->Body = Template::getView( 'mailing/admin_new_user.html.twig', $data );
				$mail->addAddress( ADMIN_MAIL );
				$mail->AddEmbeddedImage( ABSPATH.'images/mailing.jpg', 'header', URL.'images/mailing.jpg', 'base64', 'image/jpeg' );
				$mail->send();
			}else{
				$r[ 'notice' ] = $form->getNotices();
				$r[ 'fields' ] = $form->errorsList();
				
				foreach ($r[ 'notice' ] as $key => $notice) {
					$r[ 'notice' ][ $key ] = $notice['msg'];
				}
			}
			Template::renderJson( $r );
		}

		Template::render( 'registrations/home.html.twig', array( 'form' => $form ) );
	}

	function downloadAction(){
		//
		$realm = "Descargar los usuarios registrados";
		$users = array('sitio' => 'contrasenia');

    	// Here is the FIX
	    if(empty($_SERVER['PHP_AUTH_DIGEST'])){
	        $_SERVER['PHP_AUTH_DIGEST'] = $_SERVER['DEVMD_AUTHORIZATION'];
	    }

    	if (empty($_SERVER['PHP_AUTH_DIGEST']) || !isset( $_SESSION[ SESSION_NAME ][ 'login' ] )) {
			header('HTTP/1.1 401 Unauthorized');
    		header('WWW-Authenticate: Digest realm="'.$realm.
           '",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');

    		$_SESSION[ SESSION_NAME ][ 'login' ] = true;
    		exit;
    	}else if( isset($_SERVER['PHP_AUTH_DIGEST']) ){
			unset( $_SESSION[ SESSION_NAME ][ 'login' ] );

    		$data = $this->http_digest_parse($_SERVER['PHP_AUTH_DIGEST']);

			if( isset( $data['username'] ) && isset( $users[$data['username']] ) ){
				$A1 = md5($data['username'] . ':' . $realm . ':' . $users[$data['username']]);
				$A2 = md5($_SERVER['REQUEST_METHOD'].':'.$data['uri']);
				$valid_response = md5($A1.':'.$data['nonce'].':'.$data['nc'].':'.$data['cnonce'].':'.$data['qop'].':'.$A2);

				if ($data['response'] == $valid_response){
					$this->makeXls();
				}
			}
		}else{
			unset( $_SESSION[ SESSION_NAME ][ 'login' ] );
		}
	}

	protected function http_digest_parse($txt){
		// protect against missing data
		$needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
		$data = array();
		$keys = implode('|', array_keys($needed_parts));

		preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);

		foreach ($matches as $m) {
		    $data[$m[1]] = $m[3] ? $m[3] : $m[4];
		    unset($needed_parts[$m[1]]);
		}

		return $needed_parts ? false : $data;
	}

	protected function makeXls(){
		require ABSPATH.O_LIBRARIES.'PHPExcel/PHPExcel.php';

		$fileName = 'Registros';
		$sheetTitle = 'Usuarios registrados';
		$widthMin = 12;
		$headers = array(
			'#',
			'Nombre',
			'Correo electrónico',
			'Teléfono',
			'Mensaje',
			'Fecha de registro'
		);

		//
		$objPHPExcel = new PHPExcel();
		//$objPHPExcel->createSheet();
		$activeSheet = $objPHPExcel->getActiveSheet();
		$activeSheet->setTitle( $sheetTitle );

		$registrations = new Registrations();
		$registrations = $registrations->get();

		$i = 0;
		foreach ($headers as $header) {
			$activeSheet->setCellValueByColumnAndRow( $i, 1, $header );
			$activeSheet->getColumnDimension( PHPExcel_Cell::stringFromColumnIndex($i) )->setAutoSize(true);
			$i++;
		}

		//Estilos del header
		$i--;
		$columMax = PHPExcel_Cell::stringFromColumnIndex($i);
		$name = 'A1:'.$columMax.'1';

		$activeSheet->getStyle( $name )->applyFromArray(array(
			'font' => array(
				'bold' => true,
				'color' => array('rgb' => 'FFFFFF'),
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'startcolor' => array('rgb' => '5a2f90')
			)
		));
		$activeSheet->freezePane( 'A2' );

		//Registros
		$i = 2;
		$color = true;
		foreach ($registrations as $registration) {
			$activeSheet->setCellValueByColumnAndRow( 0, $i, $registration['registration_id'] );

			unset( $registration['registration_id'] );
			$registerDate = '';
			if( $registration['register_date'] != '' && !is_null($registration['register_date']) ){
				$registerDate = date( 'd/m/Y H:i:s', strtotime( $registration['register_date'] ) );
				unset( $registration['register_date'] );
			}
			$registration['register_date'] = $registerDate;

			$j = 1;
			foreach ($registration as $value) {
				$column  = PHPExcel_Cell::stringFromColumnIndex( $j );
				//$activeSheet->setCellValueByColumnAndRow( $j, $i, utf8_decode($value) );
				$value = trim( $value );
				$value = utf8_encode($value);
				$value = utf8_decode($value);

				$activeSheet->setCellValueExplicit($column.$i, $value, PHPExcel_Cell_DataType::TYPE_STRING);
				$j++;
			}

			//Estilo
			if( $color ){
				$activeSheet->getStyle( 'A'.$i.':'.$columMax.$i )->getFill()
							->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
							->getStartColor()->setRGB('fffccd');
				$color = false;
			}else{
				$color = true;
			}

			//$activeSheet->getCellByColumnAndRow(5, $i)->getHyperlink()->setUrl( $registration['file'] );

			$i++;
		}

		//Estilos de los registros
		$i--;
		$columns = 'A2:'.$columMax.$i;
		$activeSheet->getStyle( $columns )->applyFromArray(array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('rgb' => 'ffffff')
				)
			)
		));
		$activeSheet->getStyle( $columns )->getAlignment()->setWrapText(true); 
		$activeSheet->getStyle( $columns )->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

		/*
		$activeSheet->getStyle( 'F2:'.'F'.$i )->applyFromArray(array(
			'font' => array(
				'underline' => PHPExcel_Style_Font::UNDERLINE_SINGLE,
				'color' => array('rgb' => '0000ff')
			)
		)); 
		*/

		//Filtros
		$activeSheet->setAutoFilter('A1:'.$columMax.$i);

		//Acomoda el ancho de las columnas
		$activeSheet->calculateColumnWidths();
		for( $k=0; $k<=$j; $k++){
			$column  = PHPExcel_Cell::stringFromColumnIndex( $k );
			$column = $activeSheet->getColumnDimension( $column );
			$width = $column->getWidth();

			if( $width > 70 ){
				$width = 70;
			}else if( $width < $widthMin ){
				$width = $widthMin;
			}

			$column->setAutoSize(false);
			$column->setWidth( $width );
		}

		//Nombre final del archivo
		$fileName = $fileName.' '.date('d-m-Y-H-i-s');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		ob_end_clean();

		//
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$fileName.'.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter->save('php://output');
		exit;
	}
}
?>