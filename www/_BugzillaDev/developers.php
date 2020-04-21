<?php
/*
	Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
	All rights reserved.
	To use this component please contact slidertom@gmail.com to obtain a license.
*/
                                 
require_once (__DIR__)."/../_Bugzilla/profiles.php";

function cmp_by($d1, $d2, $field)   { return ($d1->$field == $d2->$field) ? 0 : ($d1->$field < $d2->$field) ? -1 : 1; }
function cmp_by_real_name($d1, $d2) { return cmp_by($d1, $d2, 'm_real_name'); }

function developers_to_combo($developers, $dev_to_select)
{	
	$first_id = strlen($dev_to_select) > 0 ? $dev_to_select : -1;
	foreach ($developers as $id => $dev )
	{
		if ( $dev->m_real_name != "" && $dev->m_disabled_text == "" )
		{
			if ( $first_id == -1 ) {
				$first_id = $id;
			}
			
			$bug_count = $dev->m_bug_count;
			if ( $dev->m_bug_count > 0 ) {
				if ( $dev_to_select == $id ) {
					echo "<option value=$id selected>".$dev->m_real_name."&nbsp;&nbsp;(bugs count: ".$bug_count.")"."</option>";	
					$first_id = $id;
				}
				else {
					echo "<option value=$id>".$dev->m_real_name."&nbsp;&nbsp;(bugs count: ".$bug_count.")"."</option>";	
				}
			}
		}
	}
	
	return $first_id;
}

function developers_create_combo($dbh, $dev)
{
	$developers = get_user_profiles($dbh); // <userid><login_name>
	
	uasort($developers, 'cmp_by_real_name');
	
	echo "<select name='Developer' id='Developer'>";
	$sel_id = developers_to_combo($developers, $dev);
	echo"</select>";
	
	return $sel_id;
}

?>