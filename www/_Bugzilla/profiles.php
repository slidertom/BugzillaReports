<?php
/*
	Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
	All rights reserved.
	To use this component please contact slidertom@gmail.com to obtain a license.
*/

require_once("../bugzilla_base/bugs_sql.php");

class CProfile
{
    public $m_id;
	public $m_login_name;
	public $m_real_name;
	public $m_disabled_text;
	public $m_bug_count;
};

function get_user_profiles($dbh)
{
	$sql_profile = "SELECT * FROM profiles";
	$profiles = $dbh->query($sql_profile);
	$users = array();
	foreach ($profiles as $row)
	{
		$prof = new CProfile();
		$user_id = $row['userid'];
		$prof->m_login_name    = $row['login_name'];
		$prof->m_real_name     = $row['realname'];
		$prof->m_disabled_text = $row['disabledtext'];
		$prof->m_bug_count     = get_developer_bugs_count($dbh, $user_id);
		$prof->m_id            = $user_id;
		$users[$user_id] = $prof;
	}
	
	return $users;
};

function find_user_by_login($users, $login_name)
{
	foreach ($users as $i => $user)
	{
		if ($login_name == $user->m_login_name)
		{
			return $i;
		}
	}
	
	return NULL;
}

?>