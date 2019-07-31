package com.herokuapp.pointofsale.ui.owner;

import androidx.lifecycle.Observer;
import androidx.lifecycle.ViewModelProviders;

import android.content.SharedPreferences;

import com.google.android.material.appbar.AppBarLayout;
import androidx.core.view.LayoutInflaterCompat;
import androidx.appcompat.app.AppCompatActivity;
import android.os.Bundle;

import com.herokuapp.pointofsale.R;
import com.herokuapp.pointofsale.viewmodels.owner.Owner;
import com.herokuapp.pointofsale.resources.Common;

import java.util.Objects;;

import com.herokuapp.pointofsale.resources.NavigationBars;
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
