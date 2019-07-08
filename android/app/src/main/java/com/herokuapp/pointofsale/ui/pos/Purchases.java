package com.herokuapp.pointofsale.ui.pos;

import android.arch.lifecycle.LiveData;
import android.arch.lifecycle.MutableLiveData;
import android.arch.lifecycle.Observer;
import android.arch.lifecycle.ViewModelProviders;
import android.content.Intent;
import android.content.SharedPreferences;
import android.graphics.Color;
import android.support.design.widget.AppBarLayout;
import android.support.design.widget.TabLayout;
import android.support.v4.content.ContextCompat;
import android.support.v4.view.LayoutInflaterCompat;
import android.support.v4.view.ViewPager;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;

import com.ferfalk.simplesearchview.SimpleSearchView;
import com.google.gson.internal.LinkedTreeMap;
import com.herokuapp.pointofsale.R;
import com.herokuapp.pointofsale.ui.resources.Common;
import com.herokuapp.pointofsale.ui.resources.CustomToast;
import com.herokuapp.pointofsale.ui.resources.NavigationBars;
import com.herokuapp.pointofsale.ui.resources.PurchasesAdapter;
import com.mikepenz.fontawesome_typeface_library.FontAwesome;
import com.mikepenz.iconics.IconicsDrawable;
import com.mikepenz.iconics.context.IconicsLayoutInflater2;

import java.util.ArrayList;
import java.util.Objects;

public class Purchases extends AppCompatActivity {

	private com.herokuapp.pointofsale.models.pos.Purchases purchasesVM;

	private MutableLiveData<LinkedTreeMap> userdata;
	public LiveData<LinkedTreeMap> getUserData(){return userdata;}

	private MutableLiveData<ArrayList> products;
	public LiveData<ArrayList> getProducts(){return products;}

	private MutableLiveData<String> filter;
	public LiveData<String> getFilter(){return filter;}

	public MutableLiveData<ArrayList> selectedProducts;
	public LiveData<ArrayList> getSelectedProducts(){return selectedProducts;}

	private SimpleSearchView searchView;

	private Observer<SharedPreferences> getUserdataObserver = sharedPreferences -> {
		if(sharedPreferences == null || Integer.parseInt(sharedPreferences.getString("level", "-1")) < 1){
			Common.launchLauncherActivity(this);
		}
	};

	private Observer<LinkedTreeMap> getUserDetailsObserver = userDetails -> {
		if(userDetails != null && userDetails.get("user_id") != null){
			userdata.setValue(userDetails);
			refreshProducts();
		}else{
			userdata.setValue(null);
		}
	};

	private Observer<ArrayList> getPurchaseProductsObserver = products -> {
		if(products != null && products.size() > 0){
			this.products.setValue(products);
		}else{
			this.products.setValue(null);
		}
	};

	private Observer<Integer> getPurchaseStatusObserver = status ->{
		if(status != null && status == 0){
			selectedProducts.setValue(new ArrayList());
			refreshProducts();
		}
	};

	private Observer<String> getPurchaseErrorObserver = error ->{
		if(error != null && !error.trim().isEmpty()){
			CustomToast.showToast(this, " " + error, "danger");
		}
	};

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		LayoutInflaterCompat.setFactory2(getLayoutInflater(), new IconicsLayoutInflater2(getDelegate()));

		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_purchases);

		products = new MutableLiveData<>();
		userdata = new MutableLiveData<>();
		filter = new MutableLiveData<>();
		selectedProducts = new MutableLiveData<>();
		purchasesVM = ViewModelProviders.of(this).get(com.herokuapp.pointofsale.models.pos.Purchases.class);
		purchasesVM.getUserData().observe(this, getUserdataObserver);
		purchasesVM.getCurrentUserDetails().observe(this, getUserDetailsObserver);
		purchasesVM.getPurchaseProducts().observe(this, getPurchaseProductsObserver);
		purchasesVM.getAddPurchaseStatus().observe(this, getPurchaseStatusObserver);
		purchasesVM.getAddPurchaseError().observe(this, getPurchaseErrorObserver);


		AppBarLayout toolbar = findViewById(R.id.toolbar);
		initializeTabLayout(toolbar.findViewById(R.id.tab_layout));
		Common.setCustomActionBar(this, toolbar.findViewById(R.id.actual_toolbar));
		NavigationBars.getNavBar(this, toolbar.findViewById(R.id.actual_toolbar), Objects.requireNonNull(purchasesVM.getUserData().getValue()).getString("level", "-1"));

		searchView = findViewById(R.id.search_view);
		searchView.setOnQueryTextListener(new SimpleSearchView.OnQueryTextListener() {
			@Override
			public boolean onQueryTextSubmit(String query) {
				filter.setValue(query);
				searchView.clearFocus();
				return true;
			}

			@Override
			public boolean onQueryTextChange(String newText) {
				filter.setValue(newText);
				return false;
			}

			@Override
			public boolean onQueryTextCleared() {
				filter.setValue("");
				return false;
			}
		});
		searchView.setOnSearchViewListener(new SimpleSearchView.SearchViewListener() {
			@Override
			public void onSearchViewShown() {}

			@Override
			public void onSearchViewClosed() {
				filter.setValue("");
			}

			@Override
			public void onSearchViewShownAnimation() {}

			@Override
			public void onSearchViewClosedAnimation() {}
		});
	}

	private void initializeTabLayout(TabLayout tabLayout){
		SharedPreferences preferences = purchasesVM.getUserData().getValue();

		Objects.requireNonNull(tabLayout.getTabAt(0)).setIcon(new IconicsDrawable(this)
				.icon(FontAwesome.Icon.faw_shopping_cart)
				.color(ContextCompat.getColor(this, R.color.colorPrimaryDark)));
		Objects.requireNonNull(tabLayout.getTabAt(1)).setIcon(new IconicsDrawable(this)
				.icon(FontAwesome.Icon.faw_cart_plus)
				.color(ContextCompat.getColor(this, R.color.colorPrimaryDark)));
		Objects.requireNonNull(tabLayout.getTabAt(2)).setIcon(new IconicsDrawable(this)
				.icon(FontAwesome.Icon.faw_toolbox)
				.color(ContextCompat.getColor(this, R.color.colorPrimaryDark)));
		if(preferences == null || Integer.parseInt(preferences.getString("level", "-1")) < 2){
			tabLayout.removeTabAt(2);
		}
		ViewPager pager = findViewById(R.id.view_pager);
		PurchasesAdapter adapter = new PurchasesAdapter(getSupportFragmentManager(),this);
		pager.setAdapter(adapter);
		pager.addOnPageChangeListener(new TabLayout.TabLayoutOnPageChangeListener(tabLayout));
		tabLayout.addOnTabSelectedListener(new TabLayout.OnTabSelectedListener() {
			@Override
			public void onTabSelected(TabLayout.Tab tab) {
				pager.setCurrentItem(tab.getPosition());
			}

			@Override
			public void onTabUnselected(TabLayout.Tab tab) {}

			@Override
			public void onTabReselected(TabLayout.Tab tab) {}
		});
	}

	public void addPurchases(){
		purchasesVM.addPurchases(selectedProducts.getValue());
	}

	public void refreshProducts(){
		searchView.onBackPressed();
		if(userdata == null || userdata.getValue() == null || userdata.getValue().size() < 1){
			purchasesVM.fetchUserDetails();
		}else{
			purchasesVM.fetchPurchaseProducts();
		}
	}

	@Override
	public void onResume(){
		super.onResume();
		if(searchView != null && searchView.isSearchOpen()) return;
		refreshProducts();
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		MenuInflater inflater = getMenuInflater();
		inflater.inflate(R.menu.menu_items, menu);

		MenuItem search = menu.findItem(R.id.action_search);
		MenuItem notification = menu.findItem(R.id.action_notification);
		IconicsDrawable drawable = new IconicsDrawable(this).icon(FontAwesome.Icon.faw_bell).color(Color.WHITE).actionBar();
		notification.setIcon(drawable);
		notification.setOnMenuItemClickListener(item -> {
			CustomToast.showToast(this, " That Functionality is not Available Yet!", "info");
			return false;
		});
		searchView.setMenuItem(search);

		return true;
	}

	@Override
	protected void onActivityResult(int requestCode, int resultCode, Intent data) {
		if (searchView.onActivityResult(requestCode, resultCode, data))	return;

		super.onActivityResult(requestCode, resultCode, data);
		if((requestCode == 1 || requestCode == 2) && resultCode == RESULT_OK && data.getBooleanExtra("forceReload", false)){
			refreshProducts();
		}
		if(requestCode == 3 && resultCode == RESULT_OK){
			ArrayList list;
			if(selectedProducts.getValue() == null){
				list = new ArrayList();
			}else{
				list = selectedProducts.getValue();
			}

			LinkedTreeMap map = new LinkedTreeMap();
			for(String key : Objects.requireNonNull(data.getExtras()).keySet()){
				map.put(key, data.getExtras().get(key));
			}
			list.add(map);
			selectedProducts.setValue(list);
		}
	}

	@Override
	public void onBackPressed(){
		if(searchView.onBackPressed()) return;
		super.onBackPressed();
	}
}
