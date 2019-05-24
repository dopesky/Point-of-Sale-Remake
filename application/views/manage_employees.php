<?php 
$user_id = $this->session->userdata('userdata')['user_id'];
?>

<div class="row" ng-app="main" ng-cloak ng-controller="manageEmployees">
	<main class="w-100 text-center col-12">
		<h2 class="header-text">Manage Employees <i class="fas fa-users-cog"></i></h2>
		<div class="mt-4">
			<table datatable="" class="table table-striped table-hover w-100 data-table header-text" dt-options="tableOptions" dt-columns="tableColumns" dt-instance="tableInstance"></table>
		</div>
		<div ng-show="showFooter" class="mt-3 text-center box-shadow-inline">
			<a href="<?=site_url('owner/print_employee_details/'.$user_id)?>" ng-click="showPrintModal('#print-details')" target="print" class="btn btn-info mr-3"><i class="fas fa-print"></i> Print</a>
			<a href="<?=site_url('owner/download_employee_details_spreadsheet/'.$this->session->userdata('userdata')['user_id'])?>" class="btn btn-success"><i class="fas fa-file-csv"></i> Excel</a>
		</div>
	</main>
	<div class="modal fade text-dark" id="add-employee">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="text-muted"><i class="fas fa-plus-square"></i> New Employee</h4>
					<button class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<form autocomplete="off" ng-submit="addEmployee($event)">
						<div class="row">
							<div class="col-12">
								<div class="toast p-0 toast-max-width hide fade form-errors"><div class="toast-body alert alert-danger mb-0"></div></div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text"><i class="fas fa-user"></i></div>
										</div>
										<input type="text" name="fname" ng-model="inputFields.fname" class="form-control" placeholder="First Name">
									</div>
									<span class="helper-text text-left" data-original="e.g John"></span>
								</div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text"><i class="fas fa-user-tie"></i></div>
										</div>
										<input type="text" name="lname" ng-model="inputFields.lname" class="form-control" placeholder="Last Name">
									</div>
									<span class="helper-text text-left" data-original="e.g Doe"></span>
								</div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text"><i class="fas fa-at"></i></div>
										</div>
										<input type="email" name="email" ng-model="inputFields.email" class="form-control" placeholder="Email">
									</div>
									<span class="helper-text text-left" data-original="e.g dopesky@example.com"></span>
								</div>
								<?php if($departments){?>
									<div class="form-group">
									    <div class="input-group">
									    	<div class="input-group-prepend">
												<div class="input-group-text"><i class="fas fa-tools"></i></div>
											</div>
										    <select class="form-control" ng-model="inputFields.department" name="department">
										    	<option value="0" disabled>Department</option>
										    	<?php foreach($departments as $department){?>
										    		<option value="<?=$department->department_id?>"><?=ucfirst($department->department)?></option>
										    	<?php }?>
										    </select>
									    </div>
									    <span class="helper-text text-left" data-original="Select a Department For This Employee!"></span>
									</div>
								<?php }?>
								<div class="form-group text-right">
									<button type="submit" class="btn btn-info width-100"><i class="fas fa-pen"></i> Employ</button>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade text-dark" id="edit-employee">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="text-muted"><i class="fas fa-cog fa-spin"></i> Modify Employee Details</h4>
					<button class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<form autocomplete="off" ng-submit="updateEmployee($event)">
						<div class="row">
							<div class="col-12">
								<div class="toast p-0 toast-max-width hide fade form-errors"><div class="toast-body alert alert-danger mb-0"></div></div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text"><i class="fas fa-user"></i></div>
										</div>
										<input type="text" name="fname" ng-model="updateFields.fname" class="form-control" placeholder="First Name">
									</div>
									<span class="helper-text text-left" data-original="e.g John"></span>
								</div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text"><i class="fas fa-user-tie"></i></div>
										</div>
										<input type="text" name="lname" ng-model="updateFields.lname" class="form-control" placeholder="Last Name">
									</div>
									<span class="helper-text text-left" data-original="e.g Doe"></span>
								</div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text"><i class="fas fa-at"></i></div>
										</div>
										<input type="email" name="email" ng-model="updateFields.email" class="form-control" placeholder="Email">
									</div>
									<span class="helper-text text-left" data-original="e.g dopesky@example.com"></span>
								</div>
								<?php if($departments){?>
									<div class="form-group">
									    <div class="input-group">
									    	<div class="input-group-prepend">
												<div class="input-group-text"><i class="fas fa-tools"></i></div>
											</div>
										    <select class="form-control" ng-model="updateFields.department" name="department">
										    	<option value="0" disabled>Department</option>
										    	<?php foreach($departments as $department){?>
										    		<option value="<?=$department->department_id?>"><?=ucfirst($department->department)?></option>
										    	<?php }?>
										    </select>
									    </div>
									    <span class="helper-text text-left" data-original="Select a Department For This Employee!"></span>
									</div>
								<?php }?>
								<div class="form-group row">
									<div class="col-12 col-sm-6">
										<button ng-show="showButton.update" type="submit" class="btn btn-info update mt-3 btn-block"><i class="fas fa-edit"></i> Update</button>
									</div>
									<div class="col-12 col-sm-6">
										<button ng-show="showButton.unemploy && showButton.all" ng-click="unemploy($event)" type="button" class="btn btn-danger mt-3 btn-block"><i class="fas fa-user-times"></i> Unemploy</button>
										<button ng-show="showButton.reemploy && showButton.all" ng-click="reemploy($event)" type="button" class="btn btn-success mt-3 btn-block"><i class="fas fa-user-check"></i> Reemploy</button>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade text-dark" id="print-details">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="text-muted"><i class="fas fa-print"></i> Print Employee Details</h4>
					<button class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body p-1 h-80">
					<iframe class="w-100 h-100" src="" name="print"></iframe>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	var user_id = "<?=$user_id?>"
</script>
<script src="<?=base_url('assets/js/manage-employees.js')?>"></script>