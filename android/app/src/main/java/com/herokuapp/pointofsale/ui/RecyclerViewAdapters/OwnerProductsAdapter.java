package com.herokuapp.pointofsale.ui.RecyclerViewAdapters;

import android.app.Activity;
import android.content.Intent;
import android.graphics.Color;
import android.os.Bundle;
import androidx.annotation.NonNull;
import com.google.android.material.button.MaterialButton;
import com.google.android.material.card.MaterialCardView;

import androidx.annotation.Nullable;
import androidx.core.app.ActivityCompat;
import androidx.core.app.ActivityOptionsCompat;
import androidx.recyclerview.widget.DiffUtil;
import androidx.recyclerview.widget.RecyclerView;
import android.text.TextUtils;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.google.gson.internal.LinkedTreeMap;
import com.herokuapp.pointofsale.R;
import com.herokuapp.pointofsale.ui.owner.EditProductDetails;
import com.herokuapp.pointofsale.ui.owner.ManageProducts;
import com.herokuapp.pointofsale.resources.Common;
import com.herokuapp.pointofsale.resources.CustomToast;

import net.cachapa.expandablelayout.ExpandableLayout;

import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.Locale;
import java.util.Objects;

public class OwnerProductsAdapter extends
		RecyclerView.Adapter<OwnerProductsAdapter.ViewHolder> {
	private ArrayList<LinkedTreeMap<String,String>> dataToView;
	private ArrayList<LinkedTreeMap<String,String>> dataFromDB;
	private LayoutInflater inflater;
	private Activity activity;
	private String filter;

	public OwnerProductsAdapter(Activity context, ArrayList<LinkedTreeMap<String,String>> dataFromDB) {
		activity = context;
		inflater = LayoutInflater.from(context);
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

	public void updateData(ArrayList<LinkedTreeMap<String,String>> dataToView){
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
				String oldCost = dataToView.get(oldItemPosition).get("cost_per_unit");
				String oldInventory = dataToView.get(oldItemPosition).get("status");
				String oldTurnover = dataToView.get(oldItemPosition).get("modified_date");
				String oldUnitCost = dataToView.get(oldItemPosition).get("category_id");
				String oldStates = dataToView.get(oldItemPosition).get("active") + dataToView.get(oldItemPosition).get("suspended") +
						dataToView.get(oldItemPosition).get("owner_suspended") + dataToView.get(oldItemPosition).get("owner_active");

				String newName = newList.get(newItemPosition).get("product");
				String newCategory = newList.get(newItemPosition).get("category_name");
				String newCost = newList.get(newItemPosition).get("cost_per_unit");
				String newInventory = newList.get(newItemPosition).get("status");
				String newTurnover = newList.get(newItemPosition).get("modified_date");
				String newUnitCost = newList.get(newItemPosition).get("category_id");
				String newStates = newList.get(newItemPosition).get("active") + newList.get(newItemPosition).get("suspended") +
						newList.get(newItemPosition).get("owner_suspended") + newList.get(newItemPosition).get("owner_active");

				return oldName != null && oldCategory != null && oldCost != null && oldInventory != null
						&& oldTurnover != null && oldUnitCost != null && !oldStates.toLowerCase().contains("null")
						&& oldName.equals(newName) && oldCategory.equals(newCategory) && oldCost.equals(newCost)
						&& oldInventory.equals(newInventory) && oldTurnover.equals(newTurnover) && oldUnitCost.equals(newUnitCost)
						&& oldStates.equals(newStates);
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
		String status = current.get("status");
		String time = current.get("modified_date");
		SimpleDateFormat format = new SimpleDateFormat("yyyy-MM-dd hh:mm:ss", Locale.getDefault());
		try {
			Date date = format.parse(time);
			format.applyPattern("dd MMM yyyy • hh:mma");
			time = format.format(date);
		} catch (ParseException e) {System.out.println(e.getMessage());}

		if(filter != null && !filter.trim().isEmpty()){
			if(name.trim().toLowerCase().contains(filter.trim().toLowerCase()) || filter.trim().toLowerCase().contains(name.trim().toLowerCase())) return true;
			if(category.trim().toLowerCase().contains(filter.trim().toLowerCase()) || filter.trim().toLowerCase().contains(category.trim().toLowerCase())) return true;
			if(cost != null && (cost.trim().toLowerCase().contains(filter.trim().toLowerCase()) || filter.trim().toLowerCase().contains(cost.trim().toLowerCase()))) return true;
			if(status != null && (status.trim().toLowerCase().contains(filter.trim().toLowerCase()) || filter.trim().toLowerCase().contains(status.trim().toLowerCase()))) return true;
			return time != null && (time.trim().toLowerCase().contains(filter.trim().toLowerCase()) || filter.trim().toLowerCase().contains(time.trim().toLowerCase()));
		}
		return true;
	}

	@NonNull
	@Override
	public OwnerProductsAdapter.ViewHolder onCreateViewHolder(@NonNull ViewGroup viewGroup, int i) {
		View itemView = inflater.inflate(R.layout.owner_products, viewGroup, false);
		return new ViewHolder(itemView, this);
	}

	@Override
	public void onBindViewHolder(@NonNull OwnerProductsAdapter.ViewHolder viewHolder, int i) {
		LinkedTreeMap<String, String> current = dataToView.get(i);
		String name = Common.capitalize(Objects.requireNonNull(current.get("product")));
		String category = Common.capitalize(Objects.requireNonNull(current.get("category_name")));
		LinkedTreeMap userdata = Objects.requireNonNull(((ManageProducts) activity).getOwnerViewModel().getValue())
				.getCurrentUserDetails().getValue();
		String currencyCode = null;
		if(userdata != null){
			currencyCode = Objects.requireNonNull(userdata.get("currency_code")).toString();
		}
		String cost = Common.formatCurrency(currencyCode, current.get("cost_per_unit"));
		String status = current.get("status");
		String time = current.get("modified_date");
		SimpleDateFormat format = new SimpleDateFormat("yyyy-MM-dd hh:mm:ss", Locale.getDefault());
		try {
			Date date = format.parse(time);
			format.applyPattern("dd MMM yyyy • hh:mma");
			time = format.format(date);
		} catch (ParseException e) {System.out.println(e.getMessage());}
		String color = Common.getRandomDarkColor(i);
		viewHolder.profile.setBackgroundColor(Color.parseColor(color));
		viewHolder.profileLetter.setText(name.trim().isEmpty() ? "?" : Common.capitalize(name.substring(0,1)));
		viewHolder.currentBundle.putString("profile", color);
		viewHolder.name.setText(name);
		viewHolder.currentBundle.putString("product", name);
		viewHolder.category.setText(category);
		viewHolder.currentBundle.putString("category", current.get("category_id"));
		viewHolder.cost.setText(cost);
		viewHolder.currentBundle.putString("cost", current.get("cost_per_unit"));
		viewHolder.status.setText(status);
		viewHolder.time.setText(time);
		viewHolder.currentBundle.putBoolean("active", Integer.parseInt(Objects.requireNonNull(current.get("active"))) == 1);
		boolean suspended = Integer.parseInt(Objects.requireNonNull(current.get("suspended"))) == 1
				|| Integer.parseInt(Objects.requireNonNull(current.get("owner_suspended"))) == 1
				|| Integer.parseInt(Objects.requireNonNull(current.get("owner_active"))) == 0;
		viewHolder.currentBundle.putBoolean("suspended", suspended);
		viewHolder.currentBundle.putInt("product_id", Integer.parseInt(Objects.requireNonNull(current.get("product_id"))));
	}

	@Override
	public void onViewAttachedToWindow(@NonNull OwnerProductsAdapter.ViewHolder viewHolder){
		super.onViewAttachedToWindow(viewHolder);
		viewHolder.name.setEllipsize(TextUtils.TruncateAt.MARQUEE);
		viewHolder.name.setSelected(true);
	}

	@Override
	public void onViewDetachedFromWindow(@NonNull OwnerProductsAdapter.ViewHolder viewHolder){
		if(viewHolder.layout.isExpanded()) viewHolder.expandView(viewHolder.itemView.findViewById(R.id.show_more));
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
		final TextView status;
		final TextView time;
		final OwnerProductsAdapter adapter;
		final ExpandableLayout layout;
		final ImageView profile;
		final TextView profileLetter;
		final Bundle currentBundle;

		ViewHolder(View itemView, OwnerProductsAdapter adapter) {
			super(itemView);
			name = itemView.findViewById(R.id.product_name);
			category = itemView.findViewById(R.id.product_category);
			cost = itemView.findViewById(R.id.product_cost);
			status = itemView.findViewById(R.id.product_status);
			time = itemView.findViewById(R.id.product_time);
			layout = itemView.findViewById(R.id.expandable_layout);
			profile = itemView.findViewById(R.id.profile_image);
			profileLetter = itemView.findViewById(R.id.profile_letter);
			currentBundle = new Bundle();
			MaterialButton button = itemView.findViewById(R.id.show_more);
			button.setOnClickListener(v -> expandView(button));
			((RelativeLayout)name.getParent()).setOnClickListener(v -> button.performClick());
			((MaterialCardView)profile.getParent().getParent().getParent()).setOnClickListener(v -> {
				if(currentBundle.getBoolean("suspended")){
					CustomToast.showToast(inflater.getContext(), " Product Details Cannot be Updated!", "warning");
					return;
				}
				String transitionName = inflater.getContext().getString(R.string.default_transition_name);
				Intent intent = new Intent(activity, EditProductDetails.class);
				intent.putExtras(currentBundle);
				ActivityOptionsCompat options = ActivityOptionsCompat.makeSceneTransitionAnimation(activity, profileLetter, transitionName);
				ActivityCompat.startActivityForResult(activity, intent, 1, options.toBundle());
			});
			this.adapter = adapter;
		}

		private void expandView(MaterialButton button){
			if(layout.isExpanded()){
				Common.rotateElement(button, 180f, 0f, 300);
			}else{
				Common.rotateElement(button, 0f, 180f, 300);
			}
			layout.toggle();
		}
	}
}
