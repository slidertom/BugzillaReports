<?php
/*
	Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
	All rights reserved.
	To use this component please contact slidertom@gmail.com to obtain a license.
*/

function bugs_split_by_milestone(&$bugs)
{
	$mile_bugs = array();
	foreach ($bugs as $bug) {
		if ( !isset($mile_bugs[$bug->m_target_milestone]) ) {
			$mile_bugs[$bug->m_target_milestone] = array();
		}
		$mile_bugs[$bug->m_target_milestone][] = $bug;
	}
	return $mile_bugs;
}


?>