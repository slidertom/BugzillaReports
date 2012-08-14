<?php
/*
	Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
	All rights reserved.
	To use this component please contact slidertom@gmail.com to obtain a license.
*/

$g_logo_img = "";//"../res/logo.png";
$g_host     = php_uname("n");

function get_logo_img_full_path()
{
	global $g_logo_img;
	return $g_logo_img;
}

function get_server_host_name()
{
	global $g_host;
	return $g_host;
}

function get_system_name()
{
	return "Bugzilla Reports";
}

function get_version()
{
	return "3.0";
}

?>