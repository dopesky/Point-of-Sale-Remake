package com.herokuapp.pointofsale.ui.owner;

import androidx.lifecycle.Observer;
import androidx.lifecycle.ViewModelProviders;
import android.content.Intent;
import android.content.SharedPreferences;
import androidx.databinding.DataBindingUtil;
import android.graphics.Color;
import androidx.constraintlayout.widget.ConstraintLayout;
import com.google.android.material.appbar.AppBarLayout;
import androidx.core.app.ActivityCompat;
import androidx.core.app.ActivityOptionsCompat;
import androidx.core.view.LayoutInflaterCompat;
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;
import androidx.appcompat.app.AppCompatActivity;
import android.os.Bundle;
import androidx.recyclerview.widget.RecyclerView;

import android.os.Parcelable;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.View;
import android.widget.ProgressBar;
import android.widget.TextView;

import com.ferfalk.simplesearchview.SimpleSearchView;
import com.herokuapp.pointofsale.R;
import com.herokuapp.pointofsale.databinding.ActivityManangeEmployeesBinding;
import com.herokuapp.pointofsale.models.owner.Owner;
import com.herokuapp.pointofsale.ui.RecyclerViewAdapters.OwnerEmployeesAdapter;
import com.herokuapp.pointofsale.ui.resources.Common;
import com.herokuapp.pointofsale.ui.resources.CustomToast;
import com.herokuapp.pointofsale.ui.resources.NavigationBars;
import com.mikepenz.fontawesome_typeface_library.FontAwesome;
import com.mikepenz.iconics.IconicsDrawable;
import com.mikepenz.iconics.context.IconicsLayoutInflater2;

import java.util.ArrayList;
import java.util.Objects;

public class ManageEmployees extends AppCompatActivity {
	private RecyclerView showEmployees;
	private SwipeRefreshLayout swipeToRefresh;
	private OwnerEmployeesAdapter adapter;
	private Owner ownerVM;
	private SimpleSearchView searchView;
	private Bundle recyclerViewState;

	private Observer<ArrayList> getEmployeesObserver = employees -> {
		if(employees != null && !employees.isEmpty()){
			showEmployees(Common.copyArrayList(employees));
		}
	};

	private Observer<String> getEmployeesErrorObserver = error -> {
		if(error != null && !error.trim().isEmpty()){
			ConstraintLayout layout = findViewById(R.id.progress_bar);
			ProgressBar progressBar = (ProgressBar) layout.getChildAt(0);
			TextView textView = (TextView) layout.getChildAt(1);
			progressBar.setVisibility(View.GONE);
			if(adapter == null)
				textView.setVisibility(View.VISIBLE);
		}
	};

	private Observer<SharedPreferences> getUserdataObserver = sharedPreferences -> {
		if(sharedPreferences == null || !sharedPreferences.getString("level", "-1").trim().equals("4")){
			Common.launchLauncherActivity(this);
		}
	};

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		LayoutInflaterCompat.setFactory2(getLayoutInflater(), new IconicsLayoutInflater2(getDelegate()));

		super.onCreate(savedInstanceState);
		ActivityManangeEmployeesBinding binding = DataBindingUtil.setContentView(this, R.layout.activity_manange_employees);

		ownerVM = ViewModelProviders.of(this).get(Owner.class);
		ownerVM.getEmployees().observe(this, getEmployeesObserver);
		ownerVM.getEmployeesError().observe(this, getEmployeesErrorObserver);
		ownerVM.getUserData().observe(this, getUserdataObserver);

		AppBarLayout toolbar = findViewById(R.id.toolbar);
		Common.setCustomActionBar(this, toolbar.findViewById(R.id.actual_toolbar));
		NavigationBars.getNavBar(this, toolbar.findViewById(R.id.actual_toolbar), Objects.requireNonNull(ownerVM.getUserData().getValue()).getString("level", "-1"));

		showEmployees = findViewById(R.id.recyclerview);
		showEmployees.setItemAnimator(Common.getItemAnimator());
		swipeToRefresh = findViewById(R.id.swipeToRefresh);
		searchView = findViewById(R.id.search_view);
		swipeToRefresh.setOnRefreshListener(this::refreshEmployees);
		searchView.setOnQueryTextListener(new SimpleSearchView.OnQueryTextListener() {
			@Override
			public boolean onQueryTextSubmit(String query) {
				setAdapterFilter(query);
				searchView.clearFocus();
				return true;
			}

			@Override
			public boolean onQueryTextChange(String newText) {
				setAdapterFilter(newText);
				return false;
			}

			@Override
			public boolean onQueryTextCleared() {
				setAdapterFilter("");
				return false;
			}
		});
		searchView.setOnSearchViewListener(new SimpleSearchView.SearchViewListener() {
			@Override
			public void onSearchViewShown() {}

			@Override
			public void onSearchViewClosed() {
				setAdapterFilter("");
			}

			@Override
			public void onSearchViewShownAnimation() {}

			@Override
			public void onSearchViewClosedAnimation() {}
		});

		binding.setLifecycleOwner(this);
	}

	@Override
	public void onPause(){
		super.onPause();
		if(showEmployees.getLayoutManager() != null){
			recyclerViewState = new Bundle();
			Parcelable listState = showEmployees.getLayoutManager().onSaveInstanceState();
			recyclerViewState.putParcelable("state", listState);
		}
	}

	@Override
	public void onResume(){
		super.onResume();

		if (recyclerViewState != null && recyclerViewState.containsKey("state") && showEmployees.getLayoutManager() != null) {
			Parcelable listState = recyclerViewState.getParcelable("state");
			showEmployees.getLayoutManager().onRestoreInstanceState(listState);
		}

		if(searchView != null && searchView.isSearchOpen()) return;
		refreshEmployees();
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
			refreshEmployees();
		}
	}

	@Override
	public void onBackPressed(){
		if(searchView.onBackPressed()) return;
		super.onBackPressed();
	}

	private void setAdapterFilter(String filter) {
		if(adapter != null){
			adapter.setFilter(filter);
			ConstraintLayout layout = findViewById(R.id.progress_bar);
			layout.setVisibility(ConstraintLayout.VISIBLE);
			ProgressBar progressBar = (ProgressBar) layout.getChildAt(0);
			progressBar.setVisibility(View.GONE);
			TextView textView = (TextView) layout.getChildAt(1);
			textView.setVisibility(adapter.getItemCount() < 1 ? View.VISIBLE : View.GONE);
		}
	}

	private void refreshEmployees(){
		ConstraintLayout layout = findViewById(R.id.progress_bar);
		layout.setVisibility(ConstraintLayout.VISIBLE);
		ProgressBar progressBar = (ProgressBar) layout.getChildAt(0);
		progressBar.setVisibility(View.VISIBLE);
		TextView textView = (TextView) layout.getChildAt(1);
		textView.setVisibility(View.GONE);
		ownerVM.fetchEmployees();
		swipeToRefresh.setRefreshing(false);
		searchView.onBackPressed();
	}

	private void showEmployees(ArrayList employees) {
		ConstraintLayout layout = findViewById(R.id.progress_bar);
		layout.setVisibility(ConstraintLayout.GONE);
		if(adapter == null){
			adapter = new OwnerEmployeesAdapter(this, employees);
			showEmployees.setAdapter(Common.getAdapterAnimation(adapter));
			showEmployees.setLayoutManager(Common.getLayoutManager(this));
		}else{
			adapter.updateData(employees);
		}
	}

	public void launchAddEmployees(View view) {
		String transitionName = getString(R.string.default_transition_name);
		Intent intent = new Intent(this, AddEmployee.class);
		ActivityOptionsCompat options =
				ActivityOptionsCompat.makeSceneTransitionAnimation(this, view, transitionName);
		ActivityCompat.startActivityForResult(this, intent, 2, options.toBundle());
	}
}
