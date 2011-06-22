<?php
	#
	# 'more' UI - additional info about a particular query
	#

	require_once('init.php');

	$checksum = $_GET['checksum'];

	$fields = array(
		'Query_time',
		'Lock_time',
		'Rows_sent',
		'Rows_examined',
	);

	$points = array(
		'sum', 'min', 'max', 'pct_95', 'stddev', 'median',
	);

	$q = "SELECT 
			*
		FROM
			{$host_conf['db_query_review_history_table']}
		WHERE
			checksum={$checksum} AND
			ts_max > date_sub(now(),interval $hours hour) 
		ORDER BY ts_max ASC";
	$result=mysql_query($q);

	$rows = array();
	while ($row = mysql_fetch_assoc($result)) {
		$rows[] = $row;
	}

	require("more.tpl");
