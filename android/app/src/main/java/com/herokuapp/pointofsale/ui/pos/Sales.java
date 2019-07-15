package com.herokuapp.pointofsale.ui.pos;

import androidx.lifecycle.LiveData;
import androidx.lifecycle.MutableLiveData;
import androidx.lifecycle.Observer;
import androidx.lifecycle.ViewModelProviders;
import android.content.Intent;
import android.content.SharedPreferences;
import android.graphics.Color;
import com.google.android.material.appbar.AppBarLayout;
import com.google.android.material.tabs.TabLayout;
import androidx.core.view.LayoutInflaterCompat;
import androidx.viewpager.widget.ViewPager;
import androidx.appcompat.app.AppCompatActivity;
import android.os.Bundle;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;

import com.ferfalk.simplesearchview.SimpleSearchView;
import com.herokuapp.pointofsale.R;
import com.herokuapp.pointofsale.ui.resources.Common;
import com.herokuapp.pointofsale.ui.resources.CustomToast;
import com.herokuapp.pointofsale.ui.resources.NavigationBars;
import com.herokuapp.pointofsale.ui.resources.SalesAdapter;
import com.mikepenz.fontawesome_typeface_library.FontAwesome;
import com.mikepenz.iconics.IconicsDrawable;
import com.mikepenz.iconics.context.IconicsLayoutInflater2;

import java.util.Objects;

public class Sales extends AppCompatActivity {

	private com.herokuapp.pointofsale.models.pos.Sales salesVM;

	private MutableLiveData<String> filter;
	public LiveData<String> getFilter(){return filter;}

	private SimpleSearchView searchView;
	private AppBarLayout toolbar;

	private Observer<SharedPreferences> getUserdataObserver = sharedPreferences -> {
		if(sharedPreferences == null || Integer.parseInt(sharedPreferences.getString("level", "-1")) < 1){
			Common.launchLauncherActivity(this);
		}
	};

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		LayoutInflaterCompat.setFactory2(getLayoutInflater(), new IconicsLayoutInflater2(getDelegate()));

		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_purchases);

		filter = new MutableLiveData<>();
		salesVM = ViewModelProviders.of(this).get(com.herokuapp.pointofsale.models.pos.Sales.class);
		salesVM.getUserData().observe(this, getUserdataObserver);
		salesVM.fetchUserDetails();

		toolbar = findViewById(R.id.toolbar);
		initializeTabLayout(toolbar.findViewById(R.id.tab_layout));
		Common.setCustomActionBar(this, toolbar.findViewById(R.id.actual_toolbar));
		NavigationBars.getNavBar(this, toolbar.findViewById(R.id.actual_toolbar), Objects.requireNonNull(salesVM.getUserData().getValue()).getString("level", "-1"));

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
		SharedPreferences preferences = salesVM.getUserData().getValue();
		int drawableColor = R.color.colorPrimaryDark;
		Objects.requireNonNull(tabLayout.getTabAt(0)).setIcon(new IconicsDrawable(this)
				.icon(FontAwesome.Icon.faw_shopping_cart)
				.colorRes(drawableColor));
		Objects.requireNonNull(tabLayout.getTabAt(1)).setIcon(new IconicsDrawable(this)
				.icon(FontAwesome.Icon.faw_cart_plus)
				.colorRes(drawableColor));
		Objects.requireNonNull(tabLayout.getTabAt(2)).setIcon(new IconicsDrawable(this)
				.icon(FontAwesome.Icon.faw_toolbox)
				.colorRes(drawableColor));
		if(preferences == null || Integer.parseInt(preferences.getString("level", "-1")) < 2){
			tabLayout.removeTabAt(2);
		}
		ViewPager pager = findViewById(R.id.view_pager);
		SalesAdapter adapter = new SalesAdapter(getSupportFragmentManager());
		pager.setAdapter(adapter);
		pager.setOffscreenPageLimit(2);
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

	public void closeSearchView(){
		searchView.onBackPressed();
	}

	public boolean isSearchOpen(){
		return searchView != null && searchView.isSearchOpen();
	}

	public void refreshToolbarNumber(int number){
		TabLayout layout = toolbar.findViewById(R.id.tab_layout);
		TabLayout.Tab tab = Objects.requireNonNull(layout.getTabAt(1));
		tab.getOrCreateBadge().setNumber(number);
		tab.getOrCreateBadge().setVisible(number != 0);
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
	}

	@Override
	public void onBackPressed(){
		if(searchView.onBackPressed()) return;
		super.onBackPressed();
	}
}
