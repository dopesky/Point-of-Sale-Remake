package com.herokuapp.pointofsale.Auth;

import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;

import com.herokuapp.pointofsale.R;
import com.herokuapp.pointofsale.Resources.Common;
import com.herokuapp.pointofsale.Resources.CustomToast;
import com.herokuapp.pointofsale.api.Authentication.Registration;

import java.io.IOException;
import java.util.HashMap;
import java.util.Objects;

public class ForgotPassword extends AppCompatActivity {
	private boolean isResetting = false;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_forgot_password);
	}

	public void launchMainActivity(View view) {
		if(this.isResetting) return;
		finish();
	}

	public void forgotPasswordLogic(View view) {
		if(this.isResetting) return;
		String email = ((EditText)findViewById(R.id.email_edit_text)).getText().toString().trim();
		String errors = this.validateInput(email);
		if(!errors.isEmpty()){
			CustomToast.showToast(this, " " + errors, "danger");
			return;
		}
		this.isResetting = true;
		Button button = (Button) view;
		button.setAlpha((float)0.7);
		button.setText(getString(R.string.signing_up));
		Registration registration = ForgotPassword.resetUserPassword(this, view);
		registration.execute("reset", email);
	}

	private String validateInput(String email){
		if(email.isEmpty()){
			return "Ensure You Fill All Fields";
		}
		if(!Common.checkEmail(email)){
			return "Email is of Invalid Format";
		}
		return "";
	}

	public static Registration resetUserPassword(ForgotPassword context, View view) {
		Button button = (Button) view;
		return new Registration(context.getString(R.string.API_KEY)){
			@Override
			protected void onPostExecute(Object response){
				try {
					HashMap map = (HashMap) response;
					if ((double) map.get("status") == (double) 202) {
						context.isResetting = false;
						context.launchMainActivity(view);
					} else {
						CustomToast.showToast(context, " " + Common.parseHtml(Objects.requireNonNull(map.get("errors"))), "danger");
					}
				} catch (ClassCastException cce) {
					IOException ioe = (IOException) response;
					CustomToast.showToast(context, " " + ioe.getMessage(), "danger");
				}finally {
					button.setAlpha((float) 1.0);
					button.setText(R.string.reset);
					context.isResetting = false;
				}
			}
		};
	}
}
