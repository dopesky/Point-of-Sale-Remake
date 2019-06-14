package com.herokuapp.pointofsale.Resources;

import android.content.Context;
import android.support.annotation.NonNull;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;

import com.google.gson.internal.LinkedTreeMap;
import com.herokuapp.pointofsale.R;
import com.squareup.picasso.Picasso;

import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.Objects;

public class OwnerEmployeesAdapter extends
		RecyclerView.Adapter<OwnerEmployeesAdapter.ViewHolder> {
	private final ArrayList<LinkedTreeMap<String,String>> dataToView;
	private LayoutInflater inflater;

	public OwnerEmployeesAdapter(Context context, ArrayList<LinkedTreeMap<String,String>> dataToView) {
		inflater = LayoutInflater.from(context);
		this.dataToView = dataToView;
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
		SimpleDateFormat format = new SimpleDateFormat("yyyy-MM-dd hh:mm:ss");
		try {
			Date date = format.parse(time);
			format.applyPattern("dd MMM yyyy • hh:mma");
			time = format.format(date);
		} catch (ParseException e) {System.out.println(e.getMessage());}
		if(current.get("profile_photo") == null){
			Picasso.get().load("https://res.cloudinary.com/dopesky/image/upload/v1558329489/point_of_sale/site_data/blank-profile-picture-973460_960_720_gcn9y2.png").into(viewHolder.photo);
		}else{
			Picasso.get().load(current.get("profile_photo")).into(viewHolder.photo);
		}
		viewHolder.name.setText(name);
		viewHolder.dept.setText(dept);
		viewHolder.email.setText(email);
		viewHolder.status.setText(status);
		viewHolder.time.setText(time);
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

		ViewHolder(View itemView, OwnerEmployeesAdapter adapter) {
			super(itemView);
			name = itemView.findViewById(R.id.employee_name);
			dept = itemView.findViewById(R.id.employee_dept);
			email = itemView.findViewById(R.id.employee_email);
			status = itemView.findViewById(R.id.employee_status);
			time = itemView.findViewById(R.id.employee_time);
			photo = itemView.findViewById(R.id.employee_photo);
			this.adapter = adapter;
		}
	}
}
