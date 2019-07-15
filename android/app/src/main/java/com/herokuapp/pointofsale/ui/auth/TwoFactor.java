package com.herokuapp.pointofsale.ui.auth;

import androidx.lifecycle.Observer;
import androidx.lifecycle.ViewModelProviders;
import android.content.Intent;
import android.content.SharedPreferences;
import androidx.databinding.DataBindingUtil;
import androidx.constraintlayout.widget.ConstraintLayout;
import com.google.android.material.button.MaterialButton;
import com.google.android.material.textfield.TextInputLayout;
import androidx.appcompat.app.AppCompatActivity;
import android.os.Bundle;
import android.view.View;

import com.herokuapp.pointofsale.R;
import com.herokuapp.pointofsale.databinding.ActivityTwoFactorBinding;
import com.herokuapp.pointofsale.models.authentication.Auth;
import com.herokuapp.pointofsale.ui.owner.OwnerDashboard;
import com.herokuapp.pointofsale.ui.resources.Common;
import com.herokuapp.pointofsale.ui.resources.CustomToast;

public class TwoFactor extends AppCompatActivity {
	private boolean isVerifying = false;
	private boolean isSending = false;
	private MaterialButton googleButton;
	private MaterialButton email_button;
	private  MaterialButton emailAuthButton;
	private Auth authVM;

	private Observer<SharedPreferences> sessionDataObserver = sharedPreferences -> {
		if(sharedPreferences == null || !sharedPreferences.contains("user_id")){
			launchMainActivity();
		}else if(!sharedPreferences.getBoolean("2FA", true)){
			launchLoggedInActivity(false);
		}
	};
	private Observer<Integer> loginObserver = loginStatus ->{
		if(loginStatus != null && loginStatus == 0){
			launchLoggedInActivity(true);
		}
		googleButton.setAlpha(1.0f);
		googleButton.setText(getString(R.string.verify));
		emailAuthButton.setAlpha(1.0f);
		emailAuthButton.setText(getString(R.string.verify));
		isVerifying = false;
	};
	private Observer<String> loginErrorObserver = loginError -> {
		if(loginError != null && loginError.trim().length() > 0){
			CustomToast.showToast(this, " " + loginError, "danger");
		}
	};
	private Observer<String> emailErrorObserver = loginError -> {
		if(loginError != null && loginError.trim().length() > 0){
			CustomToast.showToast(this, " " + loginError, "danger");
		}
	};
	private Observer<Integer> emailStatusObserver = emailStatus -> {
		if(emailStatus != null && emailStatus == 0)	{
			CustomToast.showToast(this, " Code Successfully Sent. Check email!", "success");
			email_button.setText(getString(R.string.resend_code));
		}else{
			email_button.setText(getString(R.string.send_code));
		}
		email_button.setAlpha(1.0f);
		isSending = false;
	};

	private ConstraintLayout codeButton;
	private TextInputLayout codeEditText;
	private ConstraintLayout emailButton;
	private TextInputLayout emailEditText;


	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		ActivityTwoFactorBinding binding = DataBindingUtil.setContentView(this, R.layout.activity_two_factor);
		codeButton = findViewById(R.id.code_button);
		codeEditText = findViewById(R.id.code_edit_text);
		emailEditText = findViewById(R.id.email_edit_text);
		emailButton = findViewById(R.id.emailLayout);
		googleButton = findViewById(R.id.google_authenticator_button);
		email_button = findViewById(R.id.email_button);
		emailAuthButton = findViewById(R.id.email_authenticator_button);

		authVM = ViewModelProviders.of(this).get(Auth.class);
		authVM.getUserData().observe(this, sessionDataObserver);
		authVM.get2FAStatus().observe(this, loginObserver);
		authVM.get2FAError().observe(this, loginErrorObserver);
		authVM.getEmailError().observe(this, emailErrorObserver);
		authVM.getEmailStatus().observe(this, emailStatusObserver);

		Auth.DataBinder dataBinder = authVM.new DataBinder();
		binding.setLifecycleOwner(this);
		binding.setAuthVm(dataBinder);
	}

	public void launchMainActivity(){
		Intent intent = new Intent(this, MainActivity.class);
		intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK | Intent.FLAG_ACTIVITY_TASK_ON_HOME);
		startActivity(intent);
		overridePendingTransition(0,0);
	}

	public void launchLoggedInActivity(boolean showAnimation){
		Intent intent = new Intent(this, OwnerDashboard.class);
		intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK | Intent.FLAG_ACTIVITY_TASK_ON_HOME);
		startActivity(intent);
		if(!showAnimation)
			overridePendingTransition(0,0);
	}

	public void verifyGoogleCode(View view) {
		if(isVerifying) return;
		String errors = this.validateCode(authVM.codeInput, "google");
		if(!errors.isEmpty()){
			Common.shakeElement(this, view);
			return;
		}
		isVerifying = true;
		view.setAlpha(0.6f);
		((MaterialButton) view).setText(getString(R.string.verifying));
		authVM.verifyGoogleAuth();
	}

	public void sendEmail(View view) {
		if(isSending || isVerifying) return;
		isSending = true;
		view.setAlpha(0.6f);
		((MaterialButton) view).setText(getString(R.string.sending));
		authVM.sendEmailOTP();
	}

	public void verifyEmailCode(View view) {
		if(isVerifying || isSending) return;
		String errors = this.validateCode(authVM.codeInput, "email");
		if(!errors.isEmpty()){
			Common.shakeElement(this, view);
			return;
		}
		isVerifying = true;
		view.setAlpha(0.6f);
		((MaterialButton) view).setText(getString(R.string.verifying));
		authVM.verifyEmailAuth();
	}

	private String validateCode(String code, String type){
		TextInputLayout layout = (type.toLowerCase().equals("google")) ? codeEditText : emailEditText;
		if(code == null || code.trim().length() < 1){
			layout.setError("This is a Required Field");
			return "This is a Required Field";
		}
		try {
			int codeInt = Integer.parseInt(code);
			return "";
		}catch (NumberFormatException nfe){
			layout.setError("This Must be a Number");
			return "This Must be a Number";
		}
	}

	public void googleCollapse(View view) {
		MaterialButton emailToggle = findViewById(R.id.email_toggle);
		if(codeButton.getVisibility() == View.GONE){
			codeButton.setVisibility(View.VISIBLE);
			codeEditText.setVisibility(View.VISIBLE);
			Common.rotateElement(view, 180f, 0f, 150);
			emailButton.setVisibility(View.GONE);
			emailEditText.setVisibility(View.GONE);
			Common.rotateElement(emailToggle, 180f, 0f, 0);
		}else{
			codeButton.setVisibility(View.GONE);
			codeEditText.setVisibility(View.GONE);
			Common.rotateElement(view, 0f, 180f, 150);
			emailButton.setVisibility(View.VISIBLE);
			emailEditText.setVisibility(View.VISIBLE);
			Common.rotateElement(emailToggle, 0f, 180f, 0);
		}
	}

	public void emailCollapse(View view) {
		MaterialButton googleToggle = findViewById(R.id.google_toggle);
		if(emailButton.getVisibility() == View.GONE){
			emailButton.setVisibility(View.VISIBLE);
			emailEditText.setVisibility(View.VISIBLE);
			Common.rotateElement(view, 0f, 180f, 150);
			codeButton.setVisibility(View.GONE);
			codeEditText.setVisibility(View.GONE);
			Common.rotateElement(googleToggle, 0f, 180f, 0);
		}else{
			emailButton.setVisibility(View.GONE);
			emailEditText.setVisibility(View.GONE);
			Common.rotateElement(view, 180f, 0f, 150);
			codeButton.setVisibility(View.VISIBLE);
			codeEditText.setVisibility(View.VISIBLE);
			Common.rotateElement(googleToggle, 180f, 0f, 0);
		}
	}
}
