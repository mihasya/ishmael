<?php include("top.tpl"); ?>
		<p>
			<?= format_query($query) ?>
		</p>
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
<?php include("bottom.tpl"); ?>