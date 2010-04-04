<?php
	require_once('init.php');

	function _format_query_callback($str) {
		$fragment_class = "keyword";
		if (preg_match("/^\s*\/\*.*?\*\/\s*$/", $str[0])) {
			$fragment_class = "comment";
		}
		return "<span class=\"{$fragment_class}\">" . $str[0] . "</span>";
	}

	function format_query($query) {
		$pattern = "/((\/\*.*?\*\/)|SELECT|FROM|WHERE|(INNER|OUTER|STRAIGHT|RIGHT|LEFT(?:\s?))?JOIN|AND|OR|ORDER BY|GROUP BY|DESC)/";
		$query = preg_replace_callback($pattern, '_format_query_callback', $query);
		return $query;
	}

	# hours
	$hours = ($_GET['hours']) ? $_GET['hours'] : 24;
	$sort = ($_GET['sort']) ? $_GET['sort'] : "ratio";
	mysql_connect($conf['db_host'],$conf['db_user'],$conf['db_password']);
	@mysql_select_db($conf['db_database']) or die("Unable to select database");

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