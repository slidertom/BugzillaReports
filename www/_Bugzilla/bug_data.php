<?php
/*
	Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
	All rights reserved.
	To use this component please contact slidertom@gmail.com to obtain a license.
*/

require_once("../bugzilla_base/_bugzilla_reports_settings.php");

class CBugData
{
	public $m_bug_id;
	public $m_severity;
	public $m_priority;
	public $m_assigned_to;
	public $m_reporter;
	public $m_summary;
	public $m_estimated_time;
	public $m_remaining_time;
	public $m_worked_time;
	public $m_status;
	public $m_product;
	public $m_target_milestone;
	public $m_start_date;
	public $m_end_date;
	
	public function IsOpened()
	{
		if ( $this->m_status == "NEW" || $this->m_status=="ASSIGNED" || $this->m_status=="REOPENED" )
		{
			return true;
		}
		
		return false;
	}
	
	public function get_bug_remaining_time()
	{
		if ( $this->IsOpened() )
		{
			if ($this->m_remaining_time > 0 )
			{
				return $this->m_remaining_time;
			}
			else if ( $this->m_estimated_time > $this->m_worked_time )
			{
				return $this->m_estimated_time - $this->m_worked_time; // Database value is not OK.
			}
			else if ( $this->m_estimated_time <= $this->m_worked_time )
			{
				return 0;
			}
		}
		
		return 0;
	}
	
	public function get_complete()
	{
		$rem = $this->get_bug_remaining_time();
		$wrk = $this->m_worked_time;
		$all = $rem + $wrk;
		$per = $all > 0 ? $wrk / $all * 100 : 0;
		$per = number_format($per, 0);
		$per = $per. "%";
		return $per;
	}
};

function get_bugs_work_time($bugs)
{
	$left_time = 0;
	foreach ($bugs as $bug)
	{
		$left_time += $bug->get_bug_remaining_time();
	}
	
	return $left_time;
}

function hours_to_days($hours)
{
	$all_days = $hours / 8;
	$all_days = number_format($all_days, 2);
	return $all_days;
}

function bugs_explode_by_product(&$product_bugs, &$bugs)
{
	$product_bugs = array();
	foreach ($bugs as $bug )
	{
		if ( !isset($product_bugs[$bug->m_product->m_name]) )
		{
			$product_bugs[$bug->m_product->m_name] = array();
		}
		
		$product_bugs[$bug->m_product->m_name][] = $bug;
	}
}

function bugs_explode_by_priority(&$priority, &$bugs)
{ 
	$priority = array();
	foreach ($bugs as $bug )
	{
		if ( !isset($priority[$bug->m_priority]) )
		{
			$priority[$bug->m_priority] = array();
		}
		
		$priority[$bug->m_priority][$bug->m_bug_id] = $bug;
	}
}

function bugs_explode_by_product_id(&$product_bugs, &$bugs)
{
	$product_bugs = array();
	foreach ($bugs as $bug )
	{
		if ( !isset($product_bugs[$bug->m_product->m_id]) )
		{
			$product_bugs[$bug->m_product->m_id] = array();
		}
		
		$product_bugs[$bug->m_product->m_id][$bug->m_bug_id] = $bug;
	}
}

function bugs_explode_by_product_developer_id(&$developer_bugs, &$bugs)
{
	$developer_bugs = array();
	foreach ($bugs as $bug )
	{
		if ( !isset($developer_bugs[$bug->m_assigned_to->m_id]) )
		{
			$developer_bugs[$bug->m_assigned_to->m_id] = array();
		}
		
		$developer_bugs[$bug->m_assigned_to->m_id][$bug->m_bug_id] = $bug;
	}
}

function bugs_echo_table_header()
{
	echo "<thead>\n";
	echo "<tr class='header'>\n";
	/* 1*/echo "\t<th width= 50> Bug      </th>\n";
	/* 2*/echo "\t<th width= 90> Sev      </th>\n";
	/* 3*/echo "\t<th width= 40> Pri      </th>\n";
	/* 4*/echo "\t<th width=150> Assignee </th>\n";
	/* 5*/echo "\t<th width= 80> Worked   </th>\n";
	/* 6*/echo "\t<th width= 50> Left     </th>\n";
	/* 7*/echo "\t<th width= 80> Complete </th>\n";
	/* 8*/echo "\t<th>           Product  </th>\n";
	/* 9*/echo "\t<th width= 70> TargetM  </th>\n";
	/* 9*/echo "\t<th>           Start d. </th>\n";
	/* 9*/echo "\t<th>           End d. </th>\n";
	/*10*/echo "\t<th>           Summary  </th>\n";
	echo "</tr>\n";
	echo "</thead>\n";
}

function bug_echo_row_summary(&$bug)
{
	$unestimated    = ($bug->IsOpened() && ($bug->m_estimated_time <= 0));
	$unest_class    = $unestimated ? "class='unestimated'" : "";
	$bug_class      = $bug->m_severity;

	$worked_time    = $bug->m_worked_time;
	$complete       = $bug->get_complete();
	$remaining_time = $unestimated ? "X" : $bug->get_bug_remaining_time();
	$email          = $bug->m_assigned_to->m_login_name;
	$name           = $bug->m_assigned_to->m_real_name;
	$git_changes    = '../_GIT/index.php?filter=%23' . $bug->m_bug_id;
	$product_name   = $bug->m_product->m_name;
	$milestone      = $bug->m_target_milestone;
	$start_date     = "";
	$end_date       = "";
	
	if ( $bug->m_start_date && $bug->m_end_date)
	{
		$start_date     = $bug->m_start_date->format('Y-m-d');
		$end_date       = $bug->m_end_date->format('Y-m-d');
	}
	
	echo "<tr>\n";
	/* 1*/echo "\t<td>".generate_bug_link_href($bug->m_bug_id)."                                </td>\n";
	/* 2*/echo "\t<td class = '$bug_class'>                          $bug->m_severity           </td>\n";
	/* 3*/echo "\t<td>                                               $bug->m_priority           </td>\n";
	/* 4*/echo "\t<td>                      <a href=mailto:'$email'> $name               </a>   </td>\n";
	/* 5*/echo "\t<td align=right>                                   $bug->m_worked_time        </td>\n";
	/* 6*/echo "\t<td $unest_class align=right>               		 $remaining_time            </td>\n";
	/* 7*/echo "\t<td align=right>          <a href='$git_changes'>  $complete           </a>   </td>\n";
	/* 8*/echo "\t<td>											     $product_name</td>\n";
	/* 9*/echo "\t<td>											     $milestone</td>\n";
	/* 9*/echo "\t<td>											     $start_date</td>\n";
	/* 9*/echo "\t<td>											     $end_date</td>\n";
    /*10*/echo "\t<td class = '$bug_class'>                          &nbsp;&nbsp;$bug->m_summary</td>\n";
	echo "</tr>\n\n";
}

function bugs_echo_tbody_summary(&$bugs)
{
	echo "<tbody>\n";
	foreach ($bugs as $bug) {
		bug_echo_row_summary($bug);
	}
	echo "</tbody>\n";
}

function bugs_echo_table(&$bugs, $table_id, $table_class)
{
	echo "<table id='$table_id' class='$table_class'>\n";
	bugs_echo_table_header();
	bugs_echo_tbody_summary($bugs);
	echo "</table>\n";
}

?>