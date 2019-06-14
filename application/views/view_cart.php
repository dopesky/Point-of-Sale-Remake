<div class="text-muted mb-3 header-text">
	<h4><u>Summary</u></h4>
	<div class="d-flex flex-seperated justify-content-around align-items-center">
		<b class="text-center text-sm-right">Gross Cost <small>(G)</small></b>
		<span class="flex-box-inline-seperator d-none d-sm-initial"></span>
		<span class="text-center text-sm-left">{{getTotalCostBeforeDiscount()}}</span>
	</div>
	<div class="d-flex flex-seperated justify-content-around align-items-center">
		<b class="text-center text-sm-right">Total Discount <small>(D)</small></b>
		<span class="flex-box-inline-seperator d-none d-sm-initial"></span>
		<span class="text-center text-sm-left">{{getTotalDiscount()}}</span>
	</div>
	<div class="d-flex flex-seperated justify-content-around align-items-center">
		<b class="text-center text-sm-right">Net Cost <small>(G - D)</small></b>
		<span class="flex-box-inline-seperator d-none d-sm-initial"></span>
		<span class="text-center text-sm-left">{{getTotalCostAfterDiscount()}}</span>
	</div>
	<div class="d-flex justify-content-end" ng-if="products.length">
		<button class="btn btn-success text-right" data-toggle="modal" data-target="#checkout-modal" ng-click="checkout()"><i class="fas fa-sign-out-alt"></i> Checkout</button>
	</div>
</div>
<div class="table-responsive">
	<table datatable="ng" class="table table-striped w-100 table-hover data-table header-text" width="100%" dt-instance="tableInstance">
		<thead>
			<tr>
				<th>Product</th>
				<th>Quantity</th>
				<th>Total Cost <span class="d-none d-md-initial">(after discount)</span></th>
				<th>Discount</th>
				<th data-class="all">Actions</th>
			</tr>
		</thead>
		<tbody>
			<tr ng-repeat="product in products">
				<td>{{product.item1.product}}</td>
				<td>{{product.item2}}</td>
				<td>{{formatCurrency(product.item3)}}</td>
				<td>{{formatCurrency(product.item4)}}</td>
				<td>
					<div class="d-flex justify-content-center align-items-center">
						<button data-target="#add-to-cart" ng-click="preAddToCart(product)" data-toggle="modal" class="btn btn-info mr-3"><i class="fas fa-pen"></i></button>
						<button class="btn btn-danger" ng-click="removeFromCart($index)"><i class="fas fa-times"></i></button>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
</div>