package com.herokuapp.pointofsale.ui.resources;

import android.databinding.BaseObservable;
import android.databinding.Bindable;

import java.util.HashMap;

public class PosDataBinder {
	private static PosDataBinder dataBinder;
	private HashMap<String, String> posData;

	public static PosDataBinder getInstance(){
		if(dataBinder == null){
			dataBinder = new PosDataBinder();
			dataBinder.posData = new HashMap<>();
		}
		return dataBinder;
	}

	public class DataBinder extends BaseObservable {
		@Bindable
		public String getProductID(){
			return posData.containsKey("product_id") ? posData.get("product_id") : "";
		}

		public void setProductID(String id){
			posData.put("product_id",id);
		}

		@Bindable
		public String getProductName(){
			return posData.containsKey("product") ? posData.get("product") : "";
		}

		public void setProductName(String name){
			posData.put("product",name);
		}

		@Bindable
		public String getCategory(){
			return posData.containsKey("category") ? posData.get("category") : "";
		}

		public void setCategory(String name){
			posData.put("category",name);
		}

		@Bindable
		public String getQuantity(){
			return posData.containsKey("quantity") ? posData.get("quantity") : "";
		}

		public void setQuantity(String quantity){
			posData.put("quantity",quantity);
		}

		@Bindable
		public String getCost(){
			return posData.containsKey("cost") ? posData.get("cost") : "";
		}

		public void setCost(String cost){
			posData.put("cost",cost);
		}

		@Bindable
		public String getDiscount(){
			return posData.containsKey("discount") ? posData.get("discount") : "";
		}

		public void setDiscount(String discount){
			posData.put("discount", discount);
		}

		@Bindable
		public String getUpdate(){
			return posData.containsKey("update") ? posData.get("update") : "";
		}

		public void setUpdate(String update){
			posData.put("update", update);
		}
	}
}
