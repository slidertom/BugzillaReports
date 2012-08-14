<?php
/*
	Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
	All rights reserved.
	To use this component please contact slidertom@gmail.com to obtain a license.
	
	Allowed extension by Gediminas Luzys.
*/

require_once("bugs_fnc.php");
require_once("profiles.php");
require_once("products.php");
require_once("../bugzilla_base/connect_to_bugzilla_db.php");

function bugs_get_open($dbh, $product_id, $milestone, &$users, &$products)
{
	$sql = "SELECT * FROM bugs WHERE (bug_status='NEW' OR bug_status='ASSIGNED' OR bug_status='REOPENED')";

	if (!is_null($product_id))
		$sql .= " AND product_id ='$product_id'";
	
	if (!empty($milestone))
		$sql .= " AND target_milestone='$milestone'";
			
	//echo "SQL1: $sql\n\n";
	return bugs_get($dbh, $users, $products, $sql);
}

function bugs_get_no_estimation($bugs)
{
	$bugs_no_est = array();
	foreach ($bugs as $bug)
	{
		if ( $bug->m_estimated_time <= 0 )
		{
			$bugs_no_est[] = $bug;
		}
	}
	
	return $bugs_no_est;
}

function bugs_no_estimation_notify($product_name, $milestone, $supervisors, $week_day, $do_log = false)
{
	$fs = "<font color='red'> <b>";
	$fe = "</b> </font>";

	$hostanme  = get_bugs_db_hostname();
	
	echo "Connecting to database [$hostname]\n";
	$dbh = connect_to_bugzilla_db();
	
	if ($dbh) 
	{
		echo "Connected to database [$hostname]\n";
	}
	else
	{
		return;
	}
	
	$users = get_user_profiles($dbh); // <userid><login_name>
	//echo "USERS:\n"; foreach ($users as $user) echo $user->m_real_name . " - " . $user->m_login_name . "\n";
	
	$products          = products_get($dbh);
	$product_name_mile = $product_name . (empty($milestone) ? '' : " $milestone");
	$product_id        = get_product_id_by_name($products, $product_name);
	//echo "\nPRODUCTS:\n";
	//foreach ($products as $product) echo $product->m_id . " - " . $product->m_name . "\n"; }
	//echo "\nPRODUCT: $product_name (id=$product_id)\n";
	
	if (is_null($product_id))
	{
		echo "ERROR: Product [$product_name]" . (empty($milestone) ? '' : " with milestone [$milestone]") . " not found in bugzilla database\n";
		return;
	}

	$bugs           = bugs_get_open($dbh, $product_id, $milestone, $users, $products);
	$total_bugs_cnt = count($bugs);
	//echo "\nOPENED BUGS ($total_bugs_cnt):\n";
	//foreach ($bugs as $bug) print_r($bug);
	
	$bugs_no_est     = bugs_get_no_estimation($bugs);
	$no_est_bugs_cnt = count($bugs_no_est);
	//echo "\nNOT ESTIMATED BUGS ($no_est_bugs_cnt/$total_bugs_cnt):\n";
	//foreach ($bugs_no_est as $bug) echo " #" . $bug->m_bug_id . " - " . $bug->m_status . " - " . $bug->m_summary . " (" . $bug->m_assigned_to->m_login_name . ")\n";

	echo "\n";
	echo "$total_bugs_cnt  total opened bug(s) in this project\n\n";
	echo (0 != $no_est_bugs_cnt ? "WARNING: " : "") . "$no_est_bugs_cnt total NOT ESTIMATED bugs in this project\n\n";

	$bugs_by_user = Array();
	foreach ($bugs_no_est as $bug)
	{
		$mail_to = $bug->m_assigned_to->m_login_name;
		
		if (!isset($bugs_by_user[$mail_to]))
			$bugs_by_user[$mail_to] = Array();
		
		$bugs_by_user[$mail_to][] = $bug;
	}

	foreach ($bugs_by_user as $mail_to => $user_bugs)
	{
		$user_index           = find_user_by_login($users, $mail_to);
		$user                 = $users[$user_index];
		$user_name            = $user->m_real_name;
		$user_no_set_cnt      = count($user_bugs);
		
		echo hr() . hr() . "\n" . (0 != $user_no_set_cnt ? "WARNING: " : "") . "$user_no_set_cnt  not estimated bugs in $product_name by $mail_to\n\n";
		
		$headers =    'From: mxkbuilder@matrix-software.lt'         . "\r\n"
					. 'X-Mailer: PHP/' . phpversion()               . "\r\n"
					. 'MIME-Version: 1.0'                           . "\r\n"
					. 'Content-Type: text/html; charset = "UTF-8"'  . "\r\n";

		$user_subject  = "$product_name_mile: [$user_no_set_cnt] bug(s) not estimated by You";
		$super_subject = "$product_name_mile: [$user_no_set_cnt] bug(s) not estimated by $user_name";
		


		$body = "	<html>
					<head> <title>Not-estimated bugs</title> </head>
					<body>
					<p> <b> $product_name_mile: </b> </p>
					<table border=0>
						<tr> <td align=right>     $total_bugs_cnt      </td> <td>     open bugs total                       </td> </tr>
						<tr> <td align=right>     $no_est_bugs_cnt     </td> <td>     not estimated bugs total              </td> </tr>
						<tr> <td align=right> $fs $user_no_set_cnt $fe </td> <td> $fs not estimated bugs by $user_name $fe: </td> </tr>
					</table>

					<br/>
					<table border=1>
						<tr>
							<th> Bug     </th>
							<th> Status  </th>
							<th> Summary </th>
						</tr>
			  ";
  	
		$user_prod_milestones = Array();

		foreach ($user_bugs as $bug)
		{
			$user_prod_milestones[] = $bug->m_target_milestone;
			
			$link = generate_bug_link($bug->m_bug_id);
		
			$body .= "	<tr>
						<td> <a href = '$link'> #{$bug->m_bug_id} </a> </td>
						<td> {$bug->m_status} </td>
						<td> {$bug->m_summary} </td>
						</tr>";

			echo " #" . $bug->m_bug_id . " - " . $bug->m_status . " - " . $bug->m_summary . "\r\n";;
		}
		
		$user_prod_milestones = array_unique($user_prod_milestones);
		
		$ip        = get_ip();
		$link_user = "http://{$ip}/_BugzillaDev/index.php?#{$user_index}";

		switch ($week_day)
		{
		case 0: $on_day = ' everyday';        break;
		case 1: $on_day = ' every Monday';    break;
		case 2: $on_day = ' every Thuesday';  break;
		case 3: $on_day = ' every Wednesday'; break;
		case 4: $on_day = ' every Thursday';  break;
		case 5: $on_day = ' every Friday';    break;
		case 6: $on_day = ' every Saturday';  break;
		case 7: $on_day = ' every Sunday';    break;
		default: assert(FALSE);     break;
		}
		
		$body .= "	</table>
					&nbsp;
					&nbsp;
					<hr />
					<p> <font size=-2> This check is performed $on_day for this project (+milestone) </font> </p>
					<table border=0>
					";
					
		foreach ($user_prod_milestones as $user_prod_milestone)
		{
			$link_prod = "http://{$ip}/_Bugzilla/index.php?#{$product_id}?{$user_prod_milestone}";
			$body .= "<tr> <td> <font size=-2> $product_name $user_prod_milestone bugs: </font> </td> <td> <font size=-2>  <a href='$link_prod'> $link_prod </a>   </font> </td> </tr>
					";
		}
					
		$body .= "	<tr> <td> <font size=-2> $user_name bugs:         </font> </td> <td> <font size=-2>  <a href='$link_user'> $link_user </a>   </font> </td> </tr>
					</table>
					
					</body>
					</html>";

		//$mail_to = 'gediminas.luzys@gmail.com'; //DEBUG
		
		mail($mail_to, $user_subject, $body, $headers);
		
		if (!empty($supervisors))
			mail("$supervisors", $super_subject, $body, $headers);

		echo hr() . hr() . "\n";
	}




	echo "\n";
}

//bugs_get_no_estimation_bugs();

?>