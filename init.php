<?php
	require_once('conf.php');

function format_xaxis($hours){
	if ($hours < 72){
		return round(900/$hours * 1,0);
	}
	else{
		return round(900/$hours * 24,0);
	}
}

function format_xskip($hours){
	if ($hours < 72){
		return round(90/(900/$hours),0);
	}
	else{
		return round(90/(900*24/$hours),0);
	}
}
	function _format_query_callback($str) {
		$fragment_class = "keyword";
		if (preg_match("/^\s*\/\*.*?\*\/\s*$/", $str[0])) {
			$fragment_class = "comment";
		}
		return "<span class=\"{$fragment_class}\">" . $str[0] . "</span>";
	}

	function format_query($query) {
		$pattern = "/(?i)(\/\*.*?\*\/)|(\b(SELECT|INSERT|UPDATE|DELETE|FROM|WHERE|(INNER|OUTER|STRAIGHT|RIGHT|LEFT(?:\s?))?JOIN|ORDER BY|GROUP BY|DESC|FORCE INDEX|USE INDEX|BETWEEN|USING|AND|IN|OR|AS|ON)\b)/";
		$query = preg_replace_callback($pattern, '_format_query_callback', $query);

		$query = preg_replace("/([\w'\)])\s*,/", "$1, ", $query);

		if (strlen($query) > 2000) {
			$all = substr($query, 300);
			$query = substr($query, 0, 300)
				. '<span class="query-all" style="display:none">'.$all.'</span><span class="query-dot">...</span>'
				. ' (<a href="#" onclick="$(this).siblings().toggle(); return false;">show / hide</a>)';
		}

		return "<span class=\"query\">{$query}</span>";
	}

	function ish_get_central_host_list() {
		global $conf;
		$hosts = array();
		mysql_connect($conf['db_central_host'],$conf['db_user'],$conf['db_password']) or die(sprintf("Unable to connect to MySQL server: %s", mysql_error()));
		mysql_select_db($conf['db_database_mk']) or die(sprintf("Unable to select database: %s", mysql_error()));
		# get list of host review tables
		$q = 'SHOW TABLES LIKE "' . $conf['db_query_review_table'] . '"';
		$result = mysql_query($q);
		$rows = array();
		while ($row = mysql_fetch_row($result)) {
			preg_match('/^([a-z0-9.-]+)_.*/', $row[0], $match);
			if ($match[1]) {
				$host = $match[1];
				$hosts[$host] = $host;
			}
		}
		return $hosts;
	}

	function ish_get_central_host_config($host) {
		global $conf;
		$host_config = $conf;
		$host_config['db_host'] = $conf['db_central_host'];
		$host_config['title'] = $host;
		$host_config['label'] = $host;
		$host_config['db_query_review_table'] = preg_replace('/%/', $host.'_', $conf['db_query_review_table']);
		$host_config['db_query_review_history_table'] = preg_replace('/%/', $host.'_', $conf['db_query_review_history_table']);
		return($host_config);
	}

	# get the list of hosts this ishmael install is configured to look at
	function ish_get_host_list() {
		global $conf;
		$host_list = array();
		if ($conf['db_central_host']) {
			return ish_get_central_host_list();
		}
		foreach (array_keys($conf['hosts']) as $host) {
			$host_config = ish_get_host_config($host);
			$host_list[$host] = $host_config['title'];
		}
		return $host_list;
	}


	# merges the config for a particular host on top of the defaults
	function ish_get_host_config($host) {
		global $conf;
		if ($conf['db_central_host']) {
			return ish_get_central_host_config($host);
		}
		$defaults = $conf;
		unset($defaults['hosts']);
		$defaults['db_host'] = $host;
		$host_config = array_merge($defaults, $conf['hosts'][$host]);
		$host_config['title'] = $host_config['label']
			? "{$host_config['label']} - {$host_config['db_host']}"
			: $host_config['db_host'];
		if (array_key_exists('port', $host_config)) {
			$host_config['db_host'] .= ':' . $host_config['port'];
		}
		return $host_config;
	}

	# build up a query string using 1. things we need in every URL and 
	# 2. whatever is passed in $args as k-v pairs
	function ish_build_query($args) {
		global $host;
		global $hours;
		$always_need = array(
			'host' => $host,
			'hours' => $hours,
		);
		$final_args = array_merge($always_need, $args);
		return http_build_query($final_args);
	}

	$hosts = ish_get_host_list();

	# which host are we looking at
	$host = $_GET['host'] ? mysql_real_escape_string($_GET['host']) : reset(array_keys($hosts));
	$host_conf = ish_get_host_config($host);

	# what timeframe we want to look at
	$hours = $_GET['hours'] ? mysql_real_escape_string($_GET['hours']) : 24;

	mysql_connect($host_conf['db_host'],$host_conf['db_user'],$host_conf['db_password']) or die(sprintf("Unable to connect to MySQL server: %s", mysql_error()));
	mysql_select_db($host_conf['db_database_mk']) or die(sprintf("Unable to select database: %s", mysql_error()));
