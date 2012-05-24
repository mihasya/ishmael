<?php
	#
	# 'more' UI - additional info about a particular query
	#

	require_once('init.php');

	$checksum = mysql_real_escape_string($_GET['checksum']);

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
			if (preg_match('@/\* (\S+)(::\S+)?.*\*/@', $row['sample'], $matches)) {
				$c = ($matches[2]) ? $matches[1] . $matches[2] : $matches[1];
				$row['sample'] = '/* ' . $c . ' */ ' . $row['fingerprint'];
			} else {
				$row['sample'] = $row['fingerprint'];
			}
		}
		$rows[] = $row;
	}

	require("more.tpl");
