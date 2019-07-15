package com.herokuapp.pointofsale.ui.owner;

import androidx.lifecycle.Observer;
import androidx.lifecycle.ViewModelProviders;
import android.content.Intent;
import android.content.SharedPreferences;
import androidx.databinding.DataBindingUtil;
import android.graphics.Color;
import com.google.android.material.button.MaterialButton;
import androidx.core.view.LayoutInflaterCompat;
import androidx.appcompat.app.AppCompatActivity;
import android.os.Bundle;
import androidx.appcompat.widget.Toolbar;
import android.text.TextUtils;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.Spinner;

import com.bumptech.glide.Glide;
import com.herokuapp.pointofsale.R;
import com.herokuapp.pointofsale.databinding.ActivityAddEmployeeBinding;
import com.herokuapp.pointofsale.models.owner.Owner;
import com.herokuapp.pointofsale.ui.resources.Common;
import com.herokuapp.pointofsale.ui.resources.CustomToast;
import com.herokuapp.pointofsale.ui.resources.NavigationBars;
import com.herokuapp.pointofsale.ui.resources.SpinnerAdapter;
import com.mikepenz.iconics.context.IconicsLayoutInflater2;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Objects;

public class AddEmployee extends AppCompatActivity {
	private boolean isAdding;

	private ActivityAddEmployeeBinding binding;
	private Owner ownerVM;

	private LinearLayout layout;
	private Spinner spinner;
	private MaterialButton saveButton;

	private Observer<SharedPreferences> getUserdataObserver = sharedPreferences -> {
		if(sharedPreferences == null || !sharedPreferences.getString("level", "-1").trim().equals("4")){
			Common.launchLauncherActivity(this);
		}
	};

	private Observer<ArrayList> departmentsObserver = departments ->{
		if(departments == null || departments.size() < 1) return;
		if(spinner.getAdapter() == null){
			SpinnerAdapter adapter = new SpinnerAdapter(this, R.layout.textview, departments, "department");
			spinner.setAdapter(adapter);
		}
	};

	private Observer<Integer> addObserver = status ->{
		if(status != null && status == 0){
			CustomToast.showToast(this, " Successfully Added New Employee!", "success");
			Intent intent = new Intent();
			intent.putExtra("forceReload", true);
			setResult(RESULT_OK, intent);
			finish();
		}
		saveButton.setAlpha((float)1.0);
		saveButton.setText(R.string.save);
		isAdding = false;
	};

	private Observer<String> addErrorObserver = error ->{
		if(error != null && !error.trim().isEmpty()){
			CustomToast.showToast(this, " " + error, "danger");
		}
	};

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		LayoutInflaterCompat.setFactory2(getLayoutInflater(), new IconicsLayoutInflater2(getDelegate()));

		super.onCreate(savedInstanceState);
		binding = DataBindingUtil.setContentView(this, R.layout.activity_add_employee);

		ImageView profile = findViewById(R.id.profile_image);
		Glide.with(this).load(NavigationBars.BLANK_PROFILE_IMAGE).into(profile);

		layout = findViewById(R.id.layout);
		spinner = findViewById(R.id.spinner);
		saveButton = findViewById(R.id.save_button);
		spinner.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
			@Override
			public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {
				binding.getOwnerVM().setDepartment(String.valueOf(id));
			}

			@Override
			public void onNothingSelected(AdapterView<?> parent) {

			}
		});
		isAdding = false;

		ownerVM = ViewModelProviders.of(this).get(Owner.class);
		ownerVM.getUserData().observe(this, getUserdataObserver);
		ownerVM.getDepartments().observe(this, departmentsObserver);
		ownerVM.getAddEmployeeStatus().observe(this, addObserver);
		ownerVM.getAddEmployeeError().observe(this, addErrorObserver);

		Toolbar toolbar = findViewById(R.id.actual_toolbar);
		toolbar.setBackgroundColor(Color.TRANSPARENT);
		toolbar.setTitle("");
		Common.setCustomActionBar(this, toolbar);
		Objects.requireNonNull(getSupportActionBar()).setDisplayHomeAsUpEnabled(true);
		getSupportActionBar().setDisplayShowHomeEnabled(true);

		binding.setLifecycleOwner(this);
		binding.setOwnerVM(ownerVM.new DataBinder());
		HashMap<String, String> defaultValues = ownerVM.getEmployeeData().getValue();
		String employee_id = (defaultValues != null && defaultValues.size() > 0) ? defaultValues.get("employee_id") : "";
		if(employee_id != null && !employee_id.trim().isEmpty()){
			binding.getOwnerVM().setEmployeeID("");
			binding.getOwnerVM().setFirstName("");
			binding.getOwnerVM().setLastName("");
			binding.getOwnerVM().setEmail("");
			binding.getOwnerVM().setDepartment("");
		}

		layout.animate().alpha(1.0f).setDuration(500);
		spinner.animate().alpha(1.0f).setDuration(500);
		ownerVM.fetchDepartments();
	}

	@Override
	public void onBackPressed(){
		layout.animate().alpha(0f).setDuration(150);
		spinner.animate().alpha(0f).setDuration(150);
		super.onBackPressed();
	}

	@Override
	public boolean onSupportNavigateUp() {
		onBackPressed();
		return true;
	}

	public void addEmployee(View view) {
		if(isAdding) return;
		String errors = validateInput();
		if(!errors.trim().isEmpty()){
			Common.shakeElement(this, view);
			return;
		}
		isAdding = true;
		MaterialButton button = (MaterialButton) view;
		button.setAlpha((float)0.6);
		button.setText(getString(R.string.adding));
		ownerVM.employ();
	}

	private String validateInput(){
		binding.fnameEditText.setError(null);
		binding.lnameEditText.setError(null);
		binding.emailEditText.setError(null);

		if(binding.getOwnerVM() == null){
			CustomToast.showToast(this, " Unexpected Error Occurred!", "danger");
			return "Unexpected Error Occurred!";
		}

		Owner.DataBinder binder = binding.getOwnerVM();

		if(binder.getDepartment().trim().isEmpty() || !TextUtils.isDigitsOnly(binder.getDepartment())){
			CustomToast.showToast(this, " Unexpected Error Occurred!", "danger");
			return "Unexpected Error Occurred!";
		}

		if(binder.getFirstName().trim().isEmpty()){
			binding.fnameEditText.setError("This Field is Required!");
			return "This Field is Required!";
		}
		if(binder.getLastName().trim().isEmpty()){
			binding.lnameEditText.setError("This Field is Required!");
			return "This Field is Required!";
		}
		if(binder.getEmail().trim().isEmpty()){
			binding.emailEditText.setError("This Field is Required!");
			return "This Field is Required!";
		}
		if(!Common.checkEmail(binder.getEmail().trim())){
			binding.emailEditText.setError("Email is of Invalid Format!");
			return "Email is of Invalid Format!";
		}
		if(!binder.getFirstName().toLowerCase().trim().matches("^[a-z '-]+$")){
			binding.fnameEditText.setError("This Field Contains Invalid Characters");
			return "This Field Contains Invalid Characters";
		}
		if(!binder.getLastName().toLowerCase().trim().matches("^[a-z '-]+$")){
			binding.lnameEditText.setError("This Field Contains Invalid Characters");
			return "This Field Contains Invalid Characters";
		}
		return "";
	}
}
