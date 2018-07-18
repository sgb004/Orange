<?php
class LeadsController{
	public function indexAction(){
		Template::render( 'leads/home.html.twig', array( 'form' => $this->getForm() ) );
	}

	protected function getModel(){
		return new Leads();
	}

	protected function getForm(){
		$form = new Form( 'lead_form_add' );
		$form
			//->addToken()
			->add( 'name', 'TextType', array(
				'attrs' => array( 'placeholder' => 'Nombre:' )
			))
			->add( 'email' , 'EmailType', array(
				'attrs' => array( 'placeholder' => 'Correo electrónico:' )
			))
			->add( 'phone', 'NumberType', array(
				'attrs' => array( 'placeholder' => 'Teléfono:' )
			))
			->add( 'message', 'TextAreaType', array(
				'attrs' => array( 'placeholder' => 'Mensaje:', 'rows' => 5 )
			))
			->add( 'terms', 'MultiChoiceType', array(
				'choices' => array( 1 => 'He leído y aceptado el <a href="" target="_blank">Aviso de Privacidad</a>' )
			))
			->add( 'submit', 'SubmitType', array(
				'text' => 'ENVIAR'
			));
	
		return $form;
	}

	public function addLead( $campaign = '' ){
		$form = $this->getForm();

		if( $_POST ){
			$r = array( 'success' => false, 'fields' => array(), 'notice' => '' );

			$form->submit();

			if( $form->isValid() ){
				$fields = $form->getFields();

				$data = array();
				$leads = $this->getModel();

				$leads->name = $data['name'] = $fields['name']->default;
				$leads->email = $data['email'] = $fields['email']->default;
				$leads->phone = $data['phone'] = $fields['phone']->default;
				$leads->solution = $data['solution'] = $fields['solution']->default;
				$leads->message = $data['message'] = $fields['message']->default;
				$leads->campaign = $data['campaign'] = $campaign;

				$r = $leads->add();

				$r = array( 'success' => true, 'fields' => '', 'notice' => '' );
				
				//Envia correo al usuario
				$mail = new OrangeEmail;

				$mail->IsSMTP();
				$mail->SMTPAuth = true;
				$mail->Host = ''; // SMTP a utilizar. Por ej. smtp.elserver.com
				$mail->Username = ''; // Correo completo a utilizar
				$mail->Password = ''; // Contraseña
				$mail->Port = 25; // Puerto a utilizar

				$mail->Subject = 'Gracias por contactarnos - ';
				$mail->Body = Template::getView( 'mailing/new_user.html.twig' );
				$mail->addAddress( $data['email'] );
				$mail->AddEmbeddedImage( ABSPATH.'images/header-mailing.jpg', 'header', URL.'images/header-mailing.jpg', 'base64', 'image/jpeg' );
				$mail->send();

				//Envia correo al adminsitrador
				$mail = new OrangeEmail;

				$mail->IsSMTP();
				$mail->SMTPAuth = true;
				$mail->Host = ''; // SMTP a utilizar. Por ej. smtp.elserver.com
				$mail->Username = ''; // Correo completo a utilizar
				$mail->Password = ''; // Contraseña
				$mail->Port = 25; // Puerto a utilizar

				$mail->Subject = ' | Nuevo registro desde el landing '.$campaign;
				$mail->Body = Template::getView( 'mailing/admin_new_user.html.twig', $data );
				$mail->addAddress( ADMIN_MAIL );
				$mail->AddEmbeddedImage( ABSPATH.'images/header-mailing.jpg', 'header', URL.'images/header-mailing.jpg', 'base64', 'image/jpeg' );
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

		$this->indexAction();
	}

	public function thanksAction(){
		Template::render( 'leads/thanks.html.twig' );
	}

	protected function downloadConfig(){
		return array(
			'login' => array(
				'title' => 'Descargar los usuarios registrados',
				'users' => array(),
			),
			'file' => array(
				'name' => 'Reporte',
				'sheetTitle' => 'Usuarios registrados',
				'headers' => array(
					'lead_id' => '#',
					'name' => 'Nombre',
					'email' => 'Correo electrónico',
					'phone' => 'Teléfono',
					'register_date' => 'Fecha de registro'
				),
				'headerColor' => '01a4ff',
				'rowZebraColor' => 'c7e494'
			)
		);
	}

	function download(){
		$c = $this->downloadConfig();
		//
		$realm = $c['login']['title'];
		$users = $c['login']['users'];

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
					$this->makeXls( $c['file'] );
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

	protected function makeXls( $c ){
		require ABSPATH.O_LIBRARIES.'PHPExcel/PHPExcel.php';

		$fileName = $c['name'];
		$sheetTitle = $c['sheetTitle'];
		$widthMin = 12;
		$headers = $c['headers'];

		//
		$objPHPExcel = new PHPExcel();
		//$objPHPExcel->createSheet();
		$activeSheet = $objPHPExcel->getActiveSheet();
		$activeSheet->setTitle( $sheetTitle );

		$leads = $this->getModel();
		$leads = $leads->get();

		$i = 0;
		$model = array();
		foreach ($headers as $key => $header) {
			$model[$key] = '';
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
				'startcolor' => array('rgb' => $c['headerColor'])
			)
		));
		$activeSheet->freezePane( 'A2' );

		//Registros
		$i = 2;
		$color = true;
		foreach ($leads as $lead) {
			if( $lead['register_date'] != '' && !is_null($lead['register_date']) ){
				$lead['register_date'] = date( 'd/m/Y H:i:s', strtotime( $lead['register_date'] ) );
			}

			$m = array();
			foreach ($model as $key => $value) {
				$m[ $key ] = $lead[$key];
			}
			/*/
			$activeSheet->setCellValueByColumnAndRow( 0, $i, $lead['registration_id'] );

			unset( $lead['registration_id'] );
			$registerDate = '';
			if( $lead['register_date'] != '' && !is_null($lead['register_date']) ){
				$registerDate = date( 'd/m/Y H:i:s', strtotime( $lead['register_date'] ) );
				unset( $lead['register_date'] );
			}
			$lead['register_date'] = $registerDate;
			/*/
			$j = 0;
			foreach ($m as $value) {
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
							->getStartColor()->setRGB($c['rowZebraColor']);
				$color = false;
			}else{
				$color = true;
			}

			//$activeSheet->getCellByColumnAndRow(5, $i)->getHyperlink()->setUrl( $lead['file'] );

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

	function testMailingAction(){
		$r = false;

		$mail = new OrangeEmail;

		$mail->IsSMTP();
		$mail->SMTPAuth = true;
		$mail->Host = ''; // SMTP a utilizar. Por ej. smtp.elserver.com
		$mail->Username = ''; // Correo completo a utilizar
		$mail->Password = ''; // Contraseña
		$mail->Port = 25; // Puerto a utilizar

		$mail->Subject = 'Prueba';
		//$mail->Body = Template::getView( 'mailing/admin_new_user.html.twig' );
		$mail->Body = Template::getView( 'mailing/new_user.html.twig' );
		$mail->addAddress( '' );
		$mail->AddEmbeddedImage( ABSPATH.'images/header-mailing.jpg', 'header', URL.'images/header-mailing.jpg', 'base64', 'image/jpeg' );
		$r = $mail->send();

		echo '<pre>';
		print_r( ABSPATH.'images/header-mailing.jpg' );
		echo '</pre>';

		echo '<pre>';
		print_r( URL.'images/header-mailing.jpg' );
		echo '</pre>';

		echo '<pre>';
		print_r( $mail->From );
		echo '</pre>';

		echo '<pre>';
		print_r( $mail->FromName );
		echo '</pre>';

		echo '<pre>';
		print_r( 'ENVIADO '.$r );
		echo '</pre>';

		echo $mail->Body;
		exit;
	}
}
?>