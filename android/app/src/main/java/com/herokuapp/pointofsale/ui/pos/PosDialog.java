package com.herokuapp.pointofsale.ui.pos;

import android.content.Intent;

import androidx.annotation.NonNull;
import androidx.databinding.DataBindingUtil;
import androidx.core.view.LayoutInflaterCompat;
import androidx.appcompat.app.AppCompatActivity;
import android.os.Bundle;
import android.text.TextUtils;
import android.view.View;

import com.herokuapp.pointofsale.R;
import com.herokuapp.pointofsale.databinding.ActivityPosDialogBinding;
import com.herokuapp.pointofsale.resources.Common;
import com.herokuapp.pointofsale.resources.CustomToast;
import com.herokuapp.pointofsale.resources.PosDataBinder;
import com.mikepenz.iconics.context.IconicsLayoutInflater2;

public class PosDialog extends AppCompatActivity {
	ActivityPosDialogBinding binding;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		LayoutInflaterCompat.setFactory2(getLayoutInflater(), new IconicsLayoutInflater2(getDelegate()));

		super.onCreate(savedInstanceState);
		binding = DataBindingUtil.setContentView(this, R.layout.activity_pos_dialog);

		Bundle defaults = getIntent().getExtras();
		PosDataBinder binder = PosDataBinder.getInstance();

		binding.setLifecycleOwner(this);
		binding.setDataBinder(binder.new DataBinder());

		if(defaults != null && !defaults.isEmpty()){
			PosDataBinder.DataBinder posBinder = binding.getDataBinder();
			posBinder.setCurrencyCode(defaults.getString("currencyCode", null));
			posBinder.setProductID(defaults.getString("product_id", ""));
			posBinder.setProductName(defaults.getString("product_name", ""));
			posBinder.setCategory(defaults.getString("category", ""));
			posBinder.setUpdate(defaults.getString("update",""));
			posBinder.setUnitCost(defaults.getString("unit_cost","0"));
			posBinder.setIsPurchase(defaults.getBoolean("isPurchase",false));
			if(posBinder.getUpdate().equals("1") && (savedInstanceState == null || !savedInstanceState.getBoolean("restart", true))){
				posBinder.setQuantity(defaults.getString("amount", ""));
				posBinder.setCost(defaults.getString("cost", ""));
				posBinder.setDiscount(defaults.getString("discount", ""));
			}
		}
	}

	@Override
	public void finish(){
		super.finish();
		resetEverything();
	}

	@Override
	public void onSaveInstanceState(@NonNull Bundle bundle){
		super.onSaveInstanceState(bundle);
		bundle.putBoolean("restart", true);
	}

	public void addProduct(View view) {
		PosDataBinder.DataBinder binder = binding.getDataBinder();
		if(!validateData()){
			Common.shakeElement(this, view);
			return;
		}
		Intent intent = new Intent();
		Bundle extras = new Bundle();
		extras.putString("product", binder.getProductName());
		extras.putString("product_id", binder.getProductID());
		extras.putString("category_name", binder.getCategory());
		extras.putString("cost", binder.getCost());
		extras.putString("amount", binder.getQuantity());
		extras.putString("discount", binder.getDiscount());
		extras.putString("unit_cost", binder.getUnitCost());
		intent.putExtras(extras);
		setResult(RESULT_OK, intent);
		resetEverything();
		finish();
	}

	public void updateProduct(View view) {
		PosDataBinder.DataBinder binder = binding.getDataBinder();
		if(!validateData()){
			Common.shakeElement(this, view);
			return;
		}
		Intent intent = new Intent();
		Bundle extras = new Bundle();
		extras.putString("product_id", binder.getProductID());
		extras.putString("cost", binder.getCost());
		extras.putString("amount", binder.getQuantity());
		extras.putString("discount", binder.getDiscount());
		intent.putExtras(extras);
		setResult(RESULT_OK, intent);
		resetEverything();
		finish();
	}

	public void removeProduct(View view) {
		PosDataBinder.DataBinder binder = binding.getDataBinder();
		if(binder.getProductID() == null || !TextUtils.isDigitsOnly(binder.getProductID()) || binder.getProductID().trim().isEmpty()){
			CustomToast.showToast(this, " Unexpected Error Occurred!", "danger");
			Common.shakeElement(this, view);
			return;
		}
		Intent intent = new Intent();
		intent.putExtra("product_id", binder.getProductID());
		setResult(2, intent);
		resetEverything();
		finish();
	}

	private void resetEverything(){
		PosDataBinder.DataBinder binder = binding.getDataBinder();
		binder.setCategory("");
		binder.setUpdate("");
		binder.setProductID("");
		binder.setProductName("");
		binder.setCost("");
		binder.setDiscount("");
		binder.setQuantity("");
	}

	private boolean validateData(){
		binding.productDiscount.setError(null);
		binding.productAmount.setError(null);
		binding.productTotalCost.setError(null);

		PosDataBinder.DataBinder binder = binding.getDataBinder();
		if(binder.getProductID() == null || !TextUtils.isDigitsOnly(binder.getProductID()) || binder.getProductID().trim().isEmpty()){
			CustomToast.showToast(this, " Unexpected Error Occurred!", "danger");
			return false;
		}
		if(binder.getProductName() == null || !binder.getProductName().toLowerCase().matches("^[a-z0-9 '-]+$")){
			CustomToast.showToast(this, " Unexpected Error Occurred!", "warning");
			return false;
		}
		if(binder.getCategory() == null){
			CustomToast.showToast(this, " Unexpected Error Occurred!", "info");
			return false;
		}
		if(binder.getQuantity() == null || !binder.getQuantity().matches("^[0-9]+$")){
			binding.productAmount.setError("This Field is Required and Must be Numeric!");
			return false;
		}
		if(binder.getCost() == null || !binder.getCost().matches("^[0-9]+$")){
			if(binder.getIsPurchase()){
				binding.productTotalCost.setError("This Field is Required and Must be Numeric!");
			}else{
				CustomToast.showToast(this, " Unexpected Error Occurred!", "warning");
			}
			return false;
		}
		if(binder.getDiscount() == null || binder.getDiscount().trim().isEmpty()){
			binder.setDiscount("0");
		}
		if(!binder.getDiscount().matches("^[0-9]+$")){
			binding.productDiscount.setError("This Field Must be Numeric!");
			return false;
		}
		return true;
	}

	public void finish(View view) {
		finish();
	}
}
