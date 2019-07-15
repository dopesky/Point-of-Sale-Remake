package com.herokuapp.pointofsale.ui.pos.fragments;

import androidx.lifecycle.Observer;

import android.content.Intent;
import android.os.Bundle;
import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.constraintlayout.widget.ConstraintLayout;
import androidx.fragment.app.Fragment;
import androidx.lifecycle.ViewModelProviders;
import androidx.recyclerview.widget.RecyclerView;

import android.os.Parcelable;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import com.google.android.material.floatingactionbutton.ExtendedFloatingActionButton;
import com.google.gson.internal.LinkedTreeMap;
import com.herokuapp.pointofsale.R;
import com.herokuapp.pointofsale.ui.RecyclerViewAdapters.CartAdapter;
import com.herokuapp.pointofsale.ui.pos.Checkout;
import com.herokuapp.pointofsale.ui.pos.Purchases;
import com.herokuapp.pointofsale.ui.pos.Sales;
import com.herokuapp.pointofsale.ui.resources.Common;
import com.herokuapp.pointofsale.ui.resources.CustomToast;
import com.mikepenz.fontawesome_typeface_library.FontAwesome;
import com.mikepenz.iconics.IconicsDrawable;

import java.util.ArrayList;
import java.util.Objects;

import static android.app.Activity.RESULT_OK;

public class ViewCart extends Fragment {

	private com.herokuapp.pointofsale.models.pos.Purchases purchasesVM;
	private com.herokuapp.pointofsale.models.pos.Sales salesVM;

	private boolean isPurchases;
	private boolean isAdding;
	private int netCost;

	private View viewProducts;
	private RecyclerView recyclerView;
	private ExtendedFloatingActionButton button;
	private Bundle recyclerViewState;

	private CartAdapter adapter;
	private LinkedTreeMap userDetails;

	private Observer<LinkedTreeMap> getUserDataObserver = userdata -> {
		if(userdata != null && !userdata.isEmpty()) {
			userDetails = userdata;
		}
	};

	private Observer<ArrayList> getProductsObserver = products -> {
		if(products != null){
			showProducts(Common.copyArrayList(products));
		}else{
			ConstraintLayout layout = viewProducts.findViewById(R.id.progress_bar);
			TextView textView = (TextView) layout.getChildAt(0);
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
			if(isPurchases){
				purchasesVM.addSelectedData(new ArrayList<>());
				((Purchases) Objects.requireNonNull(getActivity())).refreshToolbarNumber(0);
				((Purchases) Objects.requireNonNull(getActivity())).closeSearchView();
				CustomToast.showToast(getActivity(), " Purchases Successfully Made!", "success");
				purchasesVM.fetchPurchaseProducts();
			}else{
				salesVM.addSelectedData(new ArrayList<>());
				((Sales) Objects.requireNonNull(getActivity())).refreshToolbarNumber(0);
				((Sales) Objects.requireNonNull(getActivity())).closeSearchView();
				CustomToast.showToast(getActivity(), " Sales Successfully Made!", "success");
				salesVM.fetchSaleProducts();
			}
		}
		button.setAlpha(1.0f);
		button.setText(R.string.checkout);
		isAdding = false;
	};

	private Observer<String> getAddErrorObserver = error ->{
		if(error != null && !error.trim().isEmpty()){
			CustomToast.showToast(getActivity(), " " + error, "danger");
		}
	};

	public static ViewCart newInstance() {
		return new ViewCart();
	}

	@Override
	public void onCreate(Bundle savedInstanceState){
		super.onCreate(savedInstanceState);
		isAdding = false;
		netCost = 0;
	}

	@Override
	public View onCreateView(@NonNull LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {
		viewProducts = inflater.inflate(R.layout.view_cart_fragment, container, false);
		button = viewProducts.findViewById(R.id.checkout_button);
		button.setIcon(new IconicsDrawable(Objects.requireNonNull(getActivity())).icon(FontAwesome.Icon.faw_sign_out_alt));
		button.setOnClickListener(this::checkout);
		recyclerView = viewProducts.findViewById(R.id.recyclerview);
		recyclerView.setItemAnimator(Common.getItemAnimator());
		recyclerView.setNestedScrollingEnabled(false);

		ConstraintLayout layout = viewProducts.findViewById(R.id.progress_bar);
		layout.setVisibility(ConstraintLayout.VISIBLE);
		TextView textView = (TextView) layout.getChildAt(0);
		textView.setVisibility(adapter == null || adapter.getItemCount() < 1 ? View.VISIBLE : View.GONE);
		return viewProducts;
	}

	@Override
	public void onActivityCreated(@Nullable Bundle savedInstanceState) {
		super.onActivityCreated(savedInstanceState);
		isPurchases = getActivity() instanceof Purchases;
		purchasesVM = ViewModelProviders.of(this).get(com.herokuapp.pointofsale.models.pos.Purchases.class);
		salesVM = ViewModelProviders.of(this).get(com.herokuapp.pointofsale.models.pos.Sales.class);
		if(isPurchases){
			ArrayList list = purchasesVM.getSelectedData().getValue();
			purchasesVM.getCurrentUserDetails().observe(getViewLifecycleOwner(), getUserDataObserver);
			purchasesVM.getSelectedData().observe(getViewLifecycleOwner(), getProductsObserver);
			purchasesVM.getAddPurchaseStatus().observe(getViewLifecycleOwner(), getAddStatusObserver);
			purchasesVM.getAddPurchaseError().observe(getViewLifecycleOwner(), getAddErrorObserver);
			((Purchases) Objects.requireNonNull(getActivity())).getFilter().observe(getViewLifecycleOwner(), filterObserver);
			((Purchases)getActivity()).refreshToolbarNumber(list == null ? 0 : list.size());
			purchasesVM.fetchUserDetails();
		}else{
			ArrayList list = salesVM.getSelectedData().getValue();
			salesVM.getCurrentUserDetails().observe(getViewLifecycleOwner(), getUserDataObserver);
			salesVM.getSelectedData().observe(getViewLifecycleOwner(), getProductsObserver);
			salesVM.getAddSaleStatus().observe(getViewLifecycleOwner(), getAddStatusObserver);
			salesVM.getAddSaleError().observe(getViewLifecycleOwner(), getAddErrorObserver);
			((Sales) Objects.requireNonNull(getActivity())).getFilter().observe(getViewLifecycleOwner(), filterObserver);
			((Sales)getActivity()).refreshToolbarNumber(list == null ? 0 : list.size());
			salesVM.fetchUserDetails();
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
		if (recyclerViewState != null && recyclerViewState.containsKey("state") && recyclerView.getLayoutManager() != null) {
			Parcelable listState = recyclerViewState.getParcelable("state");
			recyclerView.getLayoutManager().onRestoreInstanceState(listState);
		}
	}

	@Override
	public void onActivityResult(int requestCode, int resultCode, Intent data){
		super.onActivityResult(requestCode, resultCode, data);
		if(isPurchases){
			purchasesEvents(requestCode, resultCode, data);
		}else{
			salesEvents(requestCode, resultCode, data);
		}
	}

	private void purchasesEvents(int requestCode, int resultCode, Intent data){
		if(requestCode == 1 && resultCode == RESULT_OK){
			ArrayList<LinkedTreeMap<String, String>> list;
			Bundle bundle = Objects.requireNonNull(data.getExtras());
			if(purchasesVM.getSelectedData().getValue() == null){
				list = new ArrayList<>();
			}else{
				list = purchasesVM.getSelectedData().getValue();
			}
			int found = -1;
			for (int i = 0; i < list.size(); i++) {
				LinkedTreeMap<String, String> item = list.get(i);
				String originalID = Objects.requireNonNull(item.get("product_id"));
				String newID = bundle.getString("product_id", "");
				if (Integer.parseInt(originalID.trim()) == Integer.parseInt(newID.trim())) {
					int quantity = Integer.parseInt(Objects.requireNonNull(item.get("amount")))
							+ Integer.parseInt(bundle.getString("amount", "0"));
					int cost = Integer.parseInt(Objects.requireNonNull(item.get("cost")))
							+ Integer.parseInt(bundle.getString("cost", "0"));
					int discount = Integer.parseInt(Objects.requireNonNull(item.get("discount")))
							+ Integer.parseInt(bundle.getString("discount", "0"));
					item.put("amount", String.valueOf(quantity));
					item.put("cost", String.valueOf(cost));
					item.put("discount", String.valueOf(discount));
					found = 1;
					break;
				}
			}

			if(found == -1){
				LinkedTreeMap<String, String> map = new LinkedTreeMap<>();
				for(String key : bundle.keySet()){
					map.put(key, Objects.requireNonNull(data.getExtras().get(key)).toString());
				}
				list.add(map);
			}
			purchasesVM.addSelectedData(list);
			((Purchases) Objects.requireNonNull(getActivity())).refreshToolbarNumber(purchasesVM.getSelectedData().getValue().size());
		}

		if(requestCode == 2 && resultCode == 2){
			ArrayList<LinkedTreeMap<String, String>> list;
			Bundle bundle = Objects.requireNonNull(data.getExtras());
			if(purchasesVM.getSelectedData().getValue() != null){
				list = purchasesVM.getSelectedData().getValue();
				for (int i = 0; i < list.size(); i++) {
					LinkedTreeMap<String, String> item = list.get(i);
					String originalID = Objects.requireNonNull(item.get("product_id"));
					String newID = bundle.getString("product_id", "");
					if (Integer.parseInt(originalID.trim()) == Integer.parseInt(newID.trim())) {
						list.remove(i);
						break;
					}
				}
				purchasesVM.addSelectedData(list);
				((Purchases) Objects.requireNonNull(getActivity())).refreshToolbarNumber(purchasesVM.getSelectedData().getValue().size());
			}
		}

		if(requestCode == 2 && resultCode == RESULT_OK){
			ArrayList<LinkedTreeMap<String, String>> list;
			Bundle bundle = Objects.requireNonNull(data.getExtras());
			if(purchasesVM.getSelectedData().getValue() != null){
				list = purchasesVM.getSelectedData().getValue();
				for (int i = 0; i < list.size(); i++) {
					LinkedTreeMap<String, String> item = list.get(i);
					String originalID = Objects.requireNonNull(item.get("product_id"));
					String newID = bundle.getString("product_id", "");
					if (Integer.parseInt(originalID.trim()) == Integer.parseInt(newID.trim())) {
						item.put("amount", bundle.getString("amount", item.get("amount")));
						item.put("cost", bundle.getString("cost", item.get("cost")));
						item.put("discount", bundle.getString("discount", item.get("discount")));
						break;
					}
				}
				purchasesVM.addSelectedData(list);
				((Purchases) Objects.requireNonNull(getActivity())).refreshToolbarNumber(purchasesVM.getSelectedData().getValue().size());
			}
		}

		if(requestCode == 3 && resultCode == RESULT_OK){
			Bundle bundle = Objects.requireNonNull(data.getExtras());
			if(isAdding) return;
			button.setAlpha(0.6f);
			button.setText(R.string.purchasing);
			isAdding = true;
			purchasesVM.addPurchases(bundle.getString("method_id", "-1"));
		}
	}

	private void salesEvents(int requestCode, int resultCode, Intent data){
		if(requestCode == 1 && resultCode == RESULT_OK){
			ArrayList<LinkedTreeMap<String, String>> list;
			Bundle bundle = Objects.requireNonNull(data.getExtras());
			if(salesVM.getSelectedData().getValue() == null){
				list = new ArrayList<>();
			}else{
				list = salesVM.getSelectedData().getValue();
			}
			int found = -1;
			for (int i = 0; i < list.size(); i++) {
				LinkedTreeMap<String, String> item = list.get(i);
				String originalID = Objects.requireNonNull(item.get("product_id"));
				String newID = bundle.getString("product_id", "");
				if (Integer.parseInt(originalID.trim()) == Integer.parseInt(newID.trim())) {
					int quantity = Integer.parseInt(Objects.requireNonNull(item.get("amount")))
							+ Integer.parseInt(bundle.getString("amount", "0"));
					int cost = Integer.parseInt(Objects.requireNonNull(item.get("cost")))
							+ Integer.parseInt(bundle.getString("cost", "0"));
					int discount = Integer.parseInt(Objects.requireNonNull(item.get("discount")))
							+ Integer.parseInt(bundle.getString("discount", "0"));
					item.put("amount", String.valueOf(quantity));
					item.put("cost", String.valueOf(cost));
					item.put("discount", String.valueOf(discount));
					found = 1;
					break;
				}
			}

			if(found == -1){
				LinkedTreeMap<String, String> map = new LinkedTreeMap<>();
				for(String key : bundle.keySet()){
					map.put(key, Objects.requireNonNull(data.getExtras().get(key)).toString());
				}
				list.add(map);
			}
			salesVM.addSelectedData(list);
			((Sales) Objects.requireNonNull(getActivity())).refreshToolbarNumber(salesVM.getSelectedData().getValue().size());
		}

		if(requestCode == 2 && resultCode == 2){
			ArrayList<LinkedTreeMap<String, String>> list;
			Bundle bundle = Objects.requireNonNull(data.getExtras());
			if(salesVM.getSelectedData().getValue() != null){
				list = salesVM.getSelectedData().getValue();
				for (int i = 0; i < list.size(); i++) {
					LinkedTreeMap<String, String> item = list.get(i);
					String originalID = Objects.requireNonNull(item.get("product_id"));
					String newID = bundle.getString("product_id", "");
					if (Integer.parseInt(originalID.trim()) == Integer.parseInt(newID.trim())) {
						list.remove(i);
						break;
					}
				}
				salesVM.addSelectedData(list);
				((Sales) Objects.requireNonNull(getActivity())).refreshToolbarNumber(salesVM.getSelectedData().getValue().size());
			}
		}

		if(requestCode == 2 && resultCode == RESULT_OK){
			ArrayList<LinkedTreeMap<String, String>> list;
			Bundle bundle = Objects.requireNonNull(data.getExtras());
			if(salesVM.getSelectedData().getValue() != null){
				list = salesVM.getSelectedData().getValue();
				for (int i = 0; i < list.size(); i++) {
					LinkedTreeMap<String, String> item = list.get(i);
					String originalID = Objects.requireNonNull(item.get("product_id"));
					String newID = bundle.getString("product_id", "");
					if (Integer.parseInt(originalID.trim()) == Integer.parseInt(newID.trim())) {
						item.put("amount", bundle.getString("amount", item.get("amount")));
						item.put("cost", bundle.getString("cost", item.get("cost")));
						item.put("discount", bundle.getString("discount", item.get("discount")));
						break;
					}
				}
				salesVM.addSelectedData(list);
				((Sales) Objects.requireNonNull(getActivity())).refreshToolbarNumber(salesVM.getSelectedData().getValue().size());
			}
		}

		if(requestCode == 3 && resultCode == RESULT_OK){
			Bundle bundle = Objects.requireNonNull(data.getExtras());
			if(isAdding) return;
			button.setAlpha(0.6f);
			button.setText(R.string.selling);
			isAdding = true;
			salesVM.addSales(bundle.getString("method_id", "-1"));
		}
	}

	private void showProducts(ArrayList products) {
		if(adapter == null){
			adapter = new CartAdapter(Objects.requireNonNull(getActivity()), products, userDetails);
			recyclerView.setAdapter(Common.getAdapterAnimation(adapter));
			recyclerView.setLayoutManager(Common.getLayoutManager(getActivity()));
		}else{
			adapter.updateData(products);
		}
		ConstraintLayout layout = viewProducts.findViewById(R.id.progress_bar);
		layout.setVisibility(adapter.getItemCount() < 1 ? ConstraintLayout.VISIBLE : ConstraintLayout.GONE);
		TextView textView = (TextView) layout.getChildAt(0);
		textView.setVisibility(adapter.getItemCount() < 1 ? View.VISIBLE : View.GONE);
		updateSummary(products);
	}

	private void updateSummary(ArrayList products){
		TextView gross = viewProducts.findViewById(R.id.gross_cost);
		TextView discount = viewProducts.findViewById(R.id.discount_cost);
		TextView net = viewProducts.findViewById(R.id.net_cost);
		int discountCost = 0;
		int netCost = 0;
		for(Object item: products){
			netCost += Integer.parseInt(Objects.requireNonNull(((LinkedTreeMap) item).get("cost")).toString());
			discountCost += Integer.parseInt(Objects.requireNonNull(((LinkedTreeMap) item).get("discount")).toString());
		}

		gross.setText(Common.formatCurrency(Objects.requireNonNull(userDetails.get("currency_code")).toString(), String.valueOf(netCost + discountCost)));
		discount.setText(Common.formatCurrency(Objects.requireNonNull(userDetails.get("currency_code")).toString(), String.valueOf(discountCost)));
		net.setText(Common.formatCurrency(Objects.requireNonNull(userDetails.get("currency_code")).toString(), String.valueOf(netCost)));
		this.netCost = netCost;
	}

	private void setAdapterFilter(String filter) {
		if(adapter != null){
			adapter.setFilter(filter);
			ConstraintLayout layout = viewProducts.findViewById(R.id.progress_bar);
			layout.setVisibility(ConstraintLayout.VISIBLE);
			TextView textView = (TextView) layout.getChildAt(0);
			textView.setVisibility(adapter.getItemCount() < 1 ? View.VISIBLE : View.GONE);
		}
	}

	private void checkout(View view){
		if(isPurchases && (purchasesVM.getSelectedData().getValue() == null || purchasesVM.getSelectedData().getValue().isEmpty())){
			CustomToast.showToast(getActivity(), " Please Make a Purchase First!", "danger");
			Common.shakeElement(getActivity(), view);
			return;
		}
		if(!isPurchases && (salesVM.getSelectedData().getValue() == null || salesVM.getSelectedData().getValue().isEmpty())){
			CustomToast.showToast(getActivity(), " Please Make a Sale First!", "danger");
			Common.shakeElement(getActivity(), view);
			return;
		}
		Intent intent = new Intent(getActivity(), Checkout.class);
		intent.putExtra("net_cost", String.valueOf(netCost));
		intent.putExtra("currencyCode", userDetails != null ? Objects.requireNonNull(userDetails.get("currency_code")).toString() : null);
		intent.putExtra("isPurchase", getActivity() instanceof Purchases);
		if(getActivity() instanceof Purchases) intent.putExtra("amount_paid", String.valueOf(netCost));
		startActivityForResult(intent, 3);
	}

}
