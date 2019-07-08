package com.herokuapp.pointofsale.ui.RecyclerViewAdapters;

import android.app.Activity;
import android.graphics.Color;
import android.support.annotation.NonNull;
import android.support.design.button.MaterialButton;
import android.support.design.card.MaterialCardView;
import android.support.v7.widget.RecyclerView;
import android.text.TextUtils;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.github.aakira.expandablelayout.ExpandableLinearLayout;
import com.google.gson.internal.LinkedTreeMap;
import com.herokuapp.pointofsale.R;
import com.herokuapp.pointofsale.ui.resources.Common;

import java.util.ArrayList;
import java.util.Objects;

public class CartAdapter extends
		RecyclerView.Adapter<CartAdapter.ViewHolder> {
	private ArrayList<LinkedTreeMap<String,String>> dataToView;
	private final ArrayList<LinkedTreeMap<String,String>> dataFromDB;
	private LayoutInflater inflater;
	private LinkedTreeMap userdata;
	private String filter;

	public CartAdapter(Activity context, ArrayList<LinkedTreeMap<String,String>> dataFromDB, LinkedTreeMap userdata) {
		inflater = context.getLayoutInflater();
		this.userdata = userdata;
		this.dataFromDB = dataFromDB;
		this.dataToView = dataFromDB;
		filter = "";
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
		viewHolder.setIsRecyclable(false);
		viewHolder.layout.setInRecyclerView(true);
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
		final ExpandableLinearLayout layout;
		final ImageView profile;
		final TextView profileLetter;

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
			MaterialButton button = itemView.findViewById(R.id.show_more);
			button.setOnClickListener(v -> expandView(button));
			((RelativeLayout)name.getParent()).setOnClickListener(v -> button.performClick());
			((MaterialCardView)profile.getParent().getParent()).setOnClickListener(v -> {
				//To Be Implemented
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
