<?php
	#
	# explain UI
	#

	require_once('init.php');

	$checksum = $_GET['checksum'];

	$q = "SELECT * FROM {$conf['db_query_review_history_table']} WHERE checksum = '{$checksum}'";
	$result = mysql_query($q);
	$row = mysql_fetch_assoc($result);
	$query = $row['sample'];
	$explain = "EXPLAIN {$query}";
	
	@mysql_select_db($conf['db_database_live']) or die("Unable to select database");
	$result = mysql_query($explain);
	$rows = array();

	$tables = array();

	while ($row = mysql_fetch_assoc($result)) {
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