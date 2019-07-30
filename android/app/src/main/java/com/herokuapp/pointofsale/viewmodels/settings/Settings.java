package com.herokuapp.pointofsale.models.settings;

import android.app.Application;
import android.content.Context;
import android.content.SharedPreferences;

import androidx.annotation.NonNull;
import androidx.lifecycle.AndroidViewModel;
import androidx.lifecycle.LiveData;
import androidx.lifecycle.MutableLiveData;

import com.google.gson.internal.LinkedTreeMap;
import com.herokuapp.pointofsale.api.retrofit.Annotations;
import com.herokuapp.pointofsale.resources.Common;

import java.util.HashMap;
import java.util.Objects;

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

		userdata.setValue(preferences);
	}

	public void fetchUserDetails(){
		Annotations service = getRetrofitInstance().create(Annotations.class);
		Call<HashMap> request = service.getUserDetails(preferences.getString("user_id", "-1"));
		request.enqueue(new Callback<HashMap>() {
			@EverythingIsNonNull
			@Override
			public void onResponse(Call<HashMap> call, Response<HashMap> response) {
				HashMap map = (response.isSuccessful()) ? response.body() : getErrorObject(response);

				if (map != null && Double.parseDouble(Objects.requireNonNull(map.get("status")).toString()) == (double)202 ) {
					userDetails.setValue((LinkedTreeMap) map.get("response"));
				} else{
					userDetails.setValue(null);
				}
			}

			@EverythingIsNonNull
			@Override
			public void onFailure(Call<HashMap> call, Throwable t) {
				userDetails.setValue(null);
			}
		});
	}
}
