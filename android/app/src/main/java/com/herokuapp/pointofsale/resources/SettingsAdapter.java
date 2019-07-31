package com.herokuapp.pointofsale.resources;

import androidx.annotation.NonNull;
import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentManager;
import androidx.fragment.app.FragmentPagerAdapter;

import com.herokuapp.pointofsale.ui.pos.fragments.ManageSales;
import com.herokuapp.pointofsale.ui.pos.fragments.ViewCart;
import com.herokuapp.pointofsale.ui.pos.fragments.ViewProducts;
import com.herokuapp.pointofsale.ui.settings.fragments.MyProfile;

public class SettingsAdapter extends FragmentPagerAdapter {

	public SettingsAdapter(FragmentManager fm) {
		super(fm, FragmentPagerAdapter.BEHAVIOR_RESUME_ONLY_CURRENT_FRAGMENT);
	}

	@NonNull
	@Override
	public Fragment getItem(int i) {
		switch (i){
			case 0:
				return MyProfile.newInstance();
			case 1:
				return new ManageSales();
			case 2:
				return new ManageSales();
			default:
				throw new IllegalArgumentException("Illegal Access Index! No Fragment can be Loaded at that Index!");
		}
	}

	@Override
	public int getCount() {
		return 3;
	}
}
