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
			h.*, r.fingerprint
		FROM
			{$host_conf['db_query_review_history_table']} h
		INNER JOIN
			{$host_conf['db_query_review_table']} r
		USING(checksum) WHERE
			checksum={$checksum} AND
			ts_max > date_sub(now(),interval $hours hour) 
		ORDER BY ts_max ASC";
	$result=mysql_query($q);

	$rows = array();
	while ($row = mysql_fetch_assoc($result)) {
		if ($conf['anon']) {
			if (preg_match('@/\*.*\*/@', $row['sample'], $matches)) {
				if (preg_match('@ 127.0.0.1 \*/@', $matches[0])) {
					$extra = $matches[0];
				} elseif (preg_match('@ \d+\.\d+\.\d+\.\d+ @', $matches[0])) {
					$extra = preg_replace('@\d+\.\d+\.\d+\.\d+@', 'External_IP', $matches[0]);
				} elseif (preg_match('@ \S+ \*/@', $matches[0])) {
					$extra = preg_replace('@ \S+ \*/@', ' User */', $matches[0]);
				}		
				else {
					$extra = $matches[0];
				}
				$row['sample'] = $extra . ' ' . $row['fingerprint'];
			} else {
				$row['sample'] = $row['fingerprint'];
			}
		}
		$rows[] = $row;
	}

	require("more.tpl");
