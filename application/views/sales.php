<div class="row" ng-app="main" ng-cloak ng-controller="manageSales" ng-init="currencyCode = '<?=$user_details->currency_code?>'">
	<main class="w-100 text-center col-12">
		<h2 class="header-text">Sales <i class="fas fa-cart-arrow-down"></i></h2>
		<div class="box-shadow p-0 pt-0 container">
			<ul class="nav nav-tabs nav-justified tabs rounded-0">
				<li class="nav-item">
				    <a class="nav-link active" data-toggle="tab" href="#view-products"><i class="fas fa-shopping-cart"></i> <span class="d-none d-sm-initial">Products</span></a>
				</li>
				<li class="nav-item">
				    <a class="nav-link d-flex justify-content-center align-items-center" data-toggle="tab" href="#shopping-cart"><span><i class="fas fa-cart-plus"></i> <span class="d-none d-sm-initial">Cart</span></span> <sup id="cart-items" ng-show="cartItems.length" class="badge badge-info font-sm ml-1">{{cartItems.length}}</sup></a>
				</li>
				<?php if($this->session->userdata('userdata') !== null && (int)$this->session->userdata('userdata')['level'] > 1){?>
					<li class="nav-item">
					    <a class="nav-link" data-toggle="tab" href="#manage-purchases"><i class="fas fa-toolbox"></i> <span class="d-none d-sm-initial">Manage</span></a>
					</li>
				<?php }?>
			</ul>
			<div class="tab-content limit-tab-content-lower mb-3">
				<div class="tab-pane p-1 p-sm-3 active" id="view-products">
					<view-products url="<?=base_url('pointofsale/get_products_for_sale')?>" type="sale"></view-products>
				</div>
				<div class="tab-pane p-3 fade" id="shopping-cart">
					<view-cart type="sale"></view-cart>
				</div>
				<?php if($this->session->userdata('userdata') !== null && (int)$this->session->userdata('userdata')['level'] > 1){?>
					<div class="tab-pane p-3 fade" id="manage-purchases">
						<?=$this->load->view('manage_sales',array(),true)?>
					</div>
				<?php }?>
			</div>
		</div>
	</main>
	<div class="modal fade text-muted" id="add-to-cart">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="header-text"><i class="fas fa-cart-plus"></i> {{updateCart && 'Update Cart' || 'Add to Cart'}}</h4>
					<button class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<form autocomplete="off" ng-submit="addToCart($event)">
						<div class="row">
							<div class="col-12">
								<div class="toast p-0 toast-max-width hide fade form-errors"><div class="toast-body alert alert-danger mb-0"></div></div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text"><i class="fas fa-shopping-cart"></i></div>
										</div>
										<input type="text" name="item1" ng-model="inputFields.item1.product" class="form-control" placeholder="Product" readonly>
									</div>
									<span class="helper-text text-left" data-original="Product Name is Auto-Filled from your Selection."></span>
								</div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text"><i class="fas fa-file-invoice-dollar"></i></div>
										</div>
										<input type="text" name="item3" ng-readonly="true" ng-model="inputFields.item3" class="form-control" placeholder="Total Cost (after discount)">
									</div>
									<span class="helper-text text-left" data-original="Total Price is Auto-Calculated from your Selection"></span>
								</div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text"><i class="fas fa-list-ol"></i></div>
										</div>
										<input type="text" name="item2" ng-model="inputFields.item2" class="form-control" placeholder="Quantity">
									</div>
									<span class="helper-text text-left" data-original="Quantity e.g 3"></span>
								</div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text"><i class="fas fa-dollar-sign"></i></div>
										</div>
										<input type="text" name="item4" ng-model="inputFields.item4" class="form-control" placeholder="Discount">
									</div>
									<span class="helper-text text-left" data-original="Discount Allowed e.g 11"></span>
								</div>
								<div class="form-group text-right">
									<button type="submit" class="btn btn-info"><i class="fas fa-cart-plus"></i> {{updateCart && 'Update Cart' || 'Add to Cart'}}</button>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade text-muted" id="checkout-modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="header-text"><i class="fas fa-cart-plus"></i> Checkout</h4>
					<button class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<form autocomplete="off" ng-submit="checkout($event)">
						<div class="row">
							<div class="col-12">
								<div class="toast p-0 toast-max-width hide fade form-errors"><div class="toast-body alert alert-danger mb-0"></div></div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text"><i class="fas fa-file-invoice-dollar"></i></div>
										</div>
										<input type="text" name="item1" ng-model="checkoutFields.item1" class="form-control" placeholder="Total Cost" readonly>
									</div>
									<span class="helper-text text-left" data-original="Total Cost of Items in the Cart!"></span>
								</div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text"><i class="fas fa-wallet"></i></div>
										</div>
										<input type="text" name="item2" ng-model="checkoutFields.item2" class="form-control" placeholder="Amount Paid">
									</div>
									<span class="helper-text text-left" data-original="Amount Paid for Items in Cart!"></span>
								</div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text"><i class="fas fa-coins"></i></div>
										</div>
										<input type="text" name="item3" ng-model="checkoutFields.item3" class="form-control" placeholder="Balance" readonly>
									</div>
									<span class="helper-text text-left" data-original="Balance After Payment!"></span>
								</div>
								<?php if($payment_methods):?>
									<div class="form-group">
										<div class="input-group">
											<div class="input-group-prepend">
												<div class="input-group-text"><i class="fas fa-credit-card"></i></div>						
											</div>
											<select ng-model="checkoutFields.item4" class="form-control" name="item4">
												<?php foreach($payment_methods as $method):?>
													<option value="<?=$method->method_id?>"><?=ucwords($method->method)?></option>
												<?php endforeach;?>
											</select>
										</div>
										<span class="helper-text text-left" data-original="Method Used to Pay for the Items."></span>
									</div>
								<?php endif;?>
								<div class="form-group text-right">
									<button type="submit" class="btn btn-info"><i class="fas fa-sign-out-alt"></i> Checkout</button>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="<?=base_url('assets/js/sales.js')?>"></script>