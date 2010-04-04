<?php include("top.tpl"); ?>
	<table>
		<p>
			<span class="query"><?= format_query($query) ?>
		</p>
		<table>
			<tr>
				<? foreach ($rows[0] as $key => $value): ?>
				<th><?= $key?></th>
				<? endforeach; ?>
			</tr>
			<? foreach ($rows as $row): ?>
				<? foreach ($row as $key => $value): ?>
				<td><?= $value ?></td>
				<? endforeach; ?>
			<? endforeach; ?>
		</table>
	</table>
<?php include("bottom.tpl"); ?>