package com.herokuapp.pointofsale.ui.owner;

import android.arch.lifecycle.Observer;
import android.arch.lifecycle.ViewModelProviders;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.support.annotation.Nullable;
import android.support.constraint.ConstraintLayout;
import android.support.design.widget.AppBarLayout;
import android.support.v4.view.LayoutInflaterCompat;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.support.v7.app.AppCompatCallback;
import android.support.v7.app.AppCompatDelegate;
import android.support.v7.view.ActionMode;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.support.v7.widget.Toolbar;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.ProgressBar;
import android.widget.TextView;

import com.herokuapp.pointofsale.R;
import com.herokuapp.pointofsale.models.owner.Owner;
import com.herokuapp.pointofsale.ui.RecyclerViewAdapters.OwnerEmployeesAdapter;
import com.herokuapp.pointofsale.ui.auth.MainActivity;
import com.herokuapp.pointofsale.ui.resources.Common;

import java.util.ArrayList;
import java.util.Objects;;

import com.herokuapp.pointofsale.ui.resources.NavigationBars;
import com.mikepenz.iconics.context.IconicsContextWrapper;
import com.mikepenz.iconics.context.IconicsLayoutInflater;
import com.mikepenz.iconics.context.IconicsLayoutInflater2;

public class OwnerDashboard extends AppCompatActivity {
	private Owner ownerVM;
	private Observer<SharedPreferences> getUserdataObserver = sharedPreferences -> {
		if(sharedPreferences == null || !sharedPreferences.getString("level", "-1").trim().equals("4")){
			Common.launchLauncherActivity(this);
		}
	};

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		LayoutInflaterCompat.setFactory2(getLayoutInflater(), new IconicsLayoutInflater2(getDelegate()));
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_owner_dashboard);

		ownerVM = ViewModelProviders.of(this).get(Owner.class);
		ownerVM.getUserData().observe(this, getUserdataObserver);

		AppBarLayout toolbar = findViewById(R.id.toolbar);
		Common.setCustomActionBar(this, toolbar.findViewById(R.id.actual_toolbar));
		NavigationBars.getNavBar(this, toolbar.findViewById(R.id.actual_toolbar), Objects.requireNonNull(ownerVM.getUserData().getValue()).getString("level", "-1"));
	}
}
