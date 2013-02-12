<?php include("top.tpl"); ?>
		<p>
		</p>
		<h3>More queries on table <?= $_GET['table'] ?></h3>
		<table>
			<tr>
				<th>Count</th>
				<th>Query</th>
				<? if ($conf['explain']): ?>
				<th>Explain</th>
				<? else: ?>
				<th>More</th>
				<? endif ?>
			</tr>
			<? foreach ($rows as $row): ?>
			<tr <? if ($counter++ % 2): ?>class="alt"<? endif ?>>
				<td><?= $row['count']?></td>
				<td><?=format_query($row['query']) ?></td>
				<? if ($conf['explain']): ?>
				<td><a href="<?= $row['explain_url'] ?>">explain</a></td>
				<? else: ?>
				<td><a href="<?= $row['more_url'] ?>">more</a></td>
				<? endif ?>
			</tr>
			<? endforeach?>
		</table>
<?php include("bottom.tpl"); ?>
