package com.herokuapp.pointofsale.Auth;

import android.arch.lifecycle.LiveData;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.view.View;
import android.view.animation.Animation;
import android.view.animation.AnimationUtils;
import android.widget.Button;
import android.widget.EditText;

import com.herokuapp.pointofsale.R;
import com.herokuapp.pointofsale.Resources.Common;
import com.herokuapp.pointofsale.Resources.CustomToast;
import com.herokuapp.pointofsale.api.Common.Annotations;

import java.io.IOException;
import java.util.HashMap;
import java.util.Objects;

import okhttp3.internal.annotations.EverythingIsNonNull;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

import static com.herokuapp.pointofsale.api.Common.Common.*;

public class SignUp extends AppCompatActivity {
	private boolean isSigningUp = false;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_sign_up);
	}

	public void launchMainActivity(View view) {
		finish();
	}

	public void signUpLogic(View view) {
		if(this.isSigningUp) return;
		String email = ((EditText)findViewById(R.id.email_edit_text)).getText().toString().trim();
		String errors = this.validateInput(email);
		if(!errors.isEmpty()){
			final Animation shakeAnimation = AnimationUtils.loadAnimation(this,R.anim.shake);
			view.startAnimation(shakeAnimation);
			CustomToast.showToast(this, " " + errors, "danger");
			return;
		}
		this.isSigningUp = true;
		Button button = (Button) view;
		button.setAlpha((float)0.6);
		button.setText(getString(R.string.signing_up));

		Annotations service = getRetrofitInstance().create(Annotations.class);
		Call<HashMap> request = service.registerUser("admin", email);

		request.enqueue(new Callback<HashMap>() {
			@EverythingIsNonNull
			@Override
			public void onResponse(Call<HashMap> call, Response<HashMap> response) {
				HashMap map = (response.isSuccessful()) ? response.body() : getErrorObject(response);
				assert map != null;

				if ( Double.parseDouble(Objects.requireNonNull(map.get("status")).toString()) == (double)202 ) {
					CustomToast.showToast(SignUp.this, " Sign up Successful! Please Check Your Email!", "success");
					SignUp.this.isSigningUp = false;
					SignUp.this.launchMainActivity(view);
				} else {
					CustomToast.showToast(SignUp.this, " " + Common.parseHtml(Objects.requireNonNull(map.get("errors")).toString()), "danger");
				}

				button.setAlpha((float) 1.0);
				button.setText(R.string.create_account);
				SignUp.this.isSigningUp = false;
			}

			@EverythingIsNonNull
			@Override
			public void onFailure(Call<HashMap> call, Throwable t) {
				if(t instanceof IOException){
					CustomToast.showToast(SignUp.this, " " + t.getMessage(), "danger");
				}else{
					CustomToast.showToast(SignUp.this, " An Unnexpected Error Occurred. Contact Admin!", "danger");
				}

				button.setAlpha((float) 1.0);
				button.setText(R.string.create_account);
				SignUp.this.isSigningUp = false;
			}
		});
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
}
