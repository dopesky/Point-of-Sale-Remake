package com.herokuapp.pointofsale.ui.RecyclerViewAdapters;

import android.app.Activity;
import android.content.Intent;
import android.graphics.Color;
import android.os.Bundle;
import android.support.annotation.NonNull;
import android.support.design.button.MaterialButton;
import android.support.design.card.MaterialCardView;
import android.support.v4.app.ActivityCompat;
import android.support.v4.app.ActivityOptionsCompat;
import android.support.v7.widget.RecyclerView;
import android.text.TextUtils;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.github.aakira.expandablelayout.ExpandableLinearLayout;
import com.google.gson.internal.LinkedTreeMap;
import com.herokuapp.pointofsale.R;
import com.herokuapp.pointofsale.ui.owner.EditProductDetails;
import com.herokuapp.pointofsale.ui.owner.ManageProducts;
import com.herokuapp.pointofsale.ui.resources.Common;
import com.herokuapp.pointofsale.ui.resources.CustomToast;

import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.Locale;
import java.util.Objects;

public class OwnerProductsAdapter extends
		RecyclerView.Adapter<OwnerProductsAdapter.ViewHolder> {
	private ArrayList<LinkedTreeMap<String,String>> dataToView;
	private final ArrayList<LinkedTreeMap<String,String>> dataFromDB;
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

	public void updateData(ArrayList<LinkedTreeMap<String,String>> dataToView){
		filter = "";
		this.dataFromDB.clear();
		this.dataFromDB.addAll(dataToView);
		this.dataToView = dataToView;
		notifyDataSetChanged();
	}

	public void setFilter(String filter){
		if(dataFromDB == null || dataFromDB.size() < 1) return;
		this.filter = filter;
		dataToView = new ArrayList<>();
		for (LinkedTreeMap<String, String> current : dataFromDB) {
			if(isFilterFound(current)) dataToView.add(current);
		}
		notifyDataSetChanged();
	}

	public String getFilter(){
		return filter == null ? "" : filter;
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
		viewHolder.setIsRecyclable(false);
		viewHolder.layout.setInRecyclerView(true);
		String color = Common.getRandomDarkColor(i);
		viewHolder.profile.setBackgroundColor(Color.parseColor(color));
		viewHolder.profileLetter.setText(name.trim().isEmpty() ? "?" : Common.capitalize(name.substring(0,1)));
		viewHolder.currentBundle.putString("profile", color);
		viewHolder.name.setText(name);
		viewHolder.name.setEllipsize(TextUtils.TruncateAt.MARQUEE);
		viewHolder.name.setSelected(true);
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
		final ExpandableLinearLayout layout;
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
			((MaterialCardView)profile.getParent().getParent()).setOnClickListener(v -> {
				if(currentBundle.getBoolean("suspended")){
					CustomToast.showToast(inflater.getContext(), " Product Details Cannot be Updated!", "warning");
					return;
				}
				String transitionName = inflater.getContext().getString(R.string.default_transition_name);
				Intent intent = new Intent(activity, EditProductDetails.class);
				intent.putExtras(currentBundle);
				ActivityOptionsCompat options =
						ActivityOptionsCompat.makeSceneTransitionAnimation(activity, profile, transitionName);
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
