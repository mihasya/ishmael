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
			checksum={$checksum}
		ORDER BY ts_max DESC";
	$result=mysql_query($q);

	$rows = array();
	while ($row = mysql_fetch_assoc($result)) {
		$rows[] = $row;
	}

	require("more.tpl");