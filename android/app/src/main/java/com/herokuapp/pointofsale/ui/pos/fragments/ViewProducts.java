package com.herokuapp.pointofsale.ui.pos.fragments;

import androidx.lifecycle.Observer;

import android.os.Bundle;
import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.constraintlayout.widget.ConstraintLayout;
import androidx.fragment.app.Fragment;
import androidx.lifecycle.ViewModelProviders;
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;
import androidx.recyclerview.widget.RecyclerView;

import android.os.Parcelable;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ProgressBar;
import android.widget.TextView;

import com.google.gson.internal.LinkedTreeMap;
import com.herokuapp.pointofsale.R;
import com.herokuapp.pointofsale.ui.RecyclerViewAdapters.ProductsAdapter;
import com.herokuapp.pointofsale.ui.pos.Purchases;
import com.herokuapp.pointofsale.ui.pos.Sales;
import com.herokuapp.pointofsale.ui.resources.Common;

import java.util.ArrayList;
import java.util.Objects;

public class ViewProducts extends Fragment {

	private com.herokuapp.pointofsale.models.pos.Purchases purchasesVM;
	private com.herokuapp.pointofsale.models.pos.Sales salesVM;

	private boolean isPurchases;

	private View viewProducts;
	private RecyclerView recyclerView;
	private SwipeRefreshLayout swipeToRefresh;
	private Bundle recyclerViewState;

	private ProductsAdapter adapter;
	private LinkedTreeMap userDetails;

	private Observer<LinkedTreeMap> getUserDataObserver = userdata -> {
		if(userdata != null && !userdata.isEmpty()) {
			userDetails = userdata;
			if(isPurchases) purchasesVM.fetchPurchaseProducts();
			if(!isPurchases) salesVM.fetchSaleProducts();
		}else{
			ConstraintLayout layout = viewProducts.findViewById(R.id.progress_bar);
			ProgressBar progressBar = (ProgressBar) layout.getChildAt(0);
			TextView textView = (TextView) layout.getChildAt(1);

			layout.setVisibility(View.VISIBLE);
			progressBar.setVisibility(View.GONE);
			textView.setVisibility(View.GONE);
			if(adapter == null || adapter.getItemCount() < 1)
				textView.setVisibility(View.VISIBLE);
		}
	};

	private Observer<ArrayList> getProductsObserver = products -> {
		if(products != null && !products.isEmpty()){
			showProducts(Common.copyArrayList(products));
		}else{
			ConstraintLayout layout = viewProducts.findViewById(R.id.progress_bar);
			ProgressBar progressBar = (ProgressBar) layout.getChildAt(0);
			TextView textView = (TextView) layout.getChildAt(1);

			layout.setVisibility(View.VISIBLE);
			progressBar.setVisibility(View.GONE);
			textView.setVisibility(View.GONE);
			if(adapter == null || adapter.getItemCount() < 1)
				textView.setVisibility(View.VISIBLE);
		}
	};

	private Observer<String> filterObserver = filter -> {
		if(filter != null){
			setAdapterFilter(filter);
		}
	};

	private Observer<Integer> getAddStatusObserver = status ->{
		if(status != null && status == 0){
			refreshProducts();
		}
	};

	public static ViewProducts newInstance() {
		return new ViewProducts();
	}

	@Override
	public View onCreateView(@NonNull LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {
		viewProducts = inflater.inflate(R.layout.view_products_fragment, container, false);
		recyclerView = viewProducts.findViewById(R.id.recyclerview);
		recyclerView.setItemAnimator(Common.getItemAnimator());
		swipeToRefresh = viewProducts.findViewById(R.id.swipeToRefresh);
		swipeToRefresh.setOnRefreshListener(this::refreshProducts);
		return viewProducts;
	}

	@Override
	public void onActivityCreated(@Nullable Bundle savedInstanceState) {
		super.onActivityCreated(savedInstanceState);
		isPurchases = getActivity() instanceof Purchases;
		purchasesVM = ViewModelProviders.of(this).get(com.herokuapp.pointofsale.models.pos.Purchases.class);
		salesVM = ViewModelProviders.of(this).get(com.herokuapp.pointofsale.models.pos.Sales.class);
		if(isPurchases){
			purchasesVM.getPurchaseProducts().observe(getViewLifecycleOwner(), getProductsObserver);
			purchasesVM.getCurrentUserDetails().observe(getViewLifecycleOwner(), getUserDataObserver);
			purchasesVM.getAddPurchaseStatus().observe(getViewLifecycleOwner(), getAddStatusObserver);
			((Purchases) Objects.requireNonNull(getActivity())).getFilter().observe(getViewLifecycleOwner(), filterObserver);
		}else{
			salesVM.getSaleProducts().observe(getViewLifecycleOwner(), getProductsObserver);
			salesVM.getCurrentUserDetails().observe(getViewLifecycleOwner(), getUserDataObserver);
			salesVM.getAddSaleStatus().observe(getViewLifecycleOwner(), getAddStatusObserver);
			((Sales) Objects.requireNonNull(getActivity())).getFilter().observe(getViewLifecycleOwner(), filterObserver);
		}
	}

	@Override
	public void onPause(){
		super.onPause();
		if(recyclerView.getLayoutManager() != null){
			recyclerViewState = new Bundle();
			Parcelable listState = recyclerView.getLayoutManager().onSaveInstanceState();
			recyclerViewState.putParcelable("state", listState);
		}
	}

	@Override
	public void onResume(){
		super.onResume();
		isPurchases = getActivity() instanceof Purchases;
		if(isPurchases && !((Purchases) Objects.requireNonNull(getActivity())).isSearchOpen()) {
			purchasesVM.fetchUserDetails();
		}else if(!isPurchases && !((Sales) Objects.requireNonNull(getActivity())).isSearchOpen()){
			salesVM.fetchUserDetails();
		}

		if (recyclerViewState != null && recyclerViewState.containsKey("state") && recyclerView.getLayoutManager() != null) {
			Parcelable listState = recyclerViewState.getParcelable("state");
			recyclerView.getLayoutManager().onRestoreInstanceState(listState);
		}
	}

	private void showProducts(ArrayList products) {
		ConstraintLayout layout = viewProducts.findViewById(R.id.progress_bar);
		layout.setVisibility(ConstraintLayout.GONE);
		if(adapter == null){
			adapter = new ProductsAdapter(Objects.requireNonNull(getActivity()), products, userDetails);
			recyclerView.setAdapter(Common.getAdapterAnimation(adapter));
			recyclerView.setLayoutManager(Common.getLayoutManager(getActivity()));
		}else{
			adapter.updateData(products);
		}
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
			((Purchases) Objects.requireNonNull(getActivity())).closeSearchView();
			purchasesVM.fetchUserDetails();
		}else{
			((Sales) Objects.requireNonNull(getActivity())).closeSearchView();
			salesVM.fetchUserDetails();
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
