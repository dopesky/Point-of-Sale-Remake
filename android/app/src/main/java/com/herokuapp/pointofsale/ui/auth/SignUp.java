package com.herokuapp.pointofsale.ui.auth;

import android.arch.lifecycle.Observer;
import android.arch.lifecycle.ViewModelProviders;
import android.content.pm.PackageManager;
import android.databinding.DataBindingUtil;
import android.support.annotation.NonNull;
import android.support.design.button.MaterialButton;
import android.support.design.widget.TextInputLayout;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.view.View;

import com.herokuapp.pointofsale.R;
import com.herokuapp.pointofsale.databinding.ActivitySignUpBinding;
import com.herokuapp.pointofsale.models.authentication.Auth;
import com.herokuapp.pointofsale.ui.resources.Common;
import com.herokuapp.pointofsale.ui.resources.CustomToast;

public class SignUp extends AppCompatActivity {
	private boolean isSigningUp = false;
	private Auth authVM;
	private MaterialButton signUpButton;

	private Observer<Integer> signUpStatusObserver = signUpStatus -> {
		if(signUpStatus != null && signUpStatus == 0){
			CustomToast.showToast(this, " Successful Sign Up. Congrats!", "success");
			this.launchMainActivity(null);
		}
		isSigningUp = false;
		signUpButton.setAlpha((float)1.0);
		signUpButton.setText(getString(R.string.create_account));
	};

	private Observer<String> signUpErrorObserver = signUpError -> {
		if(signUpError != null && !signUpError.trim().isEmpty()){
			CustomToast.showToast(this, " " + signUpError, "danger");
		}
	};

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		Common.logoutUser(this);
		super.onCreate(savedInstanceState);
		ActivitySignUpBinding binding = DataBindingUtil.setContentView(this, R.layout.activity_sign_up);
		signUpButton = findViewById(R.id.sign_up_button);

		authVM = ViewModelProviders.of(this).get(Auth.class);

		authVM.getSignUpStatus().observe(this, signUpStatusObserver);
		authVM.getSignUpError().observe(this, signUpErrorObserver);

		Common.getUserCurrentLocation(this, authVM.userLocation);

		binding.setLifecycleOwner(this);
		Auth.DataBinder dataBinder = authVM.new DataBinder();
		binding.setAuthVm(dataBinder);
	}

	@Override
	public void onRequestPermissionsResult(int code, @NonNull String[] permissions, @NonNull int... results){
		if(code == 1 && results[0] == PackageManager.PERMISSION_GRANTED){
			Common.getUserCurrentLocation(this, authVM.userLocation);
		}
	}

	public void launchMainActivity(View view) {
		finish();
	}

	public void signUpLogic(View view) {
		if(this.isSigningUp) return;
		String errors = this.validateInput(authVM.email);
		if(!errors.isEmpty()){
			Common.shakeElement(this, view);
			return;
		}
		this.isSigningUp = true;
		MaterialButton button = (MaterialButton) view;
		button.setAlpha((float)0.6);
		button.setText(getString(R.string.signing_up));
		authVM.signUpUser(this);
	}

	private String validateInput(String email){
		TextInputLayout emailEditText = findViewById(R.id.email_edit_text);
		emailEditText.setError(null);
		if(email.isEmpty()){
			emailEditText.setError("This is a Required Field");
			return "Ensure You Fill All Fields";
		}
		if(!Common.checkEmail(email)){
			emailEditText.setError("Email is of invalid Format");
			return "Email is of Invalid Format";
		}
		return "";
	}
}
