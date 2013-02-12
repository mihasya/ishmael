<?php
        #
        # 'table' UI - search for queries on a given table
        #

        require_once('init.php');

        $table = mysql_real_escape_string($_GET['table']);
	$hours = mysql_real_escape_string($_GET['hours']);

        $q = "SELECT
                        t.sample as query, sum(h.ts_cnt) as count, t.checksum as checksum, t.fingerprint as fingerprint
                 from
                         {$host_conf['db_query_review_table']} as t
                 join
                         {$host_conf['db_query_review_history_table']} as h
                  on 
                        t.checksum=h.checksum 
                where 
                        t.sample like '%{$table}%'
		AND
			h.ts_max > date_sub(now(),interval {$hours} hour) 
                 group by t.checksum order by count desc";

        $result=mysql_query($q);
        $rows = array();
        while ($row = mysql_fetch_assoc($result)) {
		if ($conf['anon']) {
			if (preg_match('@/\* (\S+)(::\S+)?.*\*/@', $row['sample'], $matches)) {
				$c = ($matches[2]) ? $matches[1] . $matches[2] : $matches[1];
				$row['query'] = '/* ' . $c . ' */ ' . $row['fingerprint'];
			} else {
				$row['query'] = $row['fingerprint'];
			}
		}
                $row['explain_url'] = "explain.php?" . ish_build_query(array('checksum'=>$row['checksum']));
		$row['more_url'] = "more.php?" . ish_build_query(array('checksum'=>$row['checksum']));
                $rows[] = $row;
        }

        require("table.tpl");

