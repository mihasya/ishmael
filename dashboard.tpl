<?php include("top.tpl"); ?>
		<table>
			<tr>
				<th>Rank</th>
				<th><a href="<?= $sort_time_url ?>">% of time</a></th>
				<th><a href="<?= $sort_count_url ?>">% of queries</a></th>
				<th><a href="<?= $sort_ratio_url ?>">Ratio</a></th>
				<th>sample query</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
		<? foreach ($rows as $row) :?>
			<tr <? if ($counter++ % 2): ?>class="alt"<? endif ?>>
				<td><?=$counter ?></td>
				<td><? printf("%.2f", $row['time_pct']) ?></td>
				<td><? printf("%.2f", $row['qty_pct']) ?></td>
				<td><? printf("%.2f", $row['ratio']) ?></td>
				<td><?=format_query($row['sample']) ?></td>
				<td><a href="<?= $row['explain_url'] ?>">explain</a></td>
				<td><a href="<?= $row['more_url'] ?>">more</a></td>
			</tr>
		<? endforeach ?>
		</table>
<?php include("bottom.tpl"); ?>