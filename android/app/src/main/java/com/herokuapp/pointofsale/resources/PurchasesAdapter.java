package com.herokuapp.pointofsale.ui.resources;

import androidx.annotation.NonNull;
import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentManager;
import androidx.fragment.app.FragmentPagerAdapter;

import com.herokuapp.pointofsale.ui.pos.fragments.ManagePurchases;
import com.herokuapp.pointofsale.ui.pos.fragments.ViewCart;
import com.herokuapp.pointofsale.ui.pos.fragments.ViewProducts;

public class PurchasesAdapter extends FragmentPagerAdapter {
	public PurchasesAdapter(FragmentManager fm) {
		super(fm, FragmentPagerAdapter.BEHAVIOR_RESUME_ONLY_CURRENT_FRAGMENT);
	}

	@NonNull
	@Override
	public Fragment getItem(int i) {
		switch (i){
			case 0:
				return ViewProducts.newInstance();
			case 1:
				return  ViewCart.newInstance();
			case 2:
				return new ManagePurchases();
			default:
				throw new IllegalArgumentException("Illegal Access Index! No Fragment can be Loaded at that Index!");
		}
	}

	@Override
	public int getCount() {
		return 3;
	}
}
