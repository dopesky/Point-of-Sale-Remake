package com.herokuapp.pointofsale.ui.pos.fragments;

import android.app.Activity;
import android.arch.lifecycle.Observer;
import android.os.Bundle;
import android.support.annotation.NonNull;
import android.support.annotation.Nullable;
import android.support.constraint.ConstraintLayout;
import android.support.design.button.MaterialButton;
import android.support.v4.app.Fragment;
import android.support.v4.widget.SwipeRefreshLayout;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ProgressBar;
import android.widget.TextView;

import com.google.gson.internal.LinkedTreeMap;
import com.herokuapp.pointofsale.R;
import com.herokuapp.pointofsale.ui.RecyclerViewAdapters.CartAdapter;
import com.herokuapp.pointofsale.ui.RecyclerViewAdapters.ProductsAdapter;
import com.herokuapp.pointofsale.ui.pos.Purchases;

import java.util.ArrayList;

public class ViewCart extends Fragment {

	private boolean isPurchases;
	private Activity activity;

	private View viewProducts;
	private RecyclerView recyclerView;
	private SwipeRefreshLayout swipeToRefresh;

	private CartAdapter adapter;
	private LinkedTreeMap userDetails;

	private Observer<LinkedTreeMap> getUserDataObserver = userdata -> {
		if(userdata != null && !userdata.isEmpty()) {
			userDetails = userdata;
		}
	};

	private Observer<ArrayList> getProductsObserver = products -> {
		if(products != null){
			showProducts(products);
		}else{
			ConstraintLayout layout = viewProducts.findViewById(R.id.progress_bar);
			ProgressBar progressBar = (ProgressBar) layout.getChildAt(0);
			TextView textView = (TextView) layout.getChildAt(1);
			progressBar.setVisibility(View.GONE);
			if(adapter == null || adapter.getItemCount() < 1)
				textView.setVisibility(View.VISIBLE);
		}
	};

	private Observer<String> filterObserver = filter -> {
		if(filter != null){
			setAdapterFilter(filter);
		}
	};

	public static ViewCart newInstance(boolean isPurchases, Activity activity) {
		ViewCart viewCart = new ViewCart();
		viewCart.isPurchases = isPurchases;
		viewCart.activity = activity;
		return viewCart;
	}

	@Override
	public View onCreateView(@NonNull LayoutInflater inflater, @Nullable ViewGroup container,
							 @Nullable Bundle savedInstanceState) {
		if(viewProducts != null) return viewProducts;
		viewProducts = inflater.inflate(R.layout.view_cart_fragment, container, false);
		return viewProducts;
	}

	@Override
	public void onActivityCreated(@Nullable Bundle savedInstanceState) {
		super.onActivityCreated(savedInstanceState);

		MaterialButton button = viewProducts.findViewById(R.id.add_button);
		button.setOnClickListener(v -> ((Purchases)activity).addPurchases());

		if(recyclerView == null){
			recyclerView = viewProducts.findViewById(R.id.recyclerview);
			swipeToRefresh = viewProducts.findViewById(R.id.swipeToRefresh);
			swipeToRefresh.setOnRefreshListener(() -> swipeToRefresh.setRefreshing(false));
			if(isPurchases){
				((com.herokuapp.pointofsale.ui.pos.Purchases)activity).getSelectedProducts().observe(this, getProductsObserver);
				((com.herokuapp.pointofsale.ui.pos.Purchases)activity).getUserData().observe(this, getUserDataObserver);
				((Purchases)activity).getFilter().observe(this, filterObserver);
			}
		}

		ConstraintLayout layout = viewProducts.findViewById(R.id.progress_bar);
		layout.setVisibility(ConstraintLayout.VISIBLE);
		ProgressBar progressBar = (ProgressBar) layout.getChildAt(0);
		progressBar.setVisibility(View.GONE);
		TextView textView = (TextView) layout.getChildAt(1);
		textView.setVisibility(adapter == null || adapter.getItemCount() < 1 ? View.VISIBLE : View.GONE);
	}

	private void showProducts(ArrayList products) {
		ConstraintLayout layout = viewProducts.findViewById(R.id.progress_bar);
		layout.setVisibility(ConstraintLayout.GONE);
		adapter = new CartAdapter(activity, products, userDetails);
		recyclerView.setAdapter(adapter);
		recyclerView.setLayoutManager(new LinearLayoutManager(activity));
	}

	private void setAdapterFilter(String filter) {
		if(adapter != null){
			adapter.setFilter(filter);
			ConstraintLayout layout = viewProducts.findViewById(R.id.progress_bar);
			layout.setVisibility(ConstraintLayout.VISIBLE);
			ProgressBar progressBar = (ProgressBar) layout.getChildAt(0);
			progressBar.setVisibility(View.GONE);
			TextView textView = (TextView) layout.getChildAt(1);
			textView.setVisibility(adapter.getItemCount() < 1 ? View.VISIBLE : View.GONE);
		}
	}

}
