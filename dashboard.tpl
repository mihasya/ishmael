<?php include("top.tpl"); ?>
		<table>
			<tr>
				<th>Rank</th>
				<th><a href="?sort=time">% of time</a></th>
				<th><a href="?sort=count">% of queries</a></th>
				<th><a href="?sort=ratio">Ratio</a></th>
				<th>sample query</th>
				<th>&nbsp;</th>
		<? foreach ($rows as $row) :?>
			<tr <? if ($counter++ % 2): ?>class="alt"<? endif ?>>
				<td><?=$counter ?></td>
				<td><? printf("%.2f", $row['time_pct']) ?></td>
				<td><? printf("%.2f", $row['qty_pct']) ?></td>
				<td><? printf("%.2f", $row['ratio']) ?></td>
				<td><span class="query"><?=format_query($row['sample']) ?></span></td>
				<td><a href="explain.php?checksum=<?=$row['checksum']?>">explain</a></td>
			</tr>
		<? endforeach ?>
		</table>
<?php include("bottom.tpl"); ?>