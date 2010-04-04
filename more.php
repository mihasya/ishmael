<?php
	#
	# 'more' UI - additional info about a particular query
	#

	require_once('init.php');

	$checksum = $_GET['checksum'];

	$q = "SELECT 
			ts_max,
			query_time_sum,
			sample
		FROM
			query_review_history
		WHERE
			checksum={$checksum}
		ORDER BY ts_max DESC";
	$result=mysql_query($q);

	$rows = array();
	while ($row = mysql_fetch_assoc($result)) {
		$rows[] = $row;
	}

	require("more.tpl");