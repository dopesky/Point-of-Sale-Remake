package com.herokuapp.pointofsale.ui.pos.fragments;

import android.app.Activity;
import android.arch.lifecycle.Observer;
import android.os.Bundle;
import android.support.annotation.NonNull;
import android.support.annotation.Nullable;
import android.support.constraint.ConstraintLayout;
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
import com.herokuapp.pointofsale.ui.RecyclerViewAdapters.ProductsAdapter;
import com.herokuapp.pointofsale.ui.pos.Purchases;

import java.util.ArrayList;

public class ViewProducts extends Fragment {

	private boolean isPurchases;
	private Activity activity;

	private View viewProducts;
	private RecyclerView recyclerView;
	private SwipeRefreshLayout swipeToRefresh;

	private ProductsAdapter adapter;
	private LinkedTreeMap userDetails;

	private Observer<LinkedTreeMap> getUserDataObserver = userdata -> {
		if(userdata != null && !userdata.isEmpty()) {
			userDetails = userdata;
		}else{
			ConstraintLayout layout = viewProducts.findViewById(R.id.progress_bar);
			ProgressBar progressBar = (ProgressBar) layout.getChildAt(0);
			TextView textView = (TextView) layout.getChildAt(1);
			progressBar.setVisibility(View.GONE);
			if(adapter == null || adapter.getItemCount() < 1)
				textView.setVisibility(View.VISIBLE);
		}
	};

	private Observer<ArrayList> getProductsObserver = products -> {
		if(products != null && !products.isEmpty()){
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

	public static ViewProducts newInstance(boolean isPurchases, Activity activity) {
		ViewProducts viewProducts = new ViewProducts();
		viewProducts.isPurchases = isPurchases;
		viewProducts.activity = activity;
		return viewProducts;
	}

	@Override
	public View onCreateView(@NonNull LayoutInflater inflater, @Nullable ViewGroup container,
							 @Nullable Bundle savedInstanceState) {
		if(viewProducts != null) return viewProducts;
		viewProducts = inflater.inflate(R.layout.view_products_fragment, container, false);
		return viewProducts;
	}

	@Override
	public void onActivityCreated(@Nullable Bundle savedInstanceState) {
		super.onActivityCreated(savedInstanceState);

		if(recyclerView == null){
			recyclerView = viewProducts.findViewById(R.id.recyclerview);
			swipeToRefresh = viewProducts.findViewById(R.id.swipeToRefresh);
			swipeToRefresh.setOnRefreshListener(this::refreshProducts);
			if(isPurchases){
				((Purchases)activity).getProducts().observe(this, getProductsObserver);
				((Purchases)activity).getUserData().observe(this, getUserDataObserver);
				((Purchases)activity).getFilter().observe(this, filterObserver);
			}
		}
		refreshProducts();
	}

	private void showProducts(ArrayList products) {
		ConstraintLayout layout = viewProducts.findViewById(R.id.progress_bar);
		layout.setVisibility(ConstraintLayout.GONE);
		adapter = new ProductsAdapter(activity, products, userDetails);
		recyclerView.setAdapter(adapter);
		recyclerView.setLayoutManager(new LinearLayoutManager(activity));
	}

	private void refreshProducts(){
		ConstraintLayout layout = viewProducts.findViewById(R.id.progress_bar);
		layout.setVisibility(ConstraintLayout.VISIBLE);
		ProgressBar progressBar = (ProgressBar) layout.getChildAt(0);
		progressBar.setVisibility(View.VISIBLE);
		TextView textView = (TextView) layout.getChildAt(1);
		textView.setVisibility(View.GONE);
		swipeToRefresh.setRefreshing(false);
		if(isPurchases){
			((Purchases)activity).refreshProducts();
		}
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
