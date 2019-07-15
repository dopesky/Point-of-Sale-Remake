package com.herokuapp.pointofsale.ui.pos.fragments;

import android.os.Bundle;

import androidx.fragment.app.Fragment;

import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

import com.herokuapp.pointofsale.R;

public class ManageSales extends Fragment {

	public static ManageSales newInstance() {
		return new ManageSales();
	}

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
	}

	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
							 Bundle savedInstanceState) {
		return inflater.inflate(R.layout.fragment_manage_sales, container, false);
	}
}
