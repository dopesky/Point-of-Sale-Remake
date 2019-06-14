<div ng-show="fetchingProducts" class="spinner-border text-info mt-3 mb-4"></div>
<div class="clearfix" ng-show="allProducts.length">
	<div class='input-group mb-3 float-md-right input-max-width'>
		<input type="text" name="search-products" class="form-control" ng-model="searchProducts" placeholder="Search for Product . . .">
		<div class='input-group-append'>
			<div class='input-group-text'>
				<i class='fas fa-search'></i>
			</div>
		</div>
	</div>
</div>
<h4 ng-show="noProducts" class="text-muted mb-4 mt-3"><i class="far fa-frown"></i> No Products Available to Complete this Transaction.</h4>
<div class="card-deck">
	<div class="card bg-translucent" ng-repeat="product in products | orderBy:'product'">
		<div class="bg-random rounded-top w-100 d-flex flex-wrap align-items-end justify-content-start height-100">
			<h6 class="card-title ml-3">{{product.product}}</h6>
		</div>
		<div class="card-body text-muted text-left">
			<p class="card-text text-muted">
				<b>Category: </b>{{product.category_name}}<br>
				<b>Unit Price: </b>{{formatCurrency(product.cost_per_unit)}}<br>
				<b>Inventory: </b>{{product.inventory_level}}<br>
				<b>Turnover: </b>{{product.inventory_turn_over !== null ? roundOff(product.inventory_turn_over,4) : 'N/A'}}
			</p>
		</div>
		<div class="card-footer text-right">
      		<button ng-if="!isSale && isPurchase" data-toggle="modal" data-target="#add-to-cart" ng-click="preAddToCart(product)" class="btn btn-success header-text font-sm"><i class="fas fa-cart-plus"></i> Add to Cart</button>
      		<button ng-if="isSale && !isPurchase" ng-click="addSalesToCart(product)" class="btn btn-success header-text font-sm"><i class="fas fa-cart-plus"></i> Add to Cart</button>
    	</div>
	</div>
</div>