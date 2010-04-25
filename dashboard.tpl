<?php include("top.tpl"); ?>
		<h2>Dashboard for <?= $host_conf['title'] ?></h2>
		<h3>Average Slow Query Time</h3>
		<div class="graph_container">
			<canvas id="dashboard_graph"></canvas>
		</div>
		 <script type="text/javascript">
			var graph = new YAHOO.Smb.Graph('dashboard_graph', {
				start: 0,
				width: 700,
				height: 200,
				end: <?= count($historical_data) ?>,
				type: 'bar',
				hideYAxis: true,
				hideXAxis: true,
				enableHoverInfo: true
			});
			var data = [
			<? foreach ($historical_data as $row): ?>
				{ date: '<?= $row['period']?>', avg_query_time: <?=$row['avg_time']?> },
			<? endforeach; ?>
			];
			graph.addDataSet(data, { y: 'avg_query_time', xLabel: 'date', yLabel: 'avg_query_time', color: '#444444' });
			graph.render();
		</script>
		<h3>Slow Query Overview</h3>
		<table>
			<tr>
				<th>Rank</th>
				<th <? if ($sort=='time'):?>class="sorted"<? endif ?>><a href="<?= $sort_time_url ?>">% time</a></th>
				<th <? if ($sort=='count'):?>class="sorted"<? endif ?>><a href="<?= $sort_count_url ?>">% queries</a></th>
				<th <? if ($sort=='ratio'):?>class="sorted"<? endif ?>><a href="<?= $sort_ratio_url ?>">Ratio</a></th>
				<th>sample query</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
		<? foreach ($rows as $row) :?>
			<tr <? if ($counter++ % 2): ?>class="alt"<? endif ?>>
				<td><?=$counter ?></td>
				<td><? printf("%.2f", $row['time_pct']) ?> <br /> <small>(<?= printf("%.2f", $row['time']) ?>)</small></td>
				<td><? printf("%.2f", $row['qty_pct']) ?> <br /> <small>(<?= $row['count'] ?>)</small></td>
				<td><? printf("%.2f", $row['ratio']) ?></td>
				<td><?=format_query($row['sample']) ?></td>
				<td><a href="<?= $row['explain_url'] ?>">explain</a></td>
				<td><a href="<?= $row['more_url'] ?>">more</a></td>
			</tr>
		<? endforeach ?>
		</table>
<?php include("bottom.tpl"); ?>