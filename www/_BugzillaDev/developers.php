<?php
/*
	Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
	All rights reserved.
	To use this component please contact slidertom@gmail.com to obtain a license.
*/
                                 
require_once(dirname(__FILE__)."/../_Bugzilla/profiles.php");

function cmp_by($d1, $d2, $field)   { return ($d1->$field == $d2->$field) ? 0 : ($d1->$field < $d2->$field) ? -1 : 1; }
function cmp_by_real_name($d1, $d2) { return cmp_by($d1, $d2, 'm_real_name'); }

function developers_to_combo($developers)
{	
	$first_id = -1;
	foreach ($developers as $id => $dev )
	{
		if ( $dev->m_real_name != "" && $dev->m_disabled_text == "" )
		{
			if ( $first_id == -1 )
			{
				$first_id = $id;
			}
			
			$bug_count = $dev->m_bug_count;
			if ( $dev->m_bug_count > 0 )
			{
				echo "<option value=$id>".$dev->m_real_name."&nbsp;&nbsp;(bugs count: ".$bug_count.")"."</option>";	
			}
		}
	}
	
	return $first_id;
}

function developers_create_combo($dbh)
{
	$developers = get_user_profiles($dbh); // <userid><login_name>
	
	// move MxKTriage to list start
	$developers[57]->m_real_name = '- ' . $developers[57]->m_real_name . ' -';

	uasort($developers, 'cmp_by_real_name');
	
	echo "<select name='Developer' id='Developer'>";
	$sel_id = developers_to_combo($developers);
	echo"</select>";
	
	return $sel_id;
}

?>