package com.herokuapp.pointofsale.ui.pos.fragments;

import android.arch.lifecycle.ViewModelProviders;
import android.os.Bundle;
import android.support.annotation.NonNull;
import android.support.annotation.Nullable;
import android.support.v4.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

import com.herokuapp.pointofsale.R;
import com.herokuapp.pointofsale.models.pos.Purchases;

public class ManagePurchases extends Fragment {

	private Purchases purchasesVM;

	public static ManagePurchases newInstance() {
		return new ManagePurchases();
	}

	@Override
	public View onCreateView(@NonNull LayoutInflater inflater, @Nullable ViewGroup container,
							 @Nullable Bundle savedInstanceState) {
		return inflater.inflate(R.layout.manage_purchases_fragment, container, false);
	}

	@Override
	public void onActivityCreated(@Nullable Bundle savedInstanceState) {
		super.onActivityCreated(savedInstanceState);
		purchasesVM = ViewModelProviders.of(this).get(Purchases.class);
		// TODO: Use the ViewModel
	}

}
