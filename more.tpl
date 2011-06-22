<?php include("top.tpl"); ?>
		<h3>Last Report</h3>
		<p>
			<?= format_query($rows[0]['sample']) ?>
		</p>
		<table>
			<tr>
				<th>&nbsp;</th>
				<? foreach($points as $point): ?>
				<th><?= $point ?></th>
				<? endforeach; ?>
			</tr>
			<? $data_counter = 1; ?>
			<tr>
				<th class="horiz">Query count</th><td><?= $rows[0]['ts_cnt']?></td>
			</tr>
			<? foreach($fields as $field): ?>
			<tr <? if ($data_counter++ % 2): ?>class="alt"<? endif ?>>
				<th class="horiz"><?= $field ?></th>
				<? foreach($points as $point): $col = "{$field}_{$point}"; ?>
				<td><?= $rows[0][$col]?></td>
				<? endforeach; ?>
			</tr>
			<? endforeach; ?>
		</table>
		<p>
		<div class="graph_container">
			<table>
			<tr><th>Queries</th></tr><tr><td>
			<canvas id="stats"></canvas>
			</td></tr><tr><th>Time</th></tr><tr><td>
			<canvas id="time"></canvas>
			</td></tr></table>
		</div>
		</p>
		 <script type="text/javascript">
			var graph = new YAHOO.Smb.Graph('stats', {
				start: 0,
				width: 700,
				height: 200,
				end: <?= count($rows)?>,
				type: 'bar',
				hideYAxis: true,
				hideXAxis: true,
				enableHoverInfo: true,
			});
			var data = [
			<? foreach ($rows as $row): ?>
				{ date: '<?= $row['ts_max']?>', count: <?=$row['ts_cnt']?> },
			<? endforeach; ?>
			];
			graph.addDataSet(data, { y: 'count', xLabel: 'date', yLabel: 'count', color: '#0063cd' });
			graph.render();
		</script>
		 <script type="text/javascript">
			var graph = new YAHOO.Smb.Graph('time', {
				start: 0,
				width: 700,
				height: 200,
				end: <?= count($rows)?>,
				type: 'bar',
				hideYAxis: true,
				hideXAxis: true,
				enableHoverInfo: true
			});
			var data = [
			<? foreach ($rows as $row): ?>
				{ date: '<?= $row['ts_max']?>', time: <?=$row['Query_time_sum']/$row['ts_cnt']?> },
			<? endforeach; ?>
			];
			graph.addDataSet(data, { y: 'time', xLabel: 'date', yLabel: 'time', color: '#ff0084' });
			graph.render();
		</script>
		<h3>Previous Reports</h3>
		<table>
			<tr>
				<th>Timestamp</th>
				<th>Count</th>
				<th>Avg time (ms)</th>
				<th>Query</th>
			</tr>
			<? foreach (array_reverse($rows) as $row): ?>
			<tr <? if ($counter++ % 2): ?>class="alt"<? endif ?>>
				<td><?= $row['ts_max']?></td>
				<td><?= $row['ts_cnt']?></td>
				<td><?= round($row['Query_time_sum']/$row['ts_cnt']*1000,2)?></td>
				<td><?= format_query($row['sample']) ?></td>
			</tr>
			<? endforeach?>
		</table>
<?php include("bottom.tpl"); ?>
