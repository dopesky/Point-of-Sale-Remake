package com.herokuapp.pointofsale.ui.settings;

import androidx.appcompat.app.AppCompatActivity;
import androidx.core.view.LayoutInflaterCompat;
import androidx.lifecycle.LiveData;
import androidx.lifecycle.MutableLiveData;
import androidx.lifecycle.Observer;
import androidx.lifecycle.ViewModelProviders;
import androidx.viewpager.widget.ViewPager;

import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.view.Menu;

import com.ferfalk.simplesearchview.SimpleSearchView;
import com.google.android.material.appbar.AppBarLayout;
import com.google.android.material.tabs.TabLayout;
import com.google.gson.internal.LinkedTreeMap;
import com.herokuapp.pointofsale.R;
import com.herokuapp.pointofsale.resources.Common;
import com.herokuapp.pointofsale.resources.SettingsAdapter;
import com.mikepenz.fontawesome_typeface_library.FontAwesome;
import com.mikepenz.iconics.IconicsDrawable;
import com.mikepenz.iconics.context.IconicsLayoutInflater2;

import java.util.LinkedHashMap;
import java.util.Objects;

public class Settings extends AppCompatActivity {

	private com.herokuapp.pointofsale.viewmodels.settings.Settings settingsVM;

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
		setContentView(R.layout.activity_settings);

		settingsVM = ViewModelProviders.of(this).get(com.herokuapp.pointofsale.viewmodels.settings.Settings.class);
		settingsVM.getUserData().observe(this, getUserdataObserver);

		toolbar = findViewById(R.id.toolbar);
		initializeTabLayout(toolbar.findViewById(R.id.tab_layout));
		Common.setCustomActionBar(this, toolbar.findViewById(R.id.actual_toolbar));
		Objects.requireNonNull(getSupportActionBar()).setDisplayHomeAsUpEnabled(true);
		getSupportActionBar().setDisplayShowHomeEnabled(true);

		filter = new MutableLiveData<>();

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
		int drawableColor = R.color.colorPrimaryDark;
		Objects.requireNonNull(tabLayout.getTabAt(0)).setIcon(new IconicsDrawable(this)
				.icon(FontAwesome.Icon.faw_user1)
				.colorRes(drawableColor));
		Objects.requireNonNull(tabLayout.getTabAt(1)).setIcon(new IconicsDrawable(this)
				.icon(FontAwesome.Icon.faw_shield_alt)
				.colorRes(drawableColor));
		Objects.requireNonNull(tabLayout.getTabAt(2)).setIcon(new IconicsDrawable(this)
				.icon(FontAwesome.Icon.faw_toolbox)
				.colorRes(drawableColor));
		ViewPager pager = findViewById(R.id.view_pager);
		SettingsAdapter adapter = new SettingsAdapter(getSupportFragmentManager());
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

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		return Common.inflateMenu(searchView, menu, this);
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

	@Override
	public boolean onSupportNavigateUp() {
		onBackPressed();
		return true;
	}
}
