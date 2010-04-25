<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
	"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<title>ishmael - a UI for mk-query-digest</title>
		<link rel="stylesheet" href="media/style.css" type="text/css"/>
		<script src="media/graph.js"></script>
	</head>

	<body>
		<div id="title_bar">
			<h1>ishmael</h1>
			a UI for mk-query-digest
			<form action="" method="GET">
				last <input type="text" name="hours" id="hours" value="<?= $hours ?>" />
				<label for="hours">hours</label>
				on <select name="host">
					<? foreach ($hosts as $h => $t):?>
					<option value="<?=$h?>" <? if ($host == $h):?>SELECTED<? endif; ?>><?= $t ?></option>
					<? endforeach; ?>
				</select>
				<? foreach ($_GET as $key => $value): ?>
					<? if (!in_array($key, array('host', 'hours'))): ?>
					<input type="hidden" name="<?= $key ?>" value="<?= $value ?>">
					<? endif; ?>
				<? endforeach; ?>
				<input type="submit" value="go">
			</form>
		</div>
		<div id="content_wrapper">