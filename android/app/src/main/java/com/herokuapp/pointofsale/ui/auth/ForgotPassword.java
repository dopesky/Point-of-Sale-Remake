package com.herokuapp.pointofsale.ui.auth;

import android.arch.lifecycle.Observer;
import android.arch.lifecycle.ViewModelProviders;
import android.databinding.DataBindingUtil;
import android.support.design.button.MaterialButton;
import android.support.design.widget.TextInputLayout;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.view.View;

import com.herokuapp.pointofsale.R;
import com.herokuapp.pointofsale.databinding.ActivityForgotPasswordBinding;
import com.herokuapp.pointofsale.models.authentication.Auth;
import com.herokuapp.pointofsale.ui.resources.Common;
import com.herokuapp.pointofsale.ui.resources.CustomToast;

public class ForgotPassword extends AppCompatActivity {
	private boolean isResetting = false;
	private Auth authVM;
	private MaterialButton resetButton;

	private Observer<Integer> resetObserver = resetStatus -> {
		if(resetStatus != null && resetStatus == 0){
			CustomToast.showToast(this, " Successful. Check your Email!", "success");
			launchMainActivity(null);
		}
		resetButton.setAlpha((float)1.0);
		resetButton.setText(R.string.reset);
		isResetting = false;
	};

	private Observer<String> resetErrorObserver = resetError -> {
		if(resetError != null && resetError.trim().length() > 0){
			CustomToast.showToast(this, " " + resetError, "danger");
		}
	};

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		Common.logoutUser(this);
		super.onCreate(savedInstanceState);
		ActivityForgotPasswordBinding binding = DataBindingUtil.setContentView(this, R.layout.activity_forgot_password);

		resetButton = findViewById(R.id.reset_button);

		authVM = ViewModelProviders.of(this).get(Auth.class);
		authVM.getResetStatus().observe(this, resetObserver);
		authVM.getResetError().observe(this, resetErrorObserver);

		binding.setLifecycleOwner(this);
		Auth.DataBinder dataBinder = authVM.new DataBinder();
		binding.setAuthVm(dataBinder);
	}

	public void launchMainActivity(View view) {
		finish();
	}

	public void forgotPasswordLogic(View view) {
		if(this.isResetting) return;
		String errors = this.validateInput(authVM.email);
		if(!errors.isEmpty()){
			Common.shakeElement(this, view);
			return;
		}
		this.isResetting = true;
		MaterialButton button = (MaterialButton) view;
		button.setAlpha((float)0.6);
		button.setText(getString(R.string.resetting));
		authVM.resetUserPassword();
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
