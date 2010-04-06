<?php include("top.tpl"); ?>
		<h3>last report</h3>
		<table>
			<tr>
				<th>&nbsp;</th>
				<? foreach($points as $point): ?>
				<th><?= $point ?></th>
				<? endforeach; ?>
			<tr>
			<? foreach($fields as $field): ?>
			<tr>
				<td><?= $field ?></td>
				<? foreach($points as $point): $col = "{$field}_{$point}"; ?>
				<td><?= $rows[0][$col]?></td>
				<? endforeach; ?>
			</tr>
			<? endforeach; ?>
		</table>
		<table>
			<tr>
				<th>Timestamp</th>
				<th>Total Query time</th>
				<th>Query</th>
			</tr>
			<? foreach ($rows as $row): ?>
			<tr <? if ($counter++ % 2): ?>class="alt"<? endif ?>>
				<td><?= $row['ts_max']?></td>
				<td><?= $row['Query_time_sum']?></td>
				<td><?= format_query($row['sample']) ?></td>
			</tr>
			<? endforeach?>
		</table>
<?php include("bottom.tpl"); ?>