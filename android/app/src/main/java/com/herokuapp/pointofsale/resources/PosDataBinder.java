package com.herokuapp.pointofsale.ui.resources;

import androidx.databinding.BaseObservable;
import androidx.databinding.Bindable;
import androidx.databinding.library.baseAdapters.BR;

import java.util.HashMap;

public class PosDataBinder {
	private static PosDataBinder dataBinder;
	private HashMap<String, String> posData;
	private HashMap<String, String> checkoutData;
	private boolean isPurchase;
	private String currencyCode;

	public static PosDataBinder getInstance(){
		if(dataBinder == null){
			dataBinder = new PosDataBinder();
			dataBinder.posData = new HashMap<>();
			dataBinder.checkoutData = new HashMap<>();
			dataBinder.isPurchase = false;
			dataBinder.currencyCode = null;
		}
		return dataBinder;
	}

	public class DataBinder extends BaseObservable {
		@Bindable
		public String getCurrencyCode(){
			return currencyCode;
		}

		public void setCurrencyCode(String currencyCode){
			PosDataBinder.this.currencyCode = currencyCode;
		}

		String getUnitCost(){
			return posData.containsKey("unit_cost") ? posData.get("unit_cost") : "";
		}

		public void setUnitCost(String cost){
			posData.put("unit_cost", cost);
		}

		@Bindable
		public boolean getIsPurchase(){
			return isPurchase;
		}

		public void setIsPurchase(boolean type){
			isPurchase = type;
		}

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
			if(!isPurchase){
				try{
					String discount = getDiscount().trim().isEmpty() ? "0" : getDiscount().trim();
					String amount = quantity.trim().isEmpty() ? "0" : quantity.trim();
					int cost = (Integer.parseInt(getUnitCost()) * Integer.parseInt(amount)) - Integer.parseInt(discount);
					setCost(String.valueOf(cost));
				}catch(NumberFormatException|NullPointerException ex){
					setCost("-1");
				}
				notifyPropertyChanged(BR.cost);
			}
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
			if(!isPurchase){
				try{
					String disc = discount.trim().isEmpty() ? "0" : discount.trim();
					String amount = getQuantity().trim().isEmpty() ? "0" : getQuantity().trim();
					int cost = (Integer.parseInt(getUnitCost()) * Integer.parseInt(amount)) - Integer.parseInt(disc);
					setCost(String.valueOf(cost));
				}catch(NumberFormatException|NullPointerException ex){
					setCost("-1");
				}
				notifyPropertyChanged(BR.cost);
			}
			posData.put("discount", discount);
		}

		@Bindable
		public String getUpdate(){
			return posData.containsKey("update") ? posData.get("update") : "";
		}

		public void setUpdate(String update){
			posData.put("update", update);
		}



		@Bindable
		public String getNetCost(){
			return checkoutData.containsKey("net_cost") ? checkoutData.get("net_cost") : "";
		}

		public void setNetCost(String cost){
			checkoutData.put("net_cost", cost);
		}

		@Bindable
		public String getAmountPaid(){
			return checkoutData.containsKey("amount_paid") ? checkoutData.get("amount_paid") : "";
		}

		public void setAmountPaid(String amount){
			try {
				String net = getNetCost().trim().isEmpty() ? "0" : getNetCost();
				String balance = String.valueOf(Integer.parseInt(amount) - Integer.parseInt(net));
				setBalance(balance);
			}catch(NullPointerException|NumberFormatException ex){
				setBalance("-1");
			}
			notifyPropertyChanged(BR.balance);
			checkoutData.put("amount_paid", amount);
		}

		@Bindable
		public String getBalance(){
			return checkoutData.containsKey("balance") ? checkoutData.get("balance") : "";
		}

		public void setBalance(String balance){
			checkoutData.put("balance", balance);
		}

		public String getMethod(){
			return checkoutData.containsKey("method_id") ? checkoutData.get("method_id") : "";
		}

		public void setMethod(String method){
			checkoutData.put("method_id", method);
		}
	}
}
