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
		'sum' => 0, 
		'min' => 0,
		'max' => 0,
		'pct_95' => array(),
		'stddev' => 0,
		'median' => array(),
	);

	$histo = array();

	foreach ($fields as $field) {
		$histo[$field] = $points;
	}

	$qcount = 0;
	$rcount = 0;
	
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
		foreach ($fields as $field) {
			foreach (array_keys($points) as $point) {
				if ($point == "sum") {
					$histo[$field][$point] += $row["{$field}_{$point}"];
				} elseif ($point == "min") {
					if ($histo[$field][$point] == 0 || $histo[$field][$point] > $row["{$field}_{$point}"]) {
						$histo[$field][$point] = $row["{$field}_{$point}"];
					}
				} elseif ($point == "max") {
					if ($histo[$field][$point] < $row["{$field}_{$point}"]) {
						$histo[$field][$point] = $row["{$field}_{$point}"];
					}
				} elseif ($point == "stddev") {
					$histo[$field][$point] += pow($row["{$field}_{$point}"], 2);
					$rcount++;
				} else {
					$histo[$field][$point][$row['ts_cnt']] = $row["{$field}_{$point}"];
				}
			}
		}
		$qcount += $row['ts_cnt'];
		$rows[] = $row;
	}

	foreach ($fields as $field) {
		$stddev = sqrt($histo[$field]["stddev"] / $rcount);
		$histo[$field]["stddev"] = $stddev;

		# Find the weighted median of 95th percentiles across all samples in period */
		# For pct_95 and median, we've bult an array containing count_per_sample => time.
		# We sort this by value [ 42 => '.00123', 542 => '.0123', 314 => '.123', 7 => '1.230' ]
		# The middle of the sum of sorted keys is the overall median
		foreach (array('pct_95', 'median') as $f) {
			$i = 0;
			$median = 0;
			asort ($histo[$field][$f], SORT_NUMERIC);
			$offset = round(array_sum(array_keys($histo[$field][$f])) * 0.5);
			foreach ($histo[$field][$f] as $cnt => $v) {
				$i += $cnt;
				if ($i >= $offset) {
					$median = $v;
					break;
				}
			}
			$histo[$field][$f] = $median;
		}
	}

	require("more.tpl");
