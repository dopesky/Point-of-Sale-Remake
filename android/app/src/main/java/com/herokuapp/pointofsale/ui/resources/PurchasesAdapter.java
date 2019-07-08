package com.herokuapp.pointofsale.ui.resources;

import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentPagerAdapter;

import com.herokuapp.pointofsale.ui.pos.Purchases;
import com.herokuapp.pointofsale.ui.pos.fragments.ManagePurchases;
import com.herokuapp.pointofsale.ui.pos.fragments.ViewCart;
import com.herokuapp.pointofsale.ui.pos.fragments.ViewProducts;

public class PurchasesAdapter extends FragmentPagerAdapter {
	private Purchases purchasesActivity;

	public PurchasesAdapter(FragmentManager fm, Purchases purchasesActivity) {
		super(fm);
		this.purchasesActivity = purchasesActivity;
	}

	@Override
	public Fragment getItem(int i) {
		switch (i){
			case 0:
				return ViewProducts.newInstance(true, purchasesActivity);
			case 1:
				return ViewCart.newInstance(true, purchasesActivity);
			case 2:
				return new ManagePurchases();
			default:
				return null;
		}
	}

	@Override
	public int getCount() {
		return 3;
	}
}
