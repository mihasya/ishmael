<?php include("top.tpl"); ?>
		<h3>EXPLAIN result</h3>
		<p>
			<?= format_query($query) ?>
		</p>
		<? if (count($rows)) { ?>
		<table>
			<tr>
				<? foreach ($rows[0] as $key => $value): ?>
				<th><?= $key?></th>
				<? endforeach; ?>
			</tr>
			<? foreach ($rows as $row): ?>
			<tr <? if ($counter++ % 2): ?>class="alt"<? endif ?>>
				<? foreach ($row as $key => $value): ?>
				<td><?= $value ?></td>
				<? endforeach; ?>
			</tr>
			<? endforeach; ?>
		</table>
		<? } ?>
		<? if (count($tables)) { ?>
		<h3>Tables</h3>
		<table>
			<tr>
				<th>Table</th>
				<th>CREATE TABLE</th>
			</tr>
			<? foreach ($tables as $table => $info): ?>
			<tr <? if ($table_counter++ % 2): ?>class="alt"<? endif ?>>
				<td><?= $table ?></td>
				<td><?= nl2br($info['Create Table']) ?></td>
			</tr>
			<? endforeach; ?>
		</table>
		<? } ?>
<?php include("bottom.tpl"); ?>
