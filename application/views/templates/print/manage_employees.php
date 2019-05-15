<div class="p-2 table-responsive">
	<table class="table table-bordered table-striped-even header-text" align="center">
		<caption class="font-sm ml-3">List of Employees generated by Point Of Sale!</caption>
		<thead class="thead-light">
			<tr>
				<th>Full Name</th>
				<th>Department</th>
				<th>Email</th>
				<th>Status</th>
				<th>Last Interaction</th>
			</tr>
		</thead>
		<tbody class="tbody">
			<?php foreach($data as $info){?>
				<tr>
					<td><?=ucwords("$info->full_name")?></td>
					<td><?=ucwords("$info->department")?></td>
					<td><?=$info->email?></td>
					<td><?=$info->status?></td>
					<td><?=$this->time->format_date($info->last_access_time,"d M, Y &#8226 h:iA")?></td>
				</tr>
			<?php }?>
		</tbody>
	</table>
</div>