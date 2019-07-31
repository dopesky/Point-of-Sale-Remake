package com.herokuapp.pointofsale.resources;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.TextView;

import com.google.gson.internal.LinkedTreeMap;

import java.util.ArrayList;
import java.util.Objects;

public class SpinnerAdapter extends BaseAdapter {
	private ArrayList data;
	private String item;
	private String itemID;
	private int layoutRes;
	private Context context;

	private void init(Context context, int layoutRes, ArrayList data, String item, String itemID){
		this.data = data;
		this.item = item;
		this.itemID = itemID;
		this.layoutRes = layoutRes;
		this.context = context;
	}

	public SpinnerAdapter(Context context, int layoutRes, ArrayList data, String item){
		init(context, layoutRes, data, item, item + "_id");
	}

	public SpinnerAdapter(Context context, int layoutRes, ArrayList data, String item, String itemID){
		init(context, layoutRes, data, item, itemID);
	}

	@Override
	public int getCount() {
		return data.size();
	}

	@Override
	public Object getItem(int position) {
		return ((LinkedTreeMap)data.get(position)).get(item);
	}

	@Override
	public long getItemId(int position) {
		return Integer.parseInt(Objects.requireNonNull(((LinkedTreeMap) data.get(position)).get(itemID)).toString());
	}

	@Override
	public View getView(int position, View convertView, ViewGroup parent) {
		Holder holder;
		if(convertView == null){
			holder = new Holder();
			convertView = LayoutInflater.from(context).inflate(layoutRes, null);
			holder.textView = (TextView)convertView;
			convertView.setTag(holder);
		}else{
			holder = (Holder)convertView.getTag();
		}
		holder.textView.setText(Common.capitalize(getItem(position).toString()));
		return convertView;
	}

	private class Holder{
		private TextView textView;
	}
}
