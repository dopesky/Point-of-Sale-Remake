package com.herokuapp.pointofsale.Auth;

import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;

import com.google.gson.internal.LinkedTreeMap;
import com.herokuapp.pointofsale.Owner.OwnerDashboard;
import com.herokuapp.pointofsale.R;
import com.herokuapp.pointofsale.Resources.Common;
import com.herokuapp.pointofsale.Resources.CustomToast;
import com.herokuapp.pointofsale.api.Authentication.Login;

import java.io.IOException;
import java.util.HashMap;
import java.util.Objects;

public class MainActivity extends AppCompatActivity {
	private boolean isLoggingIn = false;
	private boolean showUIElements;
	private SharedPreferences sessionData;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		showUIElements = true;
		setContentView(R.layout.activity_main);

		sessionData = getSharedPreferences(Common.USERDATA, Context.MODE_PRIVATE);
		if(!sessionData.getAll().isEmpty()){
			if(sessionData.contains("message")){
				SharedPreferences.Editor editSessionData = sessionData.edit();
				editSessionData.remove("message");
				editSessionData.apply();
			}
			this.launchLoggedInActivity();
		}
	}

	@Override
	protected void onPause(){
		super.onPause();
		showUIElements = false;
	}

	@Override
	protected void onStop(){
		super.onStop();
		showUIElements = false;
	}

	@Override
	protected void onResume(){
		super.onResume();
		showUIElements = true;
		this.showUIElements();
	}

	@Override
	protected void onRestart(){
		super.onRestart();
		showUIElements = true;
		this.showUIElements();
	}

	private void showUIElements(){
		SharedPreferences.Editor editSessionData = this.sessionData.edit();
		if(this.sessionData.contains("message") && this.sessionData.getInt("message", 0) == 202){
			editSessionData.remove("message");
			editSessionData.apply();
			this.launchLoggedInActivity();
		}else if(this.sessionData.contains("message")){
			CustomToast.showToast(this, " " + this.sessionData.getString("message", "Hello User"), "danger");
			editSessionData.remove("message");
			editSessionData.apply();
		}
	}

	public void launchSignUp(View view) {
		Intent intent = new Intent(this, SignUp.class);
		startActivity(intent);
	}

	public void launchForgotPassword(View view) {
		Intent intent = new Intent(this, ForgotPassword.class);
		startActivity(intent);
	}

	public void launchLoggedInActivity(){
		Intent intent = new Intent(this, OwnerDashboard.class);
		this.startActivity(intent);
		this.finish();
	}

	public void loginLogic(View view) {
		if(this.isLoggingIn) return;
		String email = ((EditText)findViewById(R.id.email_edit_text)).getText().toString().trim();
		String password = ((EditText)findViewById(R.id.password_edit_text)).getText().toString();
		String errors = this.validateInput(email,password);
		if(!errors.isEmpty()){
			CustomToast.showToast(MainActivity.this, " " + errors, "danger");
			return;
		}
		this.isLoggingIn = true;
		Button button = (Button) view;
		button.setAlpha((float)0.6);
		button.setText(R.string.signing_in);
		Login login = MainActivity.loginUser(this, view);
		login.execute(email, password);
	}

	private String validateInput(String email, String password){
		if(email.isEmpty() || password.isEmpty()){
			return "Ensure You Fill All Fields";
		}
		if(!Common.checkEmail(email)){
			return "Email is of Invalid Format";
		}
		if(password.length() < 8){
			return "Password is too Short";
		}
		return "";
	}

	private static Login loginUser(MainActivity context,View view){
		Button button = (Button) view;
		return new Login(context.getString(R.string.API_KEY)){
			@Override
			protected void onPostExecute(Object response){
				String message = null;
				try {
					HashMap map = (HashMap) response;
					if ((double) map.get("status") == (double) 202 || (double) map.get("status") == (double) 200) {
						LinkedTreeMap data = (LinkedTreeMap) map.get("response");
						SharedPreferences.Editor editSessionData = context.sessionData.edit();
						if (data != null) {
							for (Object temp : data.keySet()) {
								editSessionData.putString(temp.toString(), Objects.requireNonNull(data.get(temp)).toString());
							}
						}
						if(context.showUIElements) {
							editSessionData.apply();
							context.launchLoggedInActivity();
						}else{
							editSessionData.putInt("message",202);
							editSessionData.apply();
						}
					} else {
						message = Common.parseHtml(Objects.requireNonNull(map.get("errors")));
					}
				}catch (ClassCastException cce1) {
					try{
						IOException ioe = (IOException) response;
						message = ioe.getMessage();
					}catch (Exception cce2) {
						message = "An Unnexpected Error Occurred. Contact Admin!";
					}
				}finally {
					if(context.showUIElements && message != null){
						CustomToast.showToast(context, " " + message, "danger");
					}else if(message != null){
						SharedPreferences.Editor editSessionData = context.sessionData.edit();
						editSessionData.putString("message", message);
						editSessionData.apply();
					}
					button.setAlpha((float) 1.0);
					button.setText(R.string.main_activity_header);
					context.isLoggingIn = false;
				}
			}
		};
	}
}
