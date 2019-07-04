package com.herokuapp.pointofsale.ui.owner;

import android.arch.lifecycle.Observer;
import android.arch.lifecycle.ViewModelProviders;
import android.content.Intent;
import android.content.SharedPreferences;
import android.databinding.DataBindingUtil;
import android.support.constraint.ConstraintLayout;
import android.support.v4.view.LayoutInflaterCompat;
import android.support.v4.widget.SwipeRefreshLayout;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.support.v7.widget.Toolbar;
import android.view.View;
import android.widget.ProgressBar;
import android.widget.TextView;

import com.herokuapp.pointofsale.R;
import com.herokuapp.pointofsale.databinding.ActivityManangeEmployeesBinding;
import com.herokuapp.pointofsale.models.owner.Owner;
import com.herokuapp.pointofsale.ui.RecyclerViewAdapters.OwnerEmployeesAdapter;
import com.herokuapp.pointofsale.ui.auth.MainActivity;
import com.herokuapp.pointofsale.ui.resources.Common;
import com.herokuapp.pointofsale.ui.resources.NavigationBars;
import com.mikepenz.iconics.context.IconicsLayoutInflater2;

import java.util.ArrayList;
import java.util.Objects;

public class ManangeEmployees extends AppCompatActivity {
	private RecyclerView showEmployees;
	private SwipeRefreshLayout swipeToRefresh;
	private OwnerEmployeesAdapter adapter;
	private Owner ownerVM;

	private Observer<ArrayList> getEmployeesObserver = employees -> {
		if(employees != null && !employees.isEmpty()){
			showEmployees(employees);
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
		if(sharedPreferences == null || !sharedPreferences.contains("user_id")){
			Intent intent = new Intent(this, MainActivity.class);
			intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK | Intent.FLAG_ACTIVITY_TASK_ON_HOME);
			startActivity(intent);
			overridePendingTransition(0,0);
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

		Toolbar toolbar = findViewById(R.id.toolbar);
		Common.setCustomActionBar(this, toolbar);
		NavigationBars.getNavBar(this, toolbar, Objects.requireNonNull(ownerVM.getUserData().getValue()).getString("level", "-1"));

		showEmployees = findViewById(R.id.recyclerview);
		swipeToRefresh = findViewById(R.id.swipeToRefresh);
		swipeToRefresh.setOnRefreshListener(this::refreshEmployees);
		ownerVM.fetchEmployees();

		binding.setLifecycleOwner(this);
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
	}

	private void showEmployees(ArrayList employees) {
		ConstraintLayout layout = findViewById(R.id.progress_bar);
		layout.setVisibility(ConstraintLayout.GONE);
		if(adapter == null){
			adapter = new OwnerEmployeesAdapter(this, employees);
			showEmployees.setAdapter(adapter);
			showEmployees.setLayoutManager(new LinearLayoutManager(this));
		}else{
			adapter.updateData(employees);
		}
	}
}
