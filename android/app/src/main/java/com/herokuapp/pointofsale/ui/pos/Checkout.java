package com.herokuapp.pointofsale.ui.pos;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.view.LayoutInflaterCompat;
import androidx.databinding.DataBindingUtil;
import androidx.lifecycle.Observer;
import androidx.lifecycle.ViewModelProviders;

import android.content.Intent;
import android.graphics.Color;
import android.os.Bundle;
import android.text.TextUtils;
import android.view.View;
import android.widget.AdapterView;
import android.widget.Spinner;

import com.google.android.material.button.MaterialButton;
import com.herokuapp.pointofsale.R;
import com.herokuapp.pointofsale.databinding.ActivityCheckoutBinding;
import com.herokuapp.pointofsale.models.pos.Purchases;
import com.herokuapp.pointofsale.ui.resources.Common;
import com.herokuapp.pointofsale.ui.resources.CustomToast;
import com.herokuapp.pointofsale.ui.resources.PosDataBinder;
import com.herokuapp.pointofsale.ui.resources.SpinnerAdapter;
import com.mikepenz.fontawesome_typeface_library.FontAwesome;
import com.mikepenz.iconics.IconicsDrawable;
import com.mikepenz.iconics.context.IconicsLayoutInflater2;

import java.util.ArrayList;

public class Checkout extends AppCompatActivity {
	ActivityCheckoutBinding binding;
	Purchases purchasesVM;
	Spinner spinner;

	private Observer<ArrayList> methodsObserver = methods ->{
		if(methods == null || methods.size() < 1) return;
		if(spinner.getAdapter() == null){
			SpinnerAdapter adapter = new SpinnerAdapter(this, R.layout.textview, methods, "method");
			spinner.setAdapter(adapter);
		}
	};

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		LayoutInflaterCompat.setFactory2(getLayoutInflater(), new IconicsLayoutInflater2(getDelegate()));

		super.onCreate(savedInstanceState);
		binding = DataBindingUtil.setContentView(this, R.layout.activity_checkout);
		spinner = findViewById(R.id.spinner);
		spinner.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
			@Override
			public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {
				binding.getDataBinder().setMethod(String.valueOf(id));
			}

			@Override
			public void onNothingSelected(AdapterView<?> parent) {

			}
		});
		MaterialButton checkout = findViewById(R.id.checkout_button);
		checkout.setIcon(new IconicsDrawable(this).icon(FontAwesome.Icon.faw_sign_out_alt).color(Color.WHITE).sizeDp(20));

		purchasesVM = ViewModelProviders.of(this).get(Purchases.class);
		purchasesVM.getPaymentMethods().observe(this, methodsObserver);
		purchasesVM.fetchPaymentMethods();

		Bundle defaults = getIntent().getExtras();
		PosDataBinder binder = PosDataBinder.getInstance();

		binding.setLifecycleOwner(this);
		binding.setDataBinder(binder.new DataBinder());

		if(defaults != null && !defaults.isEmpty()){
			PosDataBinder.DataBinder posBinder = binding.getDataBinder();
			posBinder.setIsPurchase(defaults.getBoolean("isPurchase",false));
			posBinder.setNetCost(defaults.getString("net_cost", ""));
			posBinder.setCurrencyCode(defaults.getString("currencyCode", null));
			if(savedInstanceState == null || !savedInstanceState.getBoolean("restart", true)){
				posBinder.setAmountPaid(defaults.getString("amount_paid", ""));
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

	public void finish(View view) {
		finish();
	}

	public void checkout(View view) {
		PosDataBinder.DataBinder binder = binding.getDataBinder();
		if(!validateData()){
			Common.shakeElement(this, view);
			return;
		}
		Intent intent = new Intent();
		intent.putExtra("method_id", binder.getMethod());
		setResult(RESULT_OK, intent);
		resetEverything();
		finish();
	}

	private void resetEverything(){
		PosDataBinder.DataBinder binder = binding.getDataBinder();
		binder.setNetCost("");
		binder.setAmountPaid("");
		binder.setBalance("");
	}

	private boolean validateData(){
		binding.amountPaid.setError(null);

		PosDataBinder.DataBinder binder = binding.getDataBinder();
		if(binder.getNetCost() == null || !TextUtils.isDigitsOnly(binder.getNetCost()) || binder.getNetCost().trim().isEmpty()){
			CustomToast.showToast(this, " Unexpected Error Occurred!", "danger");
			return false;
		}
		if(binder.getAmountPaid() == null || !binder.getAmountPaid().matches("^[0-9]+$")){
			if(binder.getIsPurchase()){
				CustomToast.showToast(this, " Unexpected Error Occurred!", "danger");
			}else{
				binding.amountPaid.setError("This is Required and Should be Numeric!");
			}
			return false;
		}
		if(binder.getBalance() == null || !binder.getBalance().matches("^[0-9]+$")){
			CustomToast.showToast(this, " Ensure Amount Paid is Greater than Cost!", "danger");
			return false;
		}
		if(binder.getMethod() == null || !binder.getMethod().matches("^[0-9]+$")){
			CustomToast.showToast(this, " An Unnexpected Error Occurred!", "danger");
			return false;
		}
		return true;
	}
}
