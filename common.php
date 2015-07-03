<?php
/*
 * Copyright Smx (smxdev4@gmail.com) 2016
 */
function safe_sprintf($argc, $argv){
	if($argc == 0)
		return  "";
	if($argc == 1)
		return $argv[0];
	$fmt = array_shift($argv);
	return vsprintf($fmt, $argv);
}
?>
