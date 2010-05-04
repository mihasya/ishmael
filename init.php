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

	# get the list of hosts this ishmael install is configured to look at
	function ish_get_host_list() {
		global $conf;
		$host_list = array();
		foreach (array_keys($conf['hosts']) as $host) {
			$host_config = ish_get_host_config($host);
			$host_list[$host] = $host_config['title'];
		}
		return $host_list;
	}

	# merges the config for a particular host on top of the defaults
	function ish_get_host_config($host) {
		global $conf;
		$defaults = $conf;
		unset($defaults['hosts']);
		$host_config['db_host'] = $host;
		$host_config = array_merge($defaults, $conf['hosts'][$host]);
		$host_config['title'] = $host_config['label']
			? "{$host_config['label']} - {$host_config['db_host']}"
			: $host_config['db_host'];
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
	$host = $_GET['host'] ? $_GET['host'] : reset(array_keys($hosts));
	$host_conf = ish_get_host_config($host);

	# what timeframe we want to look at
	$hours = $_GET['hours'] ? $_GET['hours'] : 24;

	mysql_connect($host_conf['db_host'],$host_conf['db_user'],$host_conf['db_password']);
	@mysql_select_db($host_conf['db_database_mk']) or die("Unable to select database");
