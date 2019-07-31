package com.herokuapp.pointofsale.viewmodels.settings;

import android.app.Application;
import android.content.Context;
import android.content.SharedPreferences;

import androidx.annotation.NonNull;
import androidx.databinding.BaseObservable;
import androidx.databinding.Bindable;
import androidx.lifecycle.AndroidViewModel;
import androidx.lifecycle.LiveData;
import androidx.lifecycle.MutableLiveData;

import com.google.gson.internal.LinkedTreeMap;
import com.herokuapp.pointofsale.api.retrofit.Annotations;
import com.herokuapp.pointofsale.resources.Common;
import com.herokuapp.pointofsale.resources.NavigationBars;

import java.util.HashMap;
import java.util.Objects;

import okhttp3.MultipartBody;
import okhttp3.RequestBody;
import okhttp3.internal.annotations.EverythingIsNonNull;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

import static com.herokuapp.pointofsale.api.retrofit.Common.getErrorObject;
import static com.herokuapp.pointofsale.api.retrofit.Common.getRetrofitInstance;

public class Settings extends AndroidViewModel {

	private SharedPreferences preferences;
	private MutableLiveData<SharedPreferences> userdata;
	public LiveData<SharedPreferences> getUserData(){return userdata;}
	private MutableLiveData<LinkedTreeMap> userDetails;
	public LiveData<LinkedTreeMap> getCurrentUserDetails(){return userDetails;}
	private MutableLiveData<LinkedTreeMap> userData;
	public LiveData<LinkedTreeMap> getCurrentUserData(){return userData;}
	private MutableLiveData<Integer> updateStatus;
	public LiveData<Integer> getUpdateStatus(){return updateStatus;}
	private MutableLiveData<String> updateError;
	public LiveData<String> getUpdateError(){return updateError;}

	public Settings(@NonNull Application application) {
		super(application);
		preferences = application.getSharedPreferences(Common.USERDATA, Context.MODE_PRIVATE);
		preferences.registerOnSharedPreferenceChangeListener((sharedPreferences, key) -> {
			if(sharedPreferences != null) {
				userdata.setValue(sharedPreferences);
			}
		});

		userdata = new MutableLiveData<>();
		userDetails = new MutableLiveData<>();
		userData = new MutableLiveData<>();
		updateStatus = new MutableLiveData<>();
		updateError = new MutableLiveData<>();

		userdata.setValue(preferences);
		fetchUserDetails();
	}

	private void fetchUserDetails(){
		Annotations service = getRetrofitInstance().create(Annotations.class);
		Call<HashMap> request = service.getUserDetails(preferences.getString("user_id", "-1"));
		request.enqueue(new Callback<HashMap>() {
			@EverythingIsNonNull
			@Override
			public void onResponse(Call<HashMap> call, Response<HashMap> response) {
				HashMap map = (response.isSuccessful()) ? response.body() : getErrorObject(response);

				if (map != null && Double.parseDouble(Objects.requireNonNull(map.get("status")).toString()) == (double)202 ) {
					userDetails.setValue((LinkedTreeMap) map.get("response"));
					userData.setValue((LinkedTreeMap) map.get("response"));
				} else{
					userDetails.setValue(null);
					userData.setValue(null);
				}
			}

			@EverythingIsNonNull
			@Override
			public void onFailure(Call<HashMap> call, Throwable t) {
				userDetails.setValue(null);
				userData.setValue(null);
			}
		});
	}

	public void uploadPhoto(MultipartBody.Part photo){
		LinkedTreeMap map = userDetails.getValue();
		DataBinder binder = new DataBinder();
		if(map == null || map.get("user_id") == null || photo == null){
			updateStatus.setValue(1);
			updateError.setValue("An Unnexpected Error Occurred!");
			return;
		}
		String userID = Objects.requireNonNull(map.get("user_id")).toString();
		Annotations service = getRetrofitInstance().create(Annotations.class);
		Call<HashMap> request;
		if(binder.isOwner()){
			request = service.
					updateOwnerDetails(RequestBody.create(MultipartBody.FORM, userID),
							RequestBody.create(MultipartBody.FORM, Objects.requireNonNull(map.get("owner_fname")).toString()),
							RequestBody.create(MultipartBody.FORM, Objects.requireNonNull(map.get("owner_lname")).toString()),
							RequestBody.create(MultipartBody.FORM, Objects.requireNonNull(map.get("company")).toString()),
							photo);
		}else{
			request = service.
					updateEmployeeDetails(RequestBody.create(MultipartBody.FORM, userID),
							RequestBody.create(MultipartBody.FORM, Objects.requireNonNull(map.get("first_name")).toString()),
							RequestBody.create(MultipartBody.FORM, Objects.requireNonNull(map.get("last_name")).toString()),
							photo);
		}
		request.enqueue(new Callback<HashMap>() {
			@EverythingIsNonNull
			@Override
			public void onResponse(Call<HashMap> call, Response<HashMap> response) {
				HashMap map = (response.isSuccessful()) ? response.body() : getErrorObject(response);

				if ( map != null && Double.parseDouble(Objects.requireNonNull(map.get("status")).toString()) == (double)202 ) {
					SharedPreferences.Editor editor = Settings.this.preferences.edit();
					String fileName = Objects.requireNonNull(map.get("photo")).toString();
					binder.setImageSRC(fileName);
					editor.putString("photo", fileName);
					editor.apply();
					updateStatus.setValue(0);
				} else if(map != null) {
					updateStatus.setValue(1);
					updateError.setValue(Common.parseHtml(Objects.requireNonNull(map.get("errors")).toString()));
				} else {
					updateStatus.setValue(1);
					updateError.setValue("An Unnexpected Error Occurred");
				}
			}

			@EverythingIsNonNull
			@Override
			public void onFailure(Call<HashMap> call, Throwable t) {
				updateError.setValue(Common.parseHtml("<br><br><span>An Unnexpected Error Occurred.</span><br><br><span>Check Your Internet Connection</span>"));
				updateStatus.setValue(1);
			}
		});
	}

	public void updateUserDetails(){
		LinkedTreeMap map = userData.getValue();
		DataBinder binder = new DataBinder();
		if(map == null || map.get("user_id") == null){
			updateStatus.setValue(1);
			updateError.setValue("An Unnexpected Error Occurred!");
			return;
		}
		String userID = Objects.requireNonNull(map.get("user_id")).toString();
		Annotations service = getRetrofitInstance().create(Annotations.class);
		Call<HashMap> request;
		if(binder.isOwner()){
			request = service.
					updateOwnerDetails(RequestBody.create(MultipartBody.FORM, userID),
							RequestBody.create(MultipartBody.FORM, binder.getFirstName()),
							RequestBody.create(MultipartBody.FORM, binder.getLastName()),
							RequestBody.create(MultipartBody.FORM, binder.getCompanyName()));
		}else{
			request = service.
					updateEmployeeDetails(RequestBody.create(MultipartBody.FORM, userID),
							RequestBody.create(MultipartBody.FORM, binder.getFirstName()),
							RequestBody.create(MultipartBody.FORM, binder.getLastName()));
		}
		request.enqueue(new Callback<HashMap>() {
			@EverythingIsNonNull
			@Override
			public void onResponse(Call<HashMap> call, Response<HashMap> response) {
				HashMap map = (response.isSuccessful()) ? response.body() : getErrorObject(response);

				if ( map != null && Double.parseDouble(Objects.requireNonNull(map.get("status")).toString()) == (double)202 ) {
					SharedPreferences.Editor editor = Settings.this.preferences.edit();
					editor.putString("fname", binder.getFirstName());
					editor.putString("lname", binder.getLastName());
					editor.apply();
					updateStatus.setValue(0);
				} else if(map != null) {
					updateStatus.setValue(1);
					updateError.setValue(Common.parseHtml(Objects.requireNonNull(map.get("errors")).toString()));
				} else {
					updateStatus.setValue(1);
					updateError.setValue("An Unnexpected Error Occurred");
				}
			}

			@EverythingIsNonNull
			@Override
			public void onFailure(Call<HashMap> call, Throwable t) {
				updateError.setValue(Common.parseHtml("<br><br><span>An Unnexpected Error Occurred.</span><br><br><span>Check Your Internet Connection</span>"));
				updateStatus.setValue(1);
			}
		});
	}

	public void resetErrors(){
		updateError.setValue(null);
		updateStatus.setValue(-1);
	}

	public class DataBinder extends BaseObservable{
		@Bindable
		public String getImageSRC(){
			notifyChange();
			if(userData.getValue() != null && userData.getValue().get("id_owner") != null && userData.getValue().get("owner_photo") != null){
				return Objects.requireNonNull(userData.getValue().get("owner_photo")).toString();
			}else if(userData.getValue() != null && userData.getValue().get("owner_id") != null && userData.getValue().get("profile_photo") != null){
				return Objects.requireNonNull(userData.getValue().get("profile_photo")).toString();
			}else {
				return NavigationBars.BLANK_PROFILE_IMAGE;
			}
		}

		void setImageSRC(String image){
			LinkedTreeMap map = userData.getValue();
			if(map != null && map.get("id_owner") != null){
				map.put("owner_photo", image);
			}else if(map != null && map.get("owner_id") != null){
				map.put("profile_photo", image);
			}
			userData.setValue(map);
		}

		@Bindable
		public String getFirstName(){
			if(userData.getValue() != null && userData.getValue().get("id_owner") != null && userData.getValue().get("owner_fname") != null){
				return Objects.requireNonNull(userData.getValue().get("owner_fname")).toString();
			}else if(userData.getValue() != null && userData.getValue().get("owner_id") != null && userData.getValue().get("first_name") != null){
				return Objects.requireNonNull(userData.getValue().get("first_name")).toString();
			}
			return "";
		}

		public void setFirstName(String name){
			LinkedTreeMap map = userData.getValue();
			if(map != null && map.get("id_owner") != null){
				map.put("owner_fname", name);
			}else if(map != null && map.get("owner_id") != null){
				map.put("first_name", name);
			}
			userData.setValue(map);
		}

		@Bindable
		public String getLastName(){
			if(userData.getValue() != null && userData.getValue().get("id_owner") != null && userData.getValue().get("owner_lname") != null){
				return Objects.requireNonNull(userData.getValue().get("owner_lname")).toString();
			}else if(userData.getValue() != null && userData.getValue().get("owner_id") != null && userData.getValue().get("last_name") != null){
				return Objects.requireNonNull(userData.getValue().get("last_name")).toString();
			}
			return "";
		}

		public void setLastName(String name){
			LinkedTreeMap map = userData.getValue();
			if(map != null && map.get("id_owner") != null){
				map.put("owner_lname", name);
			}else if(map != null && map.get("owner_id") != null){
				map.put("last_name", name);
			}
			userData.setValue(map);
		}

		@Bindable
		public String getCompanyName(){
			if(userData.getValue() != null && userData.getValue().get("id_owner") != null && userData.getValue().get("company") != null){
				return Objects.requireNonNull(userData.getValue().get("company")).toString();
			}
			return "";
		}

		public void setCompanyName(String name){
			LinkedTreeMap map = userData.getValue();
			if(map != null && map.get("id_owner") != null){
				map.put("company", name);
			}
			userData.setValue(map);
		}

		@Bindable
		public boolean isOwner(){
			return userData.getValue() != null && userData.getValue().get("id_owner") != null;
		}
	}
}
