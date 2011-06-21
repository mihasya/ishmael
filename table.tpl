<?php include("top.tpl"); ?>
		<p>
		</p>
		<h3>More queries on table <?= $_GET['table'] ?></h3>
		<table>
			<tr>
				<th>Count</th>
				<th>Query</th>
				<th>Explain</th>
			</tr>
			<? foreach ($rows as $row): ?>
			<tr <? if ($counter++ % 2): ?>class="alt"<? endif ?>>
				<td><?= $row['count']?></td>
				<td><?=format_query($row['query']) ?></td>
				<td><a href="<?= $row['explain_url'] ?>">explain</a></td>
			</tr>
			<? endforeach?>
		</table>
<?php include("bottom.tpl"); ?>
