<?php include("top.tpl"); ?>
		<h3>Last Report</h3>
		<p>
			<?= format_query($rows[0]['sample']) ?>
		</p>
		<table>
			<tr>
				<th>&nbsp;</th>
				<? foreach(array_keys($points) as $point): ?>
				<th><?= $point ?></th>
				<? endforeach; ?>
			</tr>
			<? $data_counter = 1; ?>
			<tr>
				<th class="horiz">Query count</th><td><?= $qcount?></td>
			</tr>
			<? foreach($fields as $field): ?>
			<tr <? if ($data_counter++ % 2): ?>class="alt"<? endif ?>>
				<th class="horiz"><?= $field ?></th>
				<? foreach(array_keys($points) as $point): $col = "{$field}_{$point}"; ?>
				<td><?= $histo[$field][$point]?></td>
				<? endforeach; ?>
			</tr>
			<? endforeach; ?>
		</table>
		<p>
		<div class="graph_container">
			<table>
			<tr><th>Queries</th></tr><tr><td>
			<canvas id="stats"></canvas>
			</td></tr><tr><th>Time (avg, pct_95)</th></tr><tr><td>
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
				width: 740,
				height: 220,
				end: <?= count($rows)?>,
				type: 'line',
				enableHoverInfo: true,
                                showYLabels: true,
                                yLabelColor: '#888',
                                xLabelColor: '#888',
                                paddingLeft: 20,
                                xLabelColor: '#888',
                                labelFont: Fonts.Silkscreen,
                                typeLib: BitmapType,
			});
			var data = [
			<? foreach ($rows as $row): ?>
				{ date: '<?= $row['ts_max']?>', pct_95: <?=$row['Query_time_pct_95']?> },
			<? endforeach; ?>
			];
			var data2 = [
			<? foreach ($rows as $row): ?>
				{ date: '<?= $row['ts_max']?>', tavg: <?=$row['Query_time_sum']/$row['ts_cnt']?> },
			<? endforeach; ?>
			];
			graph.addDataSet(data, { y: 'pct_95', xLabel: 'date', yLabel: 'pct_95', color: '#ff0084' });
			graph.addDataSet(data2, { y: 'tavg', xLabel: 'date', yLabel: 'tavg', color: '#aa50d4' });
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
