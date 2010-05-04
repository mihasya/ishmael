<?php
	#
	# explain UI
	#

	require_once('init.php');

	$checksum = $_GET['checksum'];

	$q = "SELECT * FROM {$host_conf['db_query_review_history_table']} WHERE checksum = '{$checksum}'";
	$result = mysql_query($q);
	$row = mysql_fetch_assoc($result);
	$query = $row['sample'];
	$explain = "EXPLAIN ".preg_replace('|^\s*\/\*.*\*\/|', '', $query);
	
	mysql_select_db($host_conf['db_database_live']) or die("Unable to select database");

	$is_select_query = (stripos($explain, 'SELECT') !== false);

	if ($is_select_query) {
		$result = mysql_query($explain);
	}
	$rows = array();

	$tables = array();

	while ($is_select_query && $row = mysql_fetch_assoc($result)) {
		$row['possible_keys'] = implode('<br />', explode(',', $row['possible_keys']));
		$rows[] = $row;
		$table_alias = $row['table'];
		$create_result = mysql_query("SHOW CREATE TABLE {$table_alias}");
		if (!$create_result) {
			# try to figure out the alias
			$pattern = "/(?i)([\w]+?)(?=\s+(?:AS\s+)?\b{$table_alias}\b)/";
			list($columns, $rest) = preg_split("/(?i)from/", $query);
			preg_match_all($pattern, $rest, $matches);
			$table = $matches[0][0];
			$create_result = mysql_query("SHOW CREATE TABLE {$table}");
			if (!$create_result) {
				$table = '';
			}
		} else {
			$table = $table_alias;
		}
		if ($create_result) {
			$tables[$table] = mysql_fetch_assoc($create_result);
		}
	}

	require("explain.tpl");
