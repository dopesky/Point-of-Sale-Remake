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
import com.herokuapp.pointofsale.ui.resources.Common;

import net.cachapa.expandablelayout.ExpandableLayout;

import java.util.ArrayList;
import java.util.Objects;

public class CartAdapter extends
		RecyclerView.Adapter<CartAdapter.ViewHolder> {
	private ArrayList<LinkedTreeMap<String,String>> dataToView;
	private ArrayList<LinkedTreeMap<String,String>> dataFromDB;
	private LayoutInflater inflater;
	private LinkedTreeMap userdata;
	private Activity activity;
	private String filter;

	public CartAdapter(Activity context, ArrayList<LinkedTreeMap<String,String>> dataFromDB, LinkedTreeMap userdata) {
		inflater = context.getLayoutInflater();
		this.activity = context;
		this.userdata = userdata;
		this.dataFromDB = dataFromDB;
		this.dataToView = dataFromDB;
		filter = "";
	}

	public String getFilter(){
		return filter == null ? "" : filter;
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

	public void updateData(final ArrayList<LinkedTreeMap<String,String>> dataToView){
		filter = "";
		this.dataToView = dataFromDB;
		DiffUtil.DiffResult changes = DiffUtil.calculateDiff(getCallBack(dataToView), true);
		this.dataToView = dataToView;
		dataFromDB = dataToView;
		changes.dispatchUpdatesTo(this);
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
				String oldCost = dataToView.get(oldItemPosition).get("cost");
				String oldInventory = dataToView.get(oldItemPosition).get("amount");
				String oldTurnover = dataToView.get(oldItemPosition).get("discount");
				String oldUnitCost = dataToView.get(oldItemPosition).get("unit_cost");

				String newName = newList.get(newItemPosition).get("product");
				String newCategory = newList.get(newItemPosition).get("category_name");
				String newCost = newList.get(newItemPosition).get("cost");
				String newInventory = newList.get(newItemPosition).get("amount");
				String newTurnover = newList.get(newItemPosition).get("discount");
				String newUnitCost = newList.get(newItemPosition).get("unit_cost");

				return oldName != null && oldCategory != null && oldCost != null && oldInventory != null && oldTurnover != null
						&& (oldUnitCost != null && newUnitCost != null)
						&& oldName.equals(newName) && oldCategory.equals(newCategory) && oldCost.equals(newCost)
						&& oldInventory.equals(newInventory) && oldTurnover.equals(newTurnover) && oldUnitCost.equals(newUnitCost);
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
		String cost = current.get("cost");
		String amount = current.get("amount");
		String discount = current.get("discount");

		if(filter != null && !filter.trim().isEmpty()){
			if(name.trim().toLowerCase().contains(filter.trim().toLowerCase()) || filter.trim().toLowerCase().contains(name.trim().toLowerCase())) return true;
			if(category.trim().toLowerCase().contains(filter.trim().toLowerCase()) || filter.trim().toLowerCase().contains(category.trim().toLowerCase())) return true;
			if(cost != null && (cost.trim().toLowerCase().contains(filter.trim().toLowerCase()) || filter.trim().toLowerCase().contains(cost.trim().toLowerCase()))) return true;
			if(amount != null && (amount.trim().toLowerCase().contains(filter.trim().toLowerCase()) || filter.trim().toLowerCase().contains(amount.trim().toLowerCase()))) return true;
			return discount != null && (discount.trim().toLowerCase().contains(filter.trim().toLowerCase()) || filter.trim().toLowerCase().contains(discount.trim().toLowerCase()));
		}
		return true;
	}

	@NonNull
	@Override
	public CartAdapter.ViewHolder onCreateViewHolder(@NonNull ViewGroup viewGroup, int i) {
		View itemView = inflater.inflate(R.layout.cart_view, viewGroup, false);
		return new ViewHolder(itemView, this);
	}

	@Override
	public void onBindViewHolder(@NonNull CartAdapter.ViewHolder viewHolder, int i) {
		LinkedTreeMap<String, String> current = dataToView.get(i);
		String name = Common.capitalize(Objects.requireNonNull(current.get("product")));
		String category = Common.capitalize(Objects.requireNonNull(current.get("category_name")));
		String currencyCode = null;
		if(userdata != null){
			currencyCode = Objects.requireNonNull(userdata.get("currency_code")).toString();
		}
		String cost = Common.formatCurrency(currencyCode, current.get("cost"));
		String amount = Common.formatNumber(current.get("amount"));
		String discount = Common.formatCurrency(currencyCode, current.get("discount"));
		String color = Common.getRandomDarkColor(i);
		viewHolder.profile.setBackgroundColor(Color.parseColor(color));
		viewHolder.profileLetter.setText(name.trim().isEmpty() ? "?" : Common.capitalize(name.substring(0,1)));
		viewHolder.name.setText(name);
		viewHolder.name.setEllipsize(TextUtils.TruncateAt.MARQUEE);
		viewHolder.name.setSelected(true);
		viewHolder.category.setText(category);
		viewHolder.cost.setText(cost);
		viewHolder.amount.setText(amount);
		viewHolder.discount.setText(discount);
		viewHolder.bundle.putString("product_name", name);
		viewHolder.bundle.putString("product_id", current.get("product_id"));
		viewHolder.bundle.putString("category", category);
		viewHolder.bundle.putString("update", "1");
		viewHolder.bundle.putBoolean("isPurchase", activity instanceof Purchases);
		viewHolder.bundle.putString("amount", current.get("amount"));
		viewHolder.bundle.putString("cost", current.get("cost"));
		viewHolder.bundle.putString("discount", current.get("discount"));
		viewHolder.bundle.putString("unit_cost", current.get("unit_cost"));
	}

	@Override
	public  void onViewDetachedFromWindow(@NonNull CartAdapter.ViewHolder viewHolder){
		if(viewHolder.layout.isExpanded()) viewHolder.expandView(viewHolder.itemView.findViewById(R.id.show_more));
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
		final TextView amount;
		final TextView discount;
		final CartAdapter adapter;
		final ExpandableLayout layout;
		final ImageView profile;
		final TextView profileLetter;
		final Bundle bundle;

		ViewHolder(View itemView, CartAdapter adapter) {
			super(itemView);
			name = itemView.findViewById(R.id.product_name);
			category = itemView.findViewById(R.id.product_category);
			cost = itemView.findViewById(R.id.product_cost);
			amount = itemView.findViewById(R.id.product_amount);
			discount = itemView.findViewById(R.id.product_discount);
			layout = itemView.findViewById(R.id.expandable_layout);
			profile = itemView.findViewById(R.id.profile_image);
			profileLetter = itemView.findViewById(R.id.profile_letter);
			bundle = new Bundle();
			MaterialButton button = itemView.findViewById(R.id.show_more);
			button.setOnClickListener(v -> expandView(button));
			((RelativeLayout)name.getParent()).setOnClickListener(v -> button.performClick());
			((MaterialCardView)profile.getParent().getParent().getParent()).setOnClickListener(v -> {
				Fragment fragment = Common.getFragmentByTag((FragmentActivity) activity, R.id.view_pager, 1);
				Intent intent = new Intent(fragment.getActivity(), PosDialog.class);
				intent.putExtras(bundle);
				fragment.startActivityForResult(intent, 2);
			});
			this.adapter = adapter;
		}

		void expandView(MaterialButton button){
			if(layout.isExpanded()){
				Common.rotateElement(button, 180f, 0f, 300);
			}else{
				Common.rotateElement(button, 0f, 180f, 300);
			}
			layout.toggle(true);
		}
	}
}
