<div class="mt-3 p-3 pt-0 pb-0">
	<div>
		<ul class="list-group text-secondary">
			<li class="list-group-item bg-translucent d-flex justify-content-between align-items-center">
				<span>Enable 2 Step Authentication <small class="text-muted"><i>(Recommended)</i></small><br><small ng-show="user.token.length"><a href="#show-QR-code" data-toggle='modal'>View QR Image</a></small></span>
				<span class="custom-control custom-switch">
				    <input type="checkbox" ng-change="onChange2FA()" class="custom-control-input" ng-model="user.twoFA" id="2fa">
				    <label class="custom-control-label" for="2fa"></label>
				</span>
			</li>
		</ul>
	</div><hr>
	<div>
		<ul class="list-group text-secondary">
			<li class="list-group-item bg-translucent d-flex justify-content-between align-items-center">
				<span>Show Items Disabled by Us<br><small>Shows items that have been disabled/deleted by stakeholders of the company.</small></span>
				<span class="custom-control custom-switch">
				    <input type="checkbox" class="custom-control-input" ng-change="onChangeShowInactive()" id="showInactive" ng-model="user.showInactive">
				    <label class="custom-control-label" for="showInactive"></label>
				</span>
			</li>
		</ul>
	</div><hr>
	<div>
		<ul class="list-group text-secondary">
			<li class="list-group-item bg-translucent d-flex justify-content-between align-items-center">
				<span>Show Items Disabled by Administrator<br><small>Shows items that have been disabled/deleted by our Admin team.</small></span>
				<span class="custom-control custom-switch">
				    <input type="checkbox" class="custom-control-input" ng-change="onChangeShowDeleted()" id="showDeleted" ng-model="user.showDeleted">
				    <label class="custom-control-label" for="showDeleted"></label>
				</span>
			</li>
		</ul>
	</div><hr>
	<div>
		<ul class="list-group text-secondary">
			<li class="list-group-item bg-translucent">
				<span>Change Country<br><small>Sets the User's country for purposes of localization. Turn off any VPN to <a onclick="getLocationDetails('select[name=country]')" class="text-info">Get Current Location</a> from your IP Address.</small></span>
				<select name="country" class="custom-select mt-3 mb-3 header-text" ng-change="onChangeCountry()" ng-model="user.country">
					<?php if($countries):?>
						<?php foreach($countries as $country):?>
							<option value="<?=strtolower($country->country_name)?>"><?=$country->country_name?></option>
						<?php endforeach;?>
					<?php endif;?>
				</select>
			</li>
		</ul>
	</div><hr>
</div>