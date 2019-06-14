<div class="row mb-3" ng-app="main" ng-cloak ng-controller="manageProducts" ng-init="currencyCode = '<?=$user_details->currency_code?>'">
	<main class="w-100 text-center col-12">
		<h2 class="header-text">Manage Products <i class="fas fa-shopping-cart"></i></h2>
		<div class="mt-4">
			<table datatable="" class="table table-striped table-hover w-100 data-table header-text" dt-options="tableOptions" dt-columns="tableColumns" dt-instance="tableInstance"></table>
		</div>
		<div ng-show="showFooter" class="mt-3 text-center box-shadow-inline">
			<a ng-href="{{printHref}}" ng-click="showPrintModal('#print-details')" target="print" class="btn btn-info mr-3"><i class="fas fa-print"></i> Print</a>
			<a ng-href="{{excelHref}}" class="btn btn-success"><i class="fas fa-file-csv"></i> Excel</a>
		</div>
	</main>
	<div class="modal fade text-dark" id="add-item">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="text-muted"><i class="fas fa-plus-square"></i> New Product</h4>
					<button class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<form autocomplete="off" ng-submit="addItem($event)">
						<div class="row">
							<div class="col-12">
								<div class="toast p-0 toast-max-width hide fade form-errors"><div class="toast-body alert alert-danger mb-0"></div></div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text"><i class="fas fa-shopping-bag"></i></div>
										</div>
										<input type="text" name="item1" ng-model="inputFields.item1" class="form-control" placeholder="Product Name">
									</div>
									<span class="helper-text text-left" data-original="e.g Computer"></span>
								</div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text"><i class="fas fa-money-bill-wave"></i></div>
										</div>
										<input type="text" name="item2" ng-model="inputFields.item2" class="form-control" placeholder="Cost Per Unit">
									</div>
									<span class="helper-text text-left" data-original="e.g 24"></span>
								</div>
								<?php if($categories){?>
									<div class="form-group">
									    <div class="input-group">
									    	<div class="input-group-prepend">
												<div class="input-group-text"><i class="fas fa-shopping-basket"></i></div>
											</div>
										    <select class="form-control" ng-model="inputFields.item3" name="item3">
										    	<option value="0" disabled>Category</option>
										    	<?php foreach($categories as $category){?>
										    		<option value="<?=$category->category_id?>"><?=ucwords($category->category_name)?></option>
										    	<?php }?>
										    </select>
									    </div>
									    <span class="helper-text text-left" data-original="Select a Category For This Product!"></span>
									</div>
								<?php }?>
								<div class="form-group text-right">
									<button type="submit" class="btn btn-info width-100"><i class="fas fa-pen"></i> Add</button>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade text-dark" id="edit-item">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="text-muted"><i class="fas fa-cog fa-spin"></i> Modify Product Details</h4>
					<button class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<form autocomplete="off" ng-submit="updateItem($event)">
						<div class="row">
							<div class="col-12">
								<div class="toast p-0 toast-max-width hide fade form-errors"><div class="toast-body alert alert-danger mb-0"></div></div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text"><i class="fas fa-shopping-bag"></i></div>
										</div>
										<input type="text" name="item1" ng-model="updateFields.item1" class="form-control" placeholder="Product Name">
									</div>
									<span class="helper-text text-left" data-original="e.g Computer"></span>
								</div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text"><i class="fas fa-money-bill-wave"></i></div>
										</div>
										<input type="text" name="item2" ng-model="updateFields.item2" class="form-control" placeholder="Cost Per Unit">
									</div>
									<span class="helper-text text-left" data-original="e.g 24"></span>
								</div>
								<?php if($categories){?>
									<div class="form-group">
									    <div class="input-group">
									    	<div class="input-group-prepend">
												<div class="input-group-text"><i class="fas fa-shopping-basket"></i></div>
											</div>
										    <select class="form-control" ng-model="updateFields.item3" name="item3">
										    	<option value="0" disabled>Category</option>
										    	<?php foreach($categories as $category){?>
										    		<option value="<?=$category->category_id?>"><?=ucwords($category->category_name)?></option>
										    	<?php }?>
										    </select>
									    </div>
									    <span class="helper-text text-left" data-original="Select a Category For This Product!"></span>
									</div>
								<?php }?>
								<div class="form-group row">
									<div class="col-12 col-sm-6">
										<button ng-show="showButton.update" type="submit" class="btn btn-info update mt-3 btn-block"><i class="fas fa-edit"></i> Update</button>
									</div>
									<div class="col-12 col-sm-6">
										<button ng-show="showButton.disable && showButton.all" ng-click="deactivateItem($event)" type="button" class="btn btn-danger mt-3 btn-block"><i class="fas fa-times"></i> Disable</button>
										<button ng-show="showButton.enable && showButton.all" ng-click="reactivateItem($event)" type="button" class="btn btn-success mt-3 btn-block"><i class="fas fa-check"></i> Enable</button>
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
					<h4 class="text-muted"><i class="fas fa-print"></i> Print Product Details</h4>
					<button class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body p-1 h-80">
					<iframe class="w-100 h-100" src="" name="print"></iframe>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="<?=base_url('assets/js/manage-products.js')?>"></script>