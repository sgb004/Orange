<?php
class InstallController{
	public function installAction(){
		$timezoneList = array( '' => 'Seleccionar' );

		foreach(timezone_abbreviations_list() as $abbr => $timezone){
			foreach($timezone as $val){
				if( isset($val['timezone_id']) ){
					$timezoneList[ $val['timezone_id'] ] = $val['timezone_id'];
				}
			}
		}

		ksort($timezoneList);

		$form = new Form('install');
		$form
			//Base de datos
			->add( 'db_host', 'TextType', array(
				'label' => 'Host:',
				'required' => false
			))
			->add( 'db_user', 'TextType', array(
				'label' => 'Usuario:',
				'required' => false
			))
			->add( 'db_pass', 'TextType', array(
				'label' => 'Contraseña:',
				'required' => false
			))
			->add( 'db_database', 'TextType', array(
				'label' => 'Base de datos:',
				'required' => false
			))
			//Datos generales
			->add( 'url_site', 'TextType', array(
				'label' => 'Url del sitio:',
				'default' => URL,
				'required' => false
			))
			->add( 'admin_email', 'EmailType', array(
				'label' => 'Correo del administrador:',
				'required' => false
			))
			->add( 'no_reply_mail', 'EmailType', array(
				'label' => 'Correo de no-reply:',
				'required' => false
			))
			->add( 'no_reply_mail_name', 'TextType', array(
				'label' => 'Nombre del correo de no-reply:',
				'required' => false
			))
			//Directorios
			->add( 'dir_srcs', 'TextType', array(
				'label' => 'Directorio de recursos:',
				'required' => false,
				'default' => 'o_srcs',
				'pattern' => '^[a-zA-Z0-9\-_]+$',
				'patternError' => 'Solo se permiten letras, números, guiones y guiones bajos.'
			))
			->add( 'dir_libraries', 'TextType', array(
				'label' => 'Directorio de las librerias:',
				'required' => false,
				'default' => 'o_libraries',
				'pattern' => '^[a-zA-Z0-9\-_]+$',
				'patternError' => 'Solo se permiten letras, números, guiones y guiones bajos.'
			))
			//Otras configuraciones
			->add( 'timezone', 'SelectType', array(
				'label' => 'Zona horaria',
				'required' => false,
				'default' => 'America/Mexico_City',
				'choices' => $timezoneList
			))
			->add( 'session_name', 'TextType', array(
				'label' => 'Nombre de la variable sesión',
				'required' => false,
				'default' => 'orange',
				'pattern' => '^[a-zA-Z0-9\-_]+$',
				'patternError' => 'Solo se permiten letras, números, guiones y guiones bajos.'
			))
			->add( 'is_debug', 'MultiChoiceType', array(
				'label' => 'Modo de depurador:',
				'required' => false,
				'choices' => array(
					1 => 'Activado'
				)
			))
			->add( 'template', 'TextType', array(
				'label' => 'Template:',
				'required' => false,
				'default' => 'default',
				'pattern' => '^[a-zA-Z0-9\-_]+$',
				'patternError' => 'Solo se permiten letras, números, guiones y guiones bajos.'
			))
			->add( 'use_cache', 'MultiChoiceType', array(
				'label' => 'Usar cache:',
				'required' => false,
				'choices' => array(
					1 => 'Activado'
				)
			))
			->add( 'send', 'SubmitType', array('text' => 'Enviar'));

		if( isset($_POST['install']) ){
			$form->submit();

			if( $form->isValid() ){
				$fields = $form->getFields();

				$dirSrcs = $fields['dir_srcs']->default;
				$dirLibraries = $fields['dir_libraries']->default;
				$sessionName = $fields['session_name']->default;

				$modules = '';
				foreach ( $GLOBALS['MODULES'] as $name => $module) {
					$t = '$MODULES[\''.$name.'\'] = '.var_export($module, true).';';
					$t = preg_replace( '/\r|\n/', '', $t );
					$t = str_replace( ' (', '(', $t );
					$modules .= $t."\n";
				}
				$modules = trim($modules);

				if( $dirSrcs == '' ){
					$dirSrcs = 'o_srcs';
				}
				
				if( $dirLibraries == '' ){
					$dirLibraries = 'o_libraries';
				}

				if( $sessionName == '' ){
					$sessionName = 'orange';
				}

				$configFile = Template::getView( 'o-config.php.twig', array(
					'db_host' => $fields['db_host']->default,
					'db_user' => $fields['db_user']->default,
					'db_pass' => $fields['db_pass']->default,
					'db_database' => $fields['db_database']->default,
					'timezone' => $fields['timezone']->default,
					'admin_email' => $fields['admin_email']->default,
					'no_reply_mail' => $fields['no_reply_mail']->default,
					'no_reply_mail_name' => $fields['no_reply_mail_name']->default,
					'url_site' => $fields['url_site']->default,
					'modules' => $modules,
					'dir_srcs' => $dirSrcs,
					'dir_libraries' => $dirLibraries,
					'session_name' => $sessionName,
					'is_debug' => $fields['is_debug']->default,
					'template' => $fields['template']->default,
					'use_cache' => $fields['use_cache']->default
				));

				$configFile = '<?php'."\n".$configFile."\n".'?>';
				$saved = file_put_contents( ABSPATH.'o-config.php.log', $configFile );

				if( !$saved ){
					//oRedirect( URL );
				}else{
					$form->addNotice( 'Ocurrió un error al general el archivo de configuración, asegúrese que se tienen permisos de escritura.', 'error' );
				}
			}
		}

		Template::render( 'index.html.twig', array( 'form' => $form ) );
	}
}
?>