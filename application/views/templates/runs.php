	<h1>Runs</h1>
        <div>
            <?php 
                $distance_total = 0;
		foreach ($runs as $r) {
                    $distance_total += $r["miles"];
                }
                echo "Total distance (miles): " . $distance_total;
            ?>
        </div>
		<table cellpadding="0" cellspacing="0" border="0" class="tablesorter table table-striped table-bordered" id="runsTable">
			<thead> 
				<tr> 
					<th>Date</th>
					<th>City</th>
					<th>State</th>
					<th>Country</th>
					<th>Time</th>
					<th>Distance (miles)</th>
					<th>Pace</th>
				</tr>
			<thead> 
			<tbody> 
			<?php 
			foreach ($runs as $r) {
				echo "<tr>\n";
				echo "\t<td><a href=\"" . RELATIVEPATH . "run/" . $r["runid"] . "/\">" . $r["date"] . "</a></td>\n";
				echo "\t<td>" . $r["city"] . "</td>\n";
				echo "\t<td>" . $r["statename"] . "</td>\n";
				echo "\t<td>" . $r["country"] . "</td>\n";
				echo "\t<td>" . $r["runtime"] . "</td>\n";
				echo "\t<td>" . $r["miles"] . "</td>\n";
				echo "\t<td>" . $r["pacetime"] . "</td>\n";
				echo "</tr>\n";
			}
			?>
			</tbody>
		</table>
