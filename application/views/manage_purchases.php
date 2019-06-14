<div class="mt-4">
	<table datatable="" class="table table-striped table-hover w-100 data-table header-text" width="100%" dt-options="tableOptions" dt-columns="tableColumns" dt-instance="tableInstance"></table>
</div>
<div ng-show="showFooter" class="mt-3 text-center">
	<a ng-href="{{printHref}}" ng-click="showPrintModal('#print-details')" target="print" class="btn btn-info mr-3"><i class="fas fa-print"></i> Print</a>
	<a ng-href="{{excelHref}}" class="btn btn-success"><i class="fas fa-file-csv"></i> Excel</a>
</div>
<div class="modal fade text-dark" id="edit-item">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="text-muted"><i class="fas fa-cog fa-spin"></i> Modify Purchase Details</h4>
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
										<div class="input-group-text"><i class="fas fa-shopping-cart"></i></div>
									</div>
									<?php if($products):?>
										<select name="item1" ng-model="updateFields.item1" class="form-control">
											<option value="0" disabled>Select Product</option>
											<?php foreach($products as $product):?>
												<option value="<?=$product->product_id?>"><?=ucwords($product->product)?></option>
											<?php endforeach;?>
										</select>
									<?php endif;?>
								</div>
								<span class="helper-text text-left" data-original="Product Purchased"></span>
							</div>
							<div class="form-group">
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text"><i class="fas fa-list-ol"></i></div>
									</div>
									<input type="text" name="item2" ng-model="updateFields.item2" class="form-control" placeholder="Quantity">
								</div>
								<span class="helper-text text-left" data-original="Quantity of Product Purchased"></span>
							</div>
							<div class="form-group">
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text"><i class="fas fa-file-invoice-dollar"></i></div>
									</div>
									<input type="text" name="item3" ng-model="updateFields.item3" class="form-control" placeholder="Total Cost (after discount)">
								</div>
								<span class="helper-text text-left" data-original="Actual Amount Paid to Supplier"></span>
							</div>
							<div class="form-group">
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text"><i class="fas fa-dollar-sign"></i></div>
									</div>
									<input type="text" name="item4" ng-model="updateFields.item4" class="form-control" placeholder="Discount">
								</div>
								<span class="helper-text text-left" data-original="Discount Received from Supplier"></span>
							</div>
							<?php if($payment_methods):?>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text"><i class="fas fa-credit-card"></i></div>						
										</div>
										<select ng-model="updateFields.item5" class="form-control" name="item5">
											<option value="0" disabled>Select Payment Method</option>
											<?php foreach($payment_methods as $method):?>
												<option value="<?=$method->method_id?>"><?=ucwords($method->method)?></option>
											<?php endforeach;?>
										</select>
									</div>
									<span class="helper-text text-left" data-original="Method Used to Pay for the Items."></span>
								</div>
							<?php endif;?>
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
				<h4 class="text-muted"><i class="fas fa-print"></i> Print Purchase Details</h4>
				<button class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body p-1 h-80">
				<iframe class="w-100 h-100" src="" name="print"></iframe>
			</div>
		</div>
	</div>
</div>