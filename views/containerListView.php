<div class="panel panel-default">
<div class="panel-heading">Containers</div>
<table class="table table-striped">
	<thead>
		<tr>
			<th>#</th>
			<th class="hidden-sm hidden-xs">ID</th>
			<th class="hidden-sm hidden-xs">Image</th>
			<th>Name</th>
			<th>IP</th>
			<th>Up/Downtime</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
<?php
for($i=0;$i<sizeof($getContainers);$i++)
		{
			echo '<tr class="'.$getContainers[$i]->Isrunning.'">';
			echo '<td>'.($i+1).'</td>';
			echo '<td class="hidden-sm hidden-xs">'.substr($getContainers[$i]->ID,0,7).'</td>';
			echo '<td class="hidden-sm hidden-xs">'.$getContainers[$i]->Image.'</td>';
			echo '<td><strong>'.$getContainers[$i]->Hostname.'</strong></td>';
			echo '<td>'.$getContainers[$i]->IP.'</td>';
			echo '<td>'.$getContainers[$i]->Uptime.'</td>';
			echo '<td><a href="index.php?containerID='.$getContainers[$i]->ID.'"><button type="button" class="btn btn-primary btn-xs">Manage</button></a></td>';
			//echo '<td><a href="index.php?containerID='.$getContainers[$i]->ID.'"><button type="button" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-play"></span></button></a></td>';

			echo '</tr>';
		}

?>
	</tbody>
</table>
</div>