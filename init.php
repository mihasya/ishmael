<?php
	require_once('conf.php');

	function _format_query_callback($str) {
		$fragment_class = "keyword";
		if (preg_match("/^\s*\/\*.*?\*\/\s*$/", $str[0])) {
			$fragment_class = "comment";
		}
		return "<span class=\"{$fragment_class}\">" . $str[0] . "</span>";
	}

	function format_query($query) {
		$pattern = "/(?i)\b((\/\*.*?\*\/)|SELECT|FROM|WHERE|(INNER|OUTER|STRAIGHT|RIGHT|LEFT(?:\s?))?JOIN|AND|OR|ORDER BY|GROUP BY|DESC|FORCE INDEX|USE INDEX|BETWEEN|IN)\b/";
		$query = preg_replace_callback($pattern, '_format_query_callback', $query);
		return "<span class=\"query\">{$query}</span>";
	}


	mysql_connect($conf['db_host'],$conf['db_user'],$conf['db_password']);
	@mysql_select_db($conf['db_database_mk']) or die("Unable to select database");
