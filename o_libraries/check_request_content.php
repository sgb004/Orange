<?php
//found that the weight of the information submitted does not exceed the allowable
if($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST) && empty($_FILES) && $_SERVER['CONTENT_LENGTH'] > 0){
	$tamMax = ini_get('post_max_size');
	$tamMaxStr = $tamMax;
	$uniDat = substr($tamMax,-1);
	$valMax = str_replace($uniDat,'',$tamMax);

	if($uniDat == 'K'){
		$valMax = $valMax * 1024;
	}else if($uniDat == 'M'){
		$valMax = $valMax * pow(1024,2);
	}else if($uniDat == 'G'){
		$valMax = $valMax * pow(1024,3);
	}

	if($_SERVER['CONTENT_LENGTH'] > $valMax){
		echo '{"error":"Por favor seleccione un archivo de menos de '.$tamMaxStr.'b."}';
		exit;
	}
}
?>