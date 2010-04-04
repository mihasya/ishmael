<?php include("top.tpl"); ?>
		<table>
			<tr>
				<th>Last seen</th>
				<th>Query time</th>
				<th>Query</th>
			</tr>
			<? foreach ($rows as $row): ?>
			<tr>
				<td><?= $row['ts_max']?></td>
				<td><?= $row['query_time_sum']?></td>
				<td><?= format_query($row['sample']) ?></td>
			</tr>
			<? endforeach?>
		</table>
<?php include("bottom.tpl"); ?>