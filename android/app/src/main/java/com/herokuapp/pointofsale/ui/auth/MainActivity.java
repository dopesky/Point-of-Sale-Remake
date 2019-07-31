package com.herokuapp.pointofsale.ui.auth;

import androidx.lifecycle.Observer;
import androidx.lifecycle.ViewModelProviders;
import android.content.Intent;
import android.content.SharedPreferences;
import androidx.databinding.DataBindingUtil;
import android.os.Bundle;
import com.google.android.material.button.MaterialButton;
import com.google.android.material.textfield.TextInputLayout;
import androidx.appcompat.app.AppCompatActivity;
import android.view.View;

import com.herokuapp.pointofsale.R;
import com.herokuapp.pointofsale.databinding.ActivityMainBinding;
import com.herokuapp.pointofsale.viewmodels.authentication.Auth;
import com.herokuapp.pointofsale.resources.Common;
import com.herokuapp.pointofsale.resources.CustomToast;


public class MainActivity extends AppCompatActivity {
	private boolean isLoggingIn = false;
	private Auth authVM;
	private MaterialButton loginButton;
	private TextInputLayout emailEditText;
	private TextInputLayout passwordEditText;

	private Observer<SharedPreferences> sessionDataObserver = sharedPreferences -> {
		if(sharedPreferences != null && sharedPreferences.contains("user_id")){
			Common.launchLauncherActivity(this);
		}
	};

	private Observer<Integer> loginObserver = loginStatus -> {
		if(loginStatus != null && loginStatus > -1){
			if(loginStatus == 0) {
				Common.launchLauncherActivity(this);
			}
			loginButton.setAlpha((float)1.0);
			loginButton.setText(R.string.main_activity_header);
			isLoggingIn = false;
		}
	};

	private Observer<String> loginErrorObserver = loginError -> {
		if(loginError != null && loginError.trim().length() > 0){
			CustomToast.showToast(this, " " + loginError, "danger");
		}
	};

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		Common.logoutUser(this);
		super.onCreate(savedInstanceState);

		ActivityMainBinding binding = DataBindingUtil.setContentView(this, R.layout.activity_main);

		loginButton = findViewById(R.id.login_button);
		emailEditText = findViewById(R.id.email_edit_text);
		passwordEditText = findViewById(R.id.password_edit_text);

		authVM = ViewModelProviders.of(this).get(Auth.class);

		authVM.getUserData().observe(this, sessionDataObserver);
		authVM.getSignInStatus().observe(this, loginObserver);
		authVM.getSignInError().observe(this, loginErrorObserver);

		binding.setLifecycleOwner(this);
		Auth.DataBinder dataBinder = authVM.new DataBinder();
		binding.setAuthVm(dataBinder);
	}

	public void launchSignUp(View view) {
		Intent intent = new Intent(this, SignUp.class);
		startActivity(intent);
	}

	public void launchForgotPassword(View view) {
		Intent intent = new Intent(this, ForgotPassword.class);
		startActivity(intent);
	}

	public void loginLogic(View view) {
		if(this.isLoggingIn) return;
		String errors = this.validateInput(authVM.email, authVM.password);
		if(!errors.isEmpty()){
			Common.shakeElement(this, view);
			return;
		}
		this.isLoggingIn = true;
		MaterialButton button = (MaterialButton) view;
		button.setAlpha((float)0.6);
		button.setText(R.string.signing_in);
		authVM.loginUser();
	}

	private String validateInput(String email, String password){
		emailEditText.setError(null);
		passwordEditText.setError(null);

		if(email.isEmpty()){
			emailEditText.setError("This is a Required Field");
			return "Ensure You Fill All Fields";

		}
		if(password.isEmpty()){
			passwordEditText.setError("This is a Required Field");
			return "Ensure You Fill All Fields";

		}
		if(!Common.checkEmail(email)){
			emailEditText.setError("Email is of Invalid Format");
			return "Email is of Invalid Format";
		}
		if(password.length() < 8){
			passwordEditText.setError("Password is too Short");
			return "Password is too Short";
		}
		return "";
	}
}
