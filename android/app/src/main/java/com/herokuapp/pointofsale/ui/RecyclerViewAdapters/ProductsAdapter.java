package com.herokuapp.pointofsale.ui.RecyclerViewAdapters;

import android.app.Activity;
import android.content.Intent;
import android.graphics.Color;
import androidx.annotation.NonNull;
import com.google.android.material.button.MaterialButton;
import com.google.android.material.card.MaterialCardView;

import androidx.annotation.Nullable;
import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentActivity;
import androidx.recyclerview.widget.DiffUtil;
import androidx.recyclerview.widget.RecyclerView;

import android.os.Bundle;
import android.text.TextUtils;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.google.gson.internal.LinkedTreeMap;
import com.herokuapp.pointofsale.R;
import com.herokuapp.pointofsale.ui.pos.PosDialog;
import com.herokuapp.pointofsale.ui.pos.Purchases;
import com.herokuapp.pointofsale.resources.Common;

import net.cachapa.expandablelayout.ExpandableLayout;

import java.util.ArrayList;
import java.util.Objects;

import static android.app.Activity.RESULT_OK;

public class ProductsAdapter extends
		RecyclerView.Adapter<ProductsAdapter.ViewHolder> {
	private ArrayList<LinkedTreeMap<String,String>> dataToView;
	private ArrayList<LinkedTreeMap<String,String>> dataFromDB;
	private LayoutInflater inflater;
	private LinkedTreeMap userdata;
	private Activity activity;
	private String filter;

	public ProductsAdapter(Activity activity, ArrayList<LinkedTreeMap<String,String>> dataFromDB, LinkedTreeMap userdata) {
		inflater = activity.getLayoutInflater();
		this.userdata = userdata;
		this.activity = activity;
		this.dataFromDB = dataFromDB;
		this.dataToView = dataFromDB;
		filter = "";
	}

	public void setFilter(String filter){
		if(dataFromDB == null || dataFromDB.size() < 1) return;
		this.filter = filter;
		ArrayList<LinkedTreeMap<String,String>> dataToView = new ArrayList<>();
		for (LinkedTreeMap<String, String> current : dataFromDB) {
			if(isFilterFound(current)) dataToView.add(current);
		}
		DiffUtil.DiffResult changes = DiffUtil.calculateDiff(getCallBack(dataToView), true);
		this.dataToView = dataToView;
		changes.dispatchUpdatesTo(this);
	}

	public void updateData(ArrayList<LinkedTreeMap<String,String>> dataToView){
		filter = "";
		this.dataToView = dataFromDB;
		DiffUtil.DiffResult changes = DiffUtil.calculateDiff(getCallBack(dataToView), true);
		this.dataToView = dataToView;
		dataFromDB = dataToView;
		changes.dispatchUpdatesTo(this);
	}

	public String getFilter(){
		return filter == null ? "" : filter;
	}

	private DiffUtil.Callback getCallBack(ArrayList<LinkedTreeMap<String,String>> newList){
		return new DiffUtil.Callback() {
			@Override
			public int getOldListSize() {
				return dataToView.size();
			}

			@Override
			public int getNewListSize() {
				return newList.size();
			}

			@Override
			public boolean areItemsTheSame(int oldItemPosition, int newItemPosition) {
				String oldID = dataToView.get(oldItemPosition).get("product_id");
				String newID = newList.get(newItemPosition).get("product_id");
				return oldID != null && newID != null && oldID.trim().equals(newID.trim());
			}

			@Override
			public boolean areContentsTheSame(int oldItemPosition, int newItemPosition) {
				String oldName = dataToView.get(oldItemPosition).get("product");
				String oldCategory = dataToView.get(oldItemPosition).get("category_name");
				String oldCost = dataToView.get(oldItemPosition).get("cost_per_unit");
				String oldInventory = dataToView.get(oldItemPosition).get("inventory_level");
				String oldTurnover = dataToView.get(oldItemPosition).get("inventory_turn_over");

				String newName = newList.get(newItemPosition).get("product");
				String newCategory = newList.get(newItemPosition).get("category_name");
				String newCost = newList.get(newItemPosition).get("cost_per_unit");
				String newInventory = newList.get(newItemPosition).get("inventory_level");
				String newTurnover = newList.get(newItemPosition).get("inventory_turn_over");

				return oldName != null && oldCategory != null && oldCost != null && oldInventory != null && oldTurnover != null
						&& oldName.equals(newName) && oldCategory.equals(newCategory) && oldCost.equals(newCost)
						&& oldInventory.equals(newInventory) && oldTurnover.equals(newTurnover);
			}

			@Nullable
			@Override
			public Object getChangePayload(int oldItemPosition, int newItemPosition) {
				return super.getChangePayload(oldItemPosition, newItemPosition);
			}
		};
	}

	private boolean isFilterFound(LinkedTreeMap<String, String> current){
		String name = Common.capitalize(Objects.requireNonNull(current.get("product")));
		String category = Common.capitalize(Objects.requireNonNull(current.get("category_name")));
		String cost = current.get("cost_per_unit");
		String inventory = current.get("inventory_level");
		String turnover = current.get("inventory_turn_over");

		if(filter != null && !filter.trim().isEmpty()){
			if(name.trim().toLowerCase().contains(filter.trim().toLowerCase()) || filter.trim().toLowerCase().contains(name.trim().toLowerCase())) return true;
			if(category.trim().toLowerCase().contains(filter.trim().toLowerCase()) || filter.trim().toLowerCase().contains(category.trim().toLowerCase())) return true;
			if(cost != null && (cost.trim().toLowerCase().contains(filter.trim().toLowerCase()) || filter.trim().toLowerCase().contains(cost.trim().toLowerCase()))) return true;
			if(inventory != null && (inventory.trim().toLowerCase().contains(filter.trim().toLowerCase()) || filter.trim().toLowerCase().contains(inventory.trim().toLowerCase()))) return true;
			return turnover != null && (turnover.trim().toLowerCase().contains(filter.trim().toLowerCase()) || filter.trim().toLowerCase().contains(turnover.trim().toLowerCase()));
		}
		return true;
	}

	@NonNull
	@Override
	public ProductsAdapter.ViewHolder onCreateViewHolder(@NonNull ViewGroup viewGroup, int i) {
		View itemView = inflater.inflate(R.layout.products_view, viewGroup, false);
		return new ViewHolder(itemView, this);
	}

	@Override
	public void onBindViewHolder(@NonNull ProductsAdapter.ViewHolder viewHolder, int i) {
		LinkedTreeMap<String, String> current = dataToView.get(i);
		String name = Common.capitalize(Objects.requireNonNull(current.get("product")));
		String category = Common.capitalize(Objects.requireNonNull(current.get("category_name")));
		String currencyCode = null;
		if(userdata != null){
			currencyCode = Objects.requireNonNull(userdata.get("currency_code")).toString();
		}
		String cost = Common.formatCurrency(currencyCode, current.get("cost_per_unit"));
		String inventory = Common.formatNumber(current.get("inventory_level"));
		String turnover = Common.formatNumber(current.get("inventory_turn_over"));
		String color = Common.getRandomDarkColor(i);
		viewHolder.profile.setBackgroundColor(Color.parseColor(color));
		viewHolder.profileLetter.setText(name.trim().isEmpty() ? "?" : Common.capitalize(name.substring(0,1)));
		viewHolder.name.setText(name);
		viewHolder.category.setText(category);
		viewHolder.cost.setText(cost);
		viewHolder.inventory.setText(inventory);
		viewHolder.turnover.setText(turnover);
		viewHolder.productID = current.get("product_id");
		viewHolder.product = name;
		viewHolder.categoryName = category;
		viewHolder.unitCost = current.get("cost_per_unit");
	}

	@Override
	public void onViewAttachedToWindow(@NonNull ProductsAdapter.ViewHolder viewHolder){
		super.onViewAttachedToWindow(viewHolder);
		viewHolder.name.setEllipsize(TextUtils.TruncateAt.MARQUEE);
		viewHolder.name.setSelected(true);
	}

	@Override
	public  void onViewDetachedFromWindow(@NonNull ProductsAdapter.ViewHolder viewHolder){
		if(viewHolder.layout.isExpanded()) viewHolder.expandView();
		viewHolder.name.setEllipsize(TextUtils.TruncateAt.END);
		viewHolder.name.setSelected(false);
		super.onViewDetachedFromWindow(viewHolder);
	}

	@Override
	public int getItemCount() {
		return dataToView.size();
	}

	class ViewHolder extends RecyclerView.ViewHolder{
		final TextView name;
		final TextView category;
		final TextView cost;
		final TextView inventory;
		final TextView turnover;
		final ProductsAdapter adapter;
		final ExpandableLayout layout;
		final ImageView profile;
		final TextView profileLetter;
		final MaterialButton button;
		String productID;
		String product;
		String categoryName;
		String unitCost;

		ViewHolder(View itemView, ProductsAdapter adapter) {
			super(itemView);
			name = itemView.findViewById(R.id.product_name);
			category = itemView.findViewById(R.id.product_category);
			cost = itemView.findViewById(R.id.product_cost);
			inventory = itemView.findViewById(R.id.product_inventory);
			turnover = itemView.findViewById(R.id.product_turnover);
			layout = itemView.findViewById(R.id.expandable_layout);
			profile = itemView.findViewById(R.id.profile_image);
			profileLetter = itemView.findViewById(R.id.profile_letter);
			productID = "";
			product = "";
			categoryName = "";
			unitCost = "";
			button = itemView.findViewById(R.id.show_more);
			button.setOnClickListener(v -> expandView());
			((RelativeLayout)name.getParent()).setOnClickListener(v -> button.performClick());
			((MaterialCardView)profile.getParent().getParent().getParent()).setOnClickListener(v -> {
				//To Be Implemented
				if(activity instanceof Purchases){
					Fragment fragment = Common.getFragmentByTag((FragmentActivity) activity, R.id.view_pager, 1);
					Intent intent = new Intent(fragment.getActivity(), PosDialog.class);
					intent.putExtra("product_name", product);
					intent.putExtra("product_id", productID);
					intent.putExtra("category", categoryName);
					intent.putExtra("unit_cost", unitCost);
					intent.putExtra("update", "0");
					intent.putExtra("isPurchase", true);
					intent.putExtra("currencyCode", userdata != null ? Objects.requireNonNull(userdata.get("currency_code")).toString() : null);
					fragment.startActivityForResult(intent, 1);
				}else{
					Fragment fragment = Common.getFragmentByTag((FragmentActivity) activity, R.id.view_pager, 1);
					Intent intent = new Intent();
					Bundle extras = new Bundle();
					extras.putString("product", product);
					extras.putString("product_id", productID);
					extras.putString("category_name", categoryName);
					extras.putString("cost", unitCost);
					extras.putString("amount", "1");
					extras.putString("discount", "0");
					extras.putString("unit_cost", unitCost);
					intent.putExtras(extras);
					fragment.onActivityResult(1, RESULT_OK, intent);
				}
			});
			this.adapter = adapter;
		}

		void expandView(){
			if(layout.isExpanded()){
				Common.rotateElement(button, 180f, 0f, 300);
			}else{
				Common.rotateElement(button, 0f, 180f, 300);
			}
			layout.toggle();
		}
	}
}
