<?php
/*
	Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
	All rights reserved.
	To use this component please contact slidertom@gmail.com to obtain a license.
*/

// do not use these global variables directly, please use predefined functions instead.

$g_hostname          = 'localhost';     // bugzilla mysql hostname 
$g_bugs_db_name      = 'bugs';          // bugzilla mysql database name
$g_username          = 'reporter';      // bugzilla mysql username 
$g_password          = 'password';      // bugzilla mysql user password
$g_bugzilla_link     = "http://localhost/bugzilla";  // bugzilla http link, used to generate <a href> bug links, 
                                                     // do check out generate_bug_link function                                                          
$server_title        = "Bugzilla Reports";

function generate_bug_link($bug_id)
{
	global $g_bugzilla_link;
	return $g_bugzilla_link."/show_bug.cgi?id=".$bug_id;
}

function get_bugs_db_hostname()
{
	global $g_hostname;
	return $g_hostname;
}

function get_bugs_db_name()
{
	global $g_bugs_db_name;
	return $g_bugs_db_name;
}

function get_bugs_db_username()
{
	global $g_username;
	return $g_username;
}

function get_bugs_db_password()
{
	global $g_password;
	return $g_password;
}

function get_bugs_open_defines()
{
	$defs = array('UNCONFIRMED','NEW','ASSIGNED','REOPENED','IN_PROGRESS','CONFIRMED');
	return $defs;
}

function get_bugs_close_defines()
{
	$defs = array('VERIFIED','CLOSED','RESOLVED','WORKSFORME', 'WONTFIX', 'DUPLICATE', 'INVALID', 'FIXED', 'INCOMPLETE');
	return $defs;
}

function generate_bug_link_href($bug_id)
{
	$link = generate_bug_link($bug_id);
	global $g_open_bugs_in_the_new_tab;
	if ( $g_open_bugs_in_the_new_tab )
	{
		return "<a target='_blank' href='$link'>$bug_id</a>";
	}
	else
	{
		return "<a href='$link'>$bug_id</a>";
	}
}

?>