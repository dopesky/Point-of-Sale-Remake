package com.herokuapp.pointofsale.ui.RecyclerViewAdapters;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.support.annotation.NonNull;
import android.support.constraint.ConstraintLayout;
import android.support.design.button.MaterialButton;
import android.support.v4.app.ActivityCompat;
import android.support.v4.app.ActivityOptionsCompat;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;

import com.bumptech.glide.Glide;
import com.github.aakira.expandablelayout.ExpandableLinearLayout;
import com.google.gson.internal.LinkedTreeMap;
import com.herokuapp.pointofsale.R;
import com.herokuapp.pointofsale.ui.owner.EditEmployeeDetails;
import com.herokuapp.pointofsale.ui.resources.Common;
import com.herokuapp.pointofsale.ui.resources.CustomToast;
import com.herokuapp.pointofsale.ui.resources.NavigationBars;

import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.Locale;
import java.util.Objects;

import de.hdodenhof.circleimageview.CircleImageView;

public class OwnerEmployeesAdapter extends
		RecyclerView.Adapter<OwnerEmployeesAdapter.ViewHolder> {
	private ArrayList<LinkedTreeMap<String,String>> dataToView;
	private final ArrayList<LinkedTreeMap<String,String>> dataFromDB;
	private LayoutInflater inflater;
	private Activity activity;
	private String filter;

	public OwnerEmployeesAdapter(Activity context, ArrayList<LinkedTreeMap<String,String>> dataFromDB) {
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
		String name = Common.capitalize(current.get("first_name") + " " + current.get("last_name"));
		String dept = Common.capitalize(Objects.requireNonNull(current.get("department")));
		String email = current.get("email");
		String status = current.get("status");
		String time = current.get("last_access_time");
		SimpleDateFormat format = new SimpleDateFormat("yyyy-MM-dd hh:mm:ss", Locale.getDefault());
		try {
			Date date = format.parse(time);
			format.applyPattern("dd MMM yyyy • hh:mma");
			time = format.format(date);
		} catch (ParseException e) {System.out.println(e.getMessage());}

		if(filter != null && !filter.trim().isEmpty()){
			if(name.trim().toLowerCase().contains(filter.trim().toLowerCase()) || filter.trim().toLowerCase().contains(name.trim().toLowerCase())) return true;
			if(dept.trim().toLowerCase().contains(filter.trim().toLowerCase()) || filter.trim().toLowerCase().contains(dept.trim().toLowerCase())) return true;
			if(email != null && (email.trim().toLowerCase().contains(filter.trim().toLowerCase()) || filter.trim().toLowerCase().contains(email.trim().toLowerCase()))) return true;
			if(status != null && (status.trim().toLowerCase().contains(filter.trim().toLowerCase()) || filter.trim().toLowerCase().contains(status.trim().toLowerCase()))) return true;
			return time != null && (time.trim().toLowerCase().contains(filter.trim().toLowerCase()) || filter.trim().toLowerCase().contains(time.trim().toLowerCase()));
		}
		return true;
	}

	@NonNull
	@Override
	public OwnerEmployeesAdapter.ViewHolder onCreateViewHolder(@NonNull ViewGroup viewGroup, int i) {
		View itemView = inflater.inflate(R.layout.owner_employees, viewGroup, false);
		return new ViewHolder(itemView, this);
	}

	@Override
	public void onBindViewHolder(@NonNull OwnerEmployeesAdapter.ViewHolder viewHolder, int i) {
		LinkedTreeMap<String, String> current = dataToView.get(i);
		String name = Common.capitalize(current.get("first_name") + " " + current.get("last_name"));
		String dept = Common.capitalize(Objects.requireNonNull(current.get("department")));
		String email = current.get("email");
		String status = current.get("status");
		String time = current.get("last_access_time");
		SimpleDateFormat format = new SimpleDateFormat("yyyy-MM-dd hh:mm:ss", Locale.getDefault());
		try {
			Date date = format.parse(time);
			format.applyPattern("dd MMM yyyy • hh:mma");
			time = format.format(date);
		} catch (ParseException e) {System.out.println(e.getMessage());}
		viewHolder.setIsRecyclable(false);
		viewHolder.layout.setInRecyclerView(true);
		if(current.get("profile_photo") == null){
			Glide.with(inflater.getContext()).load(NavigationBars.BLANK_PROFILE_IMAGE).placeholder(R.drawable.image_pre_loader).into(viewHolder.photo);
			Glide.with(inflater.getContext()).load(NavigationBars.BLANK_PROFILE_IMAGE).placeholder(R.drawable.image_pre_loader).into(viewHolder.profile);
			viewHolder.currentBundle.putString("profile", NavigationBars.BLANK_PROFILE_IMAGE);
		}else{
			Glide.with(inflater.getContext()).load(current.get("profile_photo")).placeholder(R.drawable.image_pre_loader).into(viewHolder.photo);
			Glide.with(inflater.getContext()).load(current.get("profile_photo")).placeholder(R.drawable.image_pre_loader).into(viewHolder.profile);
			viewHolder.currentBundle.putString("profile", current.get("profile_photo"));
		}
		viewHolder.name.setText(name);
		viewHolder.currentBundle.putString("fname", current.get("first_name"));
		viewHolder.currentBundle.putString("lname", current.get("last_name"));
		viewHolder.dept.setText(dept);
		viewHolder.currentBundle.putString("dept", current.get("department_id"));
		viewHolder.email.setText(email);
		viewHolder.currentBundle.putString("email", email);
		viewHolder.status.setText(status);
		viewHolder.time.setText(time);
		viewHolder.currentBundle.putBoolean("active", Integer.parseInt(Objects.requireNonNull(current.get("employee_suspended"))) == 0);
		boolean suspended = Integer.parseInt(Objects.requireNonNull(current.get("suspended"))) == 1
				&& current.get("password") != null;
		viewHolder.currentBundle.putBoolean("suspended", suspended);
		viewHolder.currentBundle.putInt("employee_id", Integer.parseInt(Objects.requireNonNull(current.get("employee_id"))));
	}

	@Override
	public int getItemCount() {
		return dataToView.size();
	}

	class ViewHolder extends RecyclerView.ViewHolder{
		final TextView name;
		final TextView dept;
		final TextView email;
		final TextView status;
		final TextView time;
		final ImageView photo;
		final OwnerEmployeesAdapter adapter;
		final ExpandableLinearLayout layout;
		final CircleImageView profile;
		final Bundle currentBundle;

		ViewHolder(View itemView, OwnerEmployeesAdapter adapter) {
			super(itemView);
			name = itemView.findViewById(R.id.employee_name);
			dept = itemView.findViewById(R.id.employee_dept);
			email = itemView.findViewById(R.id.employee_email);
			status = itemView.findViewById(R.id.employee_status);
			time = itemView.findViewById(R.id.employee_time);
			photo = itemView.findViewById(R.id.employee_photo);
			layout = itemView.findViewById(R.id.expandable_layout);
			profile = itemView.findViewById(R.id.profile_image);
			currentBundle = new Bundle();
			MaterialButton button = itemView.findViewById(R.id.show_more);
			((ConstraintLayout)name.getParent()).setOnClickListener(v -> {
				if(layout.isExpanded()){
					Common.rotateElement(button, 180f, 0f, 300);
				}else{
					Common.rotateElement(button, 0f, 180f, 300);
				}
				layout.toggle();
			});
			photo.setOnClickListener(v -> {
				if(currentBundle.getBoolean("suspended")){
					CustomToast.showToast(inflater.getContext(), " Employee Details Cannot be Updated!", "warning");
					return;
				}
				String transitionName = inflater.getContext().getString(R.string.default_transition_name);
				Intent intent = new Intent(activity, EditEmployeeDetails.class);
				intent.putExtras(currentBundle);
				ActivityOptionsCompat options =
						ActivityOptionsCompat.makeSceneTransitionAnimation(activity, photo, transitionName);
				ActivityCompat.startActivityForResult(activity, intent, 1, options.toBundle());
			});
			this.adapter = adapter;
		}
	}
}
