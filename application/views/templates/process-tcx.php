	<h1>Runs</h1>
		<table cellpadding="0" cellspacing="0" border="0" class="tablesorter table table-striped table-bordered" id="runsTable">
			<thead> 
				<tr> 
					<th>ID</th>
					<th>Filesize</th>
					<th>Filename</th>
                                        <th> </th>
				</tr>
			<thead> 
			<tbody> 
			<?php 
			foreach ($tcxfiles as $r) {
				echo "<tr>\n";
				echo "\t<td>" . $r["id"] . "</td>\n";
				echo "\t<td>" . $r["filesize"] . "</td>\n";
				echo "\t<td>" . $r["filename"] . "</td>\n";
                                echo "\t<td><a href=\"" . RELATIVEPATH . "run/" . $r["id"] . "/load\">Load</a></td>\n";
				echo "</tr>\n";
			}
			?>
			</tbody>
		</table>
