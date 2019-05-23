package com.herokuapp.pointofsale.Auth;

import android.content.Intent;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;

import com.herokuapp.pointofsale.R;
import com.herokuapp.pointofsale.Resources.Common;
import com.herokuapp.pointofsale.Resources.CustomToast;
import com.herokuapp.pointofsale.api.Authentication.Login;

import java.io.IOException;
import java.util.HashMap;
import java.util.Objects;

public class MainActivity extends AppCompatActivity {
	private boolean isLoggingIn = false;
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);
	}

	public void launchSignUp(View view) {
		if(this.isLoggingIn) return;
		Intent intent = new Intent(this, SignUp.class);
		startActivity(intent);
	}

	public void launchForgotPassword(View view) {
		if(this.isLoggingIn) return;
		Intent intent = new Intent(this, ForgotPassword.class);
		startActivity(intent);
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
				try {
					HashMap map = (HashMap) response;
					if ((double) map.get("status") == (double) 202 || (double) map.get("status") == (double) 200) {
						CustomToast.showToast(context, " Login Successful!", "success");
					} else {
						CustomToast.showToast(context, " " + Common.parseHtml(Objects.requireNonNull(map.get("errors"))), "danger");
					}
				} catch (ClassCastException cce) {
					IOException ioe = (IOException) response;
					CustomToast.showToast(context, " " + ioe.getMessage(), "danger");
				}finally {
					button.setAlpha((float) 1.0);
					button.setText(R.string.main_activity_header);
					context.isLoggingIn = false;
				}
			}
		};
	}
}
