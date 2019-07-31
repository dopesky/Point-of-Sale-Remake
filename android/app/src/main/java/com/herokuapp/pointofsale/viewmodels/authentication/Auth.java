package com.herokuapp.pointofsale.viewmodels.authentication;

import android.app.Application;
import androidx.lifecycle.AndroidViewModel;
import androidx.lifecycle.LiveData;
import androidx.lifecycle.MutableLiveData;
import android.content.Context;
import android.content.SharedPreferences;
import androidx.databinding.BaseObservable;
import androidx.databinding.Bindable;
import android.location.Location;
import androidx.annotation.NonNull;

import com.google.gson.internal.LinkedTreeMap;
import com.herokuapp.pointofsale.api.retrofit.Annotations;
import com.herokuapp.pointofsale.resources.Common;
import java.util.HashMap;
import java.util.Objects;

import static com.herokuapp.pointofsale.api.retrofit.Common.*;

import okhttp3.internal.annotations.EverythingIsNonNull;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import retrofit2.Retrofit;

public class Auth extends AndroidViewModel{
	private final Retrofit retrofitInstance;

	public String email;
	public String password;
	public String codeInput;

	private SharedPreferences preferences;
	private MutableLiveData<SharedPreferences> userdata;
	public LiveData<SharedPreferences> getUserData(){return userdata;}

	public MutableLiveData<Location> userLocation;

	private MutableLiveData<Integer> signInStatus;
	public LiveData<Integer> getSignInStatus(){return signInStatus;}
	private MutableLiveData<String> signInError;
	public LiveData<String> getSignInError(){return signInError;}

	private MutableLiveData<Integer> signUpStatus;
	public LiveData<Integer> getSignUpStatus(){return signUpStatus;}
	private MutableLiveData<String> signUpError;
	public LiveData<String> getSignUpError(){return signUpError;}

	private MutableLiveData<Integer> resetStatus;
	public LiveData<Integer> getResetStatus(){return resetStatus;}
	private MutableLiveData<String> resetError;
	public LiveData<String> getResetError(){return resetError;}

	private MutableLiveData<Integer> FAStatus;
	public LiveData<Integer> get2FAStatus(){return FAStatus;}
	private MutableLiveData<String> FAError;
	public LiveData<String> get2FAError(){return FAError;}

	private MutableLiveData<String> emailError;
	public LiveData<String> getEmailError(){return emailError;}
	private MutableLiveData<Integer> emailStatus;
	public LiveData<Integer> getEmailStatus(){return emailStatus;}


	public Auth(@NonNull Application application) {
		super(application);
		userdata = new MutableLiveData<>();
		signInStatus = new MutableLiveData<>();
		signInError = new MutableLiveData<>();
		signUpStatus = new MutableLiveData<>();
		signUpError = new MutableLiveData<>();
		userLocation = new MutableLiveData<>();
		resetError = new MutableLiveData<>();
		resetStatus = new MutableLiveData<>();
		FAStatus = new MutableLiveData<>();
		FAError = new MutableLiveData<>();
		emailError = new MutableLiveData<>();
		emailStatus = new MutableLiveData<>();
		retrofitInstance = getRetrofitInstance();
		preferences = application.getSharedPreferences(Common.USERDATA, Context.MODE_PRIVATE);

		preferences.registerOnSharedPreferenceChangeListener((sharedPreferences, key) -> {
			if(sharedPreferences != null) {
				userdata.setValue(sharedPreferences);
			}
		});

		userdata.setValue(preferences);
		signInStatus.setValue(-1);
		signUpStatus.setValue(-1);
		resetStatus.setValue(-1);
		FAStatus.setValue(-1);
		emailStatus.setValue(-1);
		email = "";
		password = "";
		codeInput = "";
	}

	public void loginUser(){
		Annotations service = retrofitInstance.create(Annotations.class);
		Call<HashMap> request = service.loginUser(password, email);
		request.enqueue(new Callback<HashMap>() {
			@EverythingIsNonNull
			@Override
			public void onResponse(Call<HashMap> call, Response<HashMap> response) {
				HashMap map = (response.isSuccessful()) ? response.body() : getErrorObject(response);

				if ( map != null && Double.parseDouble(Objects.requireNonNull(map.get("status")).toString()) < (double)300 ) {
					SharedPreferences.Editor editor = Auth.this.preferences.edit();
					LinkedTreeMap sessionData = (LinkedTreeMap)Objects.requireNonNull(map.get("response"));
					for (Object obj: sessionData.keySet()) {
						editor.putString(obj.toString(), Objects.requireNonNull(sessionData.get(obj)).toString());
					}
					editor.putBoolean("2FA", Double.parseDouble(Objects.requireNonNull(map.get("status")).toString()) == (double)200);
					editor.apply();
					signInStatus.setValue(0);
				} else if(map != null) {
					signInStatus.setValue(1);
					signInError.setValue(Common.parseHtml(Objects.requireNonNull(map.get("errors")).toString()));
				} else {
					signInStatus.setValue(1);
					signInError.setValue("An Unnexpected Error Occurred");
				}
			}

			@EverythingIsNonNull
			@Override
			public void onFailure(Call<HashMap> call, Throwable t) {
				signInError.setValue(Common.parseHtml("<br><br><span>An Unnexpected Error Occurred.</span><br><br><span>Check Your Internet Connection</span>"));
				signInStatus.setValue(1);
			}
		});
	}

	public void signUpUser(Context context){
		Annotations service = getRetrofitInstance().create(Annotations.class);
		Call<HashMap> request = service.registerUser("admin", email, Common.getCountryFromLocation(context, userLocation.getValue()));
		request.enqueue(new Callback<HashMap>() {
			@EverythingIsNonNull
			@Override
			public void onResponse(Call<HashMap> call, Response<HashMap> response) {
				HashMap map = (response.isSuccessful()) ? response.body() : getErrorObject(response);

				if (map != null && Double.parseDouble(Objects.requireNonNull(map.get("status")).toString()) == (double)202 ) {
					signUpStatus.setValue(0);
				} else if(map != null) {
					signUpError.setValue(Common.parseHtml(Objects.requireNonNull(map.get("errors")).toString()));
					signUpStatus.setValue(1);
				}else{
					signUpError.setValue("An Unnexpected Error Occurred");
					signUpStatus.setValue(1);
				}
			}

			@EverythingIsNonNull
			@Override
			public void onFailure(Call<HashMap> call, Throwable t) {
				signUpError.setValue(Common.parseHtml("<br><br><span>An Unnexpected Error Occurred.</span><br><br><span>Check Your Internet Connection</span>"));
				signUpStatus.setValue(1);
			}
		});
	}

	public void resetUserPassword(){
		Annotations service = getRetrofitInstance().create(Annotations.class);
		Call<HashMap> request = service.requestPasswordReset(email);
		request.enqueue(new Callback<HashMap>() {
			@EverythingIsNonNull
			@Override
			public void onResponse(Call<HashMap> call, Response<HashMap> response) {
				HashMap map = (response.isSuccessful()) ? response.body() : getErrorObject(response);

				if (map != null && Double.parseDouble(Objects.requireNonNull(map.get("status")).toString()) == (double)202 ) {
					resetStatus.setValue(0);
				} else if(map != null) {
					resetError.setValue(Common.parseHtml(Objects.requireNonNull(map.get("errors")).toString()));
					resetStatus.setValue(1);
				}else{
					resetError.setValue("An Unnexpected Error Occurred");
					resetStatus.setValue(1);
				}
			}

			@EverythingIsNonNull
			@Override
			public void onFailure(Call<HashMap> call, Throwable t) {
				resetError.setValue(Common.parseHtml("<br><br><span>An Unnexpected Error Occurred.</span><br><br><span>Check Your Internet Connection</span>"));
				resetStatus.setValue(1);
			}
		});
	}

	public void verifyGoogleAuth(){
		Annotations service = getRetrofitInstance().create(Annotations.class);
		Call<HashMap> request = service.verifyGoogleAuth(codeInput, preferences.getString("user_id", "-1"));
		request.enqueue(new Callback<HashMap>() {
			@EverythingIsNonNull
			@Override
			public void onResponse(Call<HashMap> call, Response<HashMap> response) {
				HashMap map = (response.isSuccessful()) ? response.body() : getErrorObject(response);

				if (map != null && Double.parseDouble(Objects.requireNonNull(map.get("status")).toString()) == (double)202 ) {
					SharedPreferences.Editor editor = Auth.this.preferences.edit();
					editor.putBoolean("2FA", false);
					editor.apply();
					FAStatus.setValue(0);
				} else if(map != null) {
					FAStatus.setValue(1);
					if (Double.parseDouble(Objects.requireNonNull(map.get("status")).toString()) == (double)409 ) {
						FAError.setValue("Google Authentication is Unavailable at The Moment!");
						return;
					}
					FAError.setValue(Common.parseHtml(Objects.requireNonNull(map.get("errors")).toString()));
				}else{
					FAError.setValue("An Unnexpected Error Occurred");
					FAStatus.setValue(1);
				}
			}

			@EverythingIsNonNull
			@Override
			public void onFailure(Call<HashMap> call, Throwable t) {
				FAError.setValue(Common.parseHtml("<br><br><span>An Unnexpected Error Occurred.</span><br><br><span>Check Your Internet Connection</span>"));
				FAStatus.setValue(1);
			}
		});
	}

	public void sendEmailOTP(){
		Annotations service = getRetrofitInstance().create(Annotations.class);
		Call<HashMap> request = service.sendOTPEmail(preferences.getString("user_id", "-1"));
		request.enqueue(new Callback<HashMap>() {
			@EverythingIsNonNull
			@Override
			public void onResponse(Call<HashMap> call, Response<HashMap> response) {
				HashMap map = (response.isSuccessful()) ? response.body() : getErrorObject(response);

				if (map != null && Double.parseDouble(Objects.requireNonNull(map.get("status")).toString()) == (double)202 ) {
					emailStatus.setValue(0);
				} else if(map != null) {
					emailStatus.setValue(1);
					emailError.setValue(Common.parseHtml(Objects.requireNonNull(map.get("errors")).toString()));
				}else{
					emailStatus.setValue(1);
					emailError.setValue("An Unnexpected Error Occurred");
				}
			}

			@EverythingIsNonNull
			@Override
			public void onFailure(Call<HashMap> call, Throwable t) {
				emailStatus.setValue(1);
				emailError.setValue(Common.parseHtml("<br><br><span>An Unnexpected Error Occurred.</span><br><br><span>Check Your Internet Connection</span>"));
			}
		});
	}

	public void verifyEmailAuth(){
		Annotations service = getRetrofitInstance().create(Annotations.class);
		Call<HashMap> request = service.verifyEmailCode(codeInput, preferences.getString("user_id", "-1"));
		request.enqueue(new Callback<HashMap>() {
			@EverythingIsNonNull
			@Override
			public void onResponse(Call<HashMap> call, Response<HashMap> response) {
				HashMap map = (response.isSuccessful()) ? response.body() : getErrorObject(response);

				if (map != null && Double.parseDouble(Objects.requireNonNull(map.get("status")).toString()) == (double)202 ) {
					SharedPreferences.Editor editor = Auth.this.preferences.edit();
					editor.putBoolean("2FA", false);
					editor.apply();
					FAStatus.setValue(0);
				} else if(map != null) {
					FAStatus.setValue(1);
					FAError.setValue(Common.parseHtml(Objects.requireNonNull(map.get("errors")).toString()));
				}else{
					FAError.setValue("An Unnexpected Error Occurred");
					FAStatus.setValue(1);
				}
			}

			@EverythingIsNonNull
			@Override
			public void onFailure(Call<HashMap> call, Throwable t) {
				FAError.setValue(Common.parseHtml("<br><br><span>An Unnexpected Error Occurred.</span><br><br><span>Check Your Internet Connection</span>"));
				FAStatus.setValue(1);
			}
		});
	}

	public class DataBinder extends BaseObservable{
		@Bindable
		public String getEmailInput(){
			return email;
		}

		public void setEmailInput(String email){
			if(!email.equals(Auth.this.email)) {
				Auth.this.email = email;
				notifyChange();
			}
		}

		@Bindable
		public String getPasswordInput(){
			return password;
		}

		public void setPasswordInput(String password){
			if(!password.equals(Auth.this.password)) {
				Auth.this.password = password;
				notifyChange();
			}
		}

		@Bindable
		public String getCodeInput(){
			return codeInput;
		}

		public void setCodeInput(String codeInput){
			if(!codeInput.equals(Auth.this.codeInput)) {
				Auth.this.codeInput = codeInput;
				notifyChange();
			}
		}
	}
}
