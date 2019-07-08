package com.herokuapp.pointofsale.ui.pos;

import android.content.Intent;
import android.databinding.DataBindingUtil;
import android.support.v4.view.LayoutInflaterCompat;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.view.View;

import com.herokuapp.pointofsale.R;
import com.herokuapp.pointofsale.databinding.ActivityPosDialogBinding;
import com.herokuapp.pointofsale.ui.resources.PosDataBinder;
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
			posBinder.setProductID(defaults.getString("product_id", ""));
			posBinder.setProductName(defaults.getString("product_name", ""));
			posBinder.setCategory(defaults.getString("category", ""));
			posBinder.setUpdate(defaults.getString("update",""));
		}
	}

	public void finishAddActivity(View view) {
		PosDataBinder.DataBinder binder = binding.getDataBinder();
		Intent intent = new Intent();
		Bundle extras = new Bundle();
		extras.putString("product", binder.getProductName());
		extras.putString("product_id", binder.getProductID());
		extras.putString("category_name", binder.getCategory());
		extras.putString("cost", binder.getCost());
		extras.putString("amount", binder.getQuantity());
		extras.putString("discount", binder.getDiscount());
		intent.putExtras(extras);
		setResult(RESULT_OK, intent);
		resetEverything();
		finish();
	}

	public void resetEverything(){
		PosDataBinder.DataBinder binder = binding.getDataBinder();
		binder.setCategory("");
		binder.setUpdate("");
		binder.setProductID("");
		binder.setProductName("");
		binder.setCost("");
		binder.setDiscount("");
		binder.setQuantity("");
	}
}
