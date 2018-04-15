<?php
function p($arr,$title=null,$end=false){
	if(!is_null($title)){
		echo "<h6 style='color:#f80'>* $title</h6>";
	}
	if(is_string($arr)&&strlen($arr)){
		echo "<br><h4>* $arr</h4>";
	}else{
		echo "<pre>";
		print_r($arr);
		echo "</pre>";
	}
	if($end)return;
	echo "<br>";
	echo "=============================================================";
	echo "<br>";
}
