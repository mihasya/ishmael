<?php
	#
	# explain UI
	#

	require_once('init.php');

	$checksum = $_GET['checksum'];

	$q = "SELECT * FROM query_review_history WHERE checksum = '{$checksum}'";
	$result = mysql_query($q);
	$row = mysql_fetch_assoc($result);
	$query = $row['sample'];
	$explain = "EXPLAIN {$query}";
	
	@mysql_select_db($conf['db_database_live']) or die("Unable to select database");
	$result = mysql_query($explain);
	$rows = array();
	while ($row = mysql_fetch_assoc($result)) {
		$row['possible_keys'] = implode('<br />', explode(',', $row['possible_keys']));
		$rows[] = $row;
	}

	require("explain.tpl");