<?php
	#
	# main dashboard UI - listing of queries sorted by various characteristics
	#

	require_once('init.php');

	# hours
	$hours = ($_GET['hours']) ? $_GET['hours'] : 24;
	$sort = ($_GET['sort']) ? $_GET['sort'] : "ratio";

	# Get total amount of query time (for % later)
	$q = "SELECT
			SUM(query_time_sum) 
		FROM 
			query_review_history 
		WHERE 
			ts_max > date_sub(now(),interval {$hours} hour);";
	$result = mysql_query($q);
	$query_time_sum = mysql_result($result, 0);

	# Get total # of queries in history window (for % later)
	$q = "SELECT
			SUM(ts_cnt) 
		FROM 
			query_review_history 
		WHERE 
			ts_max > date_sub(now(),interval $hours hour);";
	$result = mysql_query($q);
	$query_qty_sum = mysql_result($result, 0);

	# Get list of bad queries
	$q = "SELECT
			checksum,
			sample,
			SUM(ts_cnt) AS count,
			SUM(query_time_sum) AS time,
			ts_max AS time_max,
			(SUM(ts_cnt)/{$query_qty_sum}*100) AS qty_pct,
			(SUM(query_time_sum)/{$query_time_sum}*100) AS time_pct,
			((SUM(query_time_sum)/{$query_time_sum}*100)/(SUM(ts_cnt)/{$query_qty_sum}*100)) AS ratio
		FROM 
			query_review_history 
		WHERE 
			ts_max > date_sub(now(),interval $hours hour) 
		GROUP BY checksum ORDER BY $sort DESC LIMIT 20";

	$result = mysql_query($q);
	$err = mysql_error();
	print_r($err);
	$rows = array();
	while ($row = mysql_fetch_assoc($result)) {
		$rows[] = $row;
	}

	#
	# spaghetti template separation
	#
	require("dashboard.tpl");
?>