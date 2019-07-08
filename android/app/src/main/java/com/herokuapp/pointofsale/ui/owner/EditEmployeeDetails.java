package com.herokuapp.pointofsale.ui.owner;

import android.arch.lifecycle.Observer;
import android.arch.lifecycle.ViewModelProviders;
import android.content.Intent;
import android.content.SharedPreferences;
import android.databinding.DataBindingUtil;
import android.graphics.Color;
import android.support.design.button.MaterialButton;
import android.support.v4.view.LayoutInflaterCompat;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.support.v7.widget.Toolbar;
import android.text.TextUtils;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.Spinner;

import com.bumptech.glide.Glide;
import com.google.gson.internal.LinkedTreeMap;
import com.herokuapp.pointofsale.R;
import com.herokuapp.pointofsale.databinding.ActivityEditEmployeeDetailsBinding;
import com.herokuapp.pointofsale.models.owner.Owner;
import com.herokuapp.pointofsale.ui.resources.Common;
import com.herokuapp.pointofsale.ui.resources.CustomToast;
import com.herokuapp.pointofsale.ui.resources.SpinnerAdapter;
import com.mikepenz.iconics.context.IconicsLayoutInflater2;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Objects;

public class EditEmployeeDetails extends AppCompatActivity {
	private Owner ownerVM;
	private ActivityEditEmployeeDetailsBinding binding;

	private MaterialButton disEnableButton;
	private MaterialButton saveButton;
	private boolean isUpdating;

	private LinearLayout layout;

	private Spinner spinner;

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
			for(int i = 0; i < departments.size(); i++){
				if(Objects.requireNonNull(((LinkedTreeMap) departments.get(i)).get("department_id")).toString()
								.equals(Objects.requireNonNull(getIntent().getExtras()).getString("dept"))){
					spinner.setSelection(i);
				}
			}
		}
	};

	private Observer<Integer> updateObserver = status ->{
		if(status != null && status == 0){
			CustomToast.showToast(this, " Update Operation was Successful!", "success");
			Intent intent = new Intent();
			intent.putExtra("forceReload", true);
			setResult(RESULT_OK, intent);
			finish();
		}
		disEnableButton.setAlpha((float)1.0);
		disEnableButton.setText(Objects.requireNonNull(getIntent().getExtras()).getBoolean("active") ? "Disable" : "Enable");
		saveButton.setAlpha((float)1.0);
		saveButton.setText(R.string.save);
		isUpdating = false;
	};

	private Observer<String> updateErrorObserver = error ->{
		if(error != null && !error.trim().isEmpty()){
			CustomToast.showToast(this, " " + error, "danger");
		}
	};

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		LayoutInflaterCompat.setFactory2(getLayoutInflater(), new IconicsLayoutInflater2(getDelegate()));
		super.onCreate(savedInstanceState);
		binding = DataBindingUtil.setContentView(this, R.layout.activity_edit_employee_details);

		Bundle bundle = getIntent().getExtras();
		ImageView profile = findViewById(R.id.profile_image);
		Glide.with(this).load(Objects.requireNonNull(bundle).getString("profile")).into(profile);

		layout = findViewById(R.id.layout);
		spinner = findViewById(R.id.spinner);
		spinner.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
			@Override
			public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {
				binding.getOwnerVM().setDepartment(String.valueOf(id));
			}

			@Override
			public void onNothingSelected(AdapterView<?> parent) {

			}
		});
		disEnableButton = findViewById(R.id.disenable_button);
		saveButton = findViewById(R.id.save_button);
		isUpdating = false;

		ownerVM = ViewModelProviders.of(this).get(Owner.class);
		ownerVM.getUserData().observe(this, getUserdataObserver);
		ownerVM.getDepartments().observe(this, departmentsObserver);
		ownerVM.getUpdateEmployeeStatus().observe(this, updateObserver);
		ownerVM.getUpdateEmployeeError().observe(this, updateErrorObserver);

		Toolbar toolbar = findViewById(R.id.actual_toolbar);
		toolbar.setBackgroundColor(Color.TRANSPARENT);
		toolbar.setTitle("");
		Common.setCustomActionBar(this, toolbar);
		Objects.requireNonNull(getSupportActionBar()).setDisplayHomeAsUpEnabled(true);
		getSupportActionBar().setDisplayShowHomeEnabled(true);

		binding.setLifecycleOwner(this);
		binding.setOwnerVM(ownerVM.new DataBinder());
		HashMap<String, String> defaultValues = ownerVM.getEmployeeData().getValue();
		if(defaultValues == null || defaultValues.size() < 1){
			binding.getOwnerVM().setFirstName(Common.capitalize(Objects.requireNonNull(bundle.getString("fname"))));
			binding.getOwnerVM().setLastName(Common.capitalize(Objects.requireNonNull(bundle.getString("lname"))));
			binding.getOwnerVM().setEmail(Objects.requireNonNull(bundle.getString("email")).toLowerCase());
			binding.getOwnerVM().setDepartment(bundle.getString("dept"));
			binding.getOwnerVM().setEmployeeActive(bundle.getBoolean("active") ? "1" : "0");
			binding.getOwnerVM().setEmployeeID(String.valueOf(bundle.getInt("employee_id")));
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

	public void updateEmployee(View view) {
		if(isUpdating) return;
		String errors = validateInput();
		if(!errors.trim().isEmpty()){
			Common.shakeElement(this, view);
			return;
		}
		isUpdating = true;
		MaterialButton button = (MaterialButton) view;
		button.setAlpha((float)0.6);
		button.setText(getString(R.string.saving));
		ownerVM.updateEmployee();
	}

	public void disableEnableEmployee(View view) {
		if(isUpdating) return;
		String errors = this.checkDefaultValues();
		if(!errors.trim().isEmpty()){
			Common.shakeElement(this, view);
			return;
		}
		isUpdating = true;
		MaterialButton button = (MaterialButton) view;
		button.setAlpha((float)0.6);
		button.setText(Objects.requireNonNull(getIntent().getExtras()).getBoolean("active") ? "Disabling . . ." : "Enabling . . .");
		ownerVM.unemployReemploy();
	}

	private String checkDefaultValues(){
		Bundle bundle = getIntent().getExtras();
		if(bundle == null){
			CustomToast.showToast(this, " Unexpected Error Occurred!", "danger");
			return "Unexpected Error Occurred!";
		}
		int employeeID = bundle.getInt("employee_id", -1);
		if(employeeID == -1){
			CustomToast.showToast(this, " Unexpected Error Occurred!", "danger");
			return "Unexpected Error Occurred!";
		}
		return "";
	}

	private String validateInput(){
		binding.fnameEditText.setError(null);
		binding.lnameEditText.setError(null);
		binding.emailEditText.setError(null);

		String defaultErrors = checkDefaultValues();

		if(!defaultErrors.trim().isEmpty()) return defaultErrors;

		if(binding.getOwnerVM() == null){
			CustomToast.showToast(this, " Unexpected Error Occurred!", "danger");
			return "Unexpected Error Occurred!";
		}

		Owner.DataBinder binder = binding.getOwnerVM();

		if(binder.getEmployeeID().trim().isEmpty()
				|| binder.getDepartment().trim().isEmpty()
				|| !TextUtils.isDigitsOnly(binder.getEmployeeID())
				|| !TextUtils.isDigitsOnly(binder.getDepartment())){
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
