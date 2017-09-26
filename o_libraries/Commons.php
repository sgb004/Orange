<?php
class Commons{
	static function print_options_select($options, $value=''){
		$html = '';
		foreach ($options as $key => $text) {
			$selected = ($key == $value) ? 'selected' : '';
			$html .= '<option value="'.$key.'" '.$selected.'>'.$text.'</option>';
		}
		return $html;
	}

	static function str($str){
		return htmlentities($str, ENT_QUOTES, "UTF-8");
	}

	static function date_check($date){
		return ( $date == '' ) ? 'Sin registro' : $date = Commons::date( $date );
	}

	static function date($date){
		return date('d-m-Y H:i:s', strtotime( $date ) );
	}

	static function generate_password(){
		return chr(rand(48, 57)).chr(rand(65, 90)).chr(rand(97, 122)).chr(rand(48, 57)).chr(rand(65, 90)).chr(rand(97, 122)).chr(rand(65, 90)).chr(rand(97, 122));
	}

	static function print_users_table( $users, $usersMark, $default, $usersSelected ){
		$table = '';

		foreach ($users as $userKey => $user) {
			$user->name = Commons::str($user->name.' '.$user->lastname);
			$user->email = Commons::str($user->email);

			switch ($user->type) {
				case 'seguimiento':
					$user->type = 'Seguimiento';
					break;
				case 'operacion':
					$user->type = 'Operación';
					break;
			}

			$mark = isset( $usersMark[$user->user_id] ) ? $usersMark[$user->user_id] : '';
			$checked = isset( $usersSelected[$user->user_id] ) ? $usersSelected[$user->user_id] : $default;

			$table .= '
			<tr id="user_select_'.$userKey.'_row" class="'.$mark.'">
				<td width="1">
					<label class="checkbox">
						<input type="checkbox" name="users_select[]" id="user_select_'.$userKey.'" value="'.$user->user_id.'" '.$checked.' required>
						<i></i>
					</label>
				</td>
				<td>
					<label for="user_select_'.$userKey.'">'.$user->name.'</label>
				</td>
				<td>
					<label for="user_select_'.$userKey.'">'.$user->email.'</label>
				</td>
				<td>
					<label for="user_select_'.$userKey.'">'.$user->type.'</label>
				</td>
			</tr>';
		}

		return $table;
	}
}
?>