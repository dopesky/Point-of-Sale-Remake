package com.herokuapp.pointofsale.models.pos;

import android.app.Application;
import android.arch.lifecycle.AndroidViewModel;
import android.arch.lifecycle.LiveData;
import android.arch.lifecycle.MutableLiveData;
import android.content.Context;
import android.content.SharedPreferences;
import android.support.annotation.NonNull;
import android.support.v4.util.Pair;

import com.google.gson.internal.LinkedTreeMap;
import com.herokuapp.pointofsale.api.Retrofit.Annotations;
import com.herokuapp.pointofsale.ui.resources.Common;

import java.io.IOException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Objects;

import okhttp3.internal.annotations.EverythingIsNonNull;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

import static com.herokuapp.pointofsale.api.Retrofit.Common.getErrorObject;
import static com.herokuapp.pointofsale.api.Retrofit.Common.getRetrofitInstance;

public class Purchases extends AndroidViewModel {

	private MutableLiveData<ArrayList> purchaseProducts;
	public LiveData<ArrayList> getPurchaseProducts(){return purchaseProducts;}

	private SharedPreferences preferences;
	private MutableLiveData<SharedPreferences> userdata;
	public LiveData<SharedPreferences> getUserData(){return userdata;}
	private MutableLiveData<LinkedTreeMap> userDetails;
	public LiveData<LinkedTreeMap> getCurrentUserDetails(){return userDetails;}

	private MutableLiveData<Integer> addPurchaseStatus;
	public LiveData<Integer> getAddPurchaseStatus(){return addPurchaseStatus;}
	private MutableLiveData<String> addPurchaseError;
	public LiveData<String> getAddPurchaseError(){return addPurchaseError;}


	public Purchases(@NonNull Application application) {
		super(application);
		preferences = application.getSharedPreferences(Common.USERDATA, Context.MODE_PRIVATE);
		preferences.registerOnSharedPreferenceChangeListener((sharedPreferences, key) -> {
			if(sharedPreferences != null) {
				userdata.setValue(sharedPreferences);
			}
		});

		userdata = new MutableLiveData<>();
		purchaseProducts = new MutableLiveData<>();
		userDetails = new MutableLiveData<>();
		addPurchaseStatus = new MutableLiveData<>();
		addPurchaseError = new MutableLiveData<>();

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

	public void fetchPurchaseProducts(){
		String owner_id = Objects.requireNonNull(userDetails.getValue()).get("owner_id") != null ?
				Objects.requireNonNull(userDetails.getValue().get("owner_id")).toString() :
				Objects.requireNonNull(userDetails.getValue().get("id_owner")).toString();
		Annotations service = getRetrofitInstance().create(Annotations.class);
		Call<HashMap> request = service.getProductsForPurchase(owner_id);
		request.enqueue(new Callback<HashMap>() {
			@EverythingIsNonNull
			@Override
			public void onResponse(Call<HashMap> call, Response<HashMap> response) {
				HashMap map = (response.isSuccessful()) ? response.body() : getErrorObject(response);

				if (map != null && Double.parseDouble(Objects.requireNonNull(map.get("status")).toString()) == (double)202 ) {
					purchaseProducts.setValue((ArrayList) map.get("response"));
				}else{
					purchaseProducts.setValue(null);
				}
			}

			@EverythingIsNonNull
			@Override
			public void onFailure(Call<HashMap> call, Throwable t) {
				purchaseProducts.setValue(null);
			}
		});
	}

	public void addPurchases(ArrayList<LinkedTreeMap<String, String>> selectedData){
		LinkedTreeMap<String, String> dataSelected = Common.makePosRequestBody(selectedData);
		Annotations service = getRetrofitInstance().create(Annotations.class);
		Call<HashMap> request = service.addPurchase(dataSelected, preferences.getString("user_id", "-1"));
		request.enqueue(new Callback<HashMap>() {
			@EverythingIsNonNull
			@Override
			public void onResponse(Call<HashMap> call, Response<HashMap> response) {
				HashMap map = (response.isSuccessful()) ? response.body() : getErrorObject(response);

				if (map != null && Double.parseDouble(Objects.requireNonNull(map.get("status")).toString()) == (double)202 ) {
					addPurchaseStatus.setValue(0);
				}else if(map != null){
					addPurchaseStatus.setValue(1);
					addPurchaseError.setValue(Common.parseHtml(Objects.requireNonNull(map.get("errors")).toString()));
				}else{
					addPurchaseStatus.setValue(1);
					addPurchaseError.setValue("An Unnexpected Error Occurred");
				}
			}

			@EverythingIsNonNull
			@Override
			public void onFailure(Call<HashMap> call, Throwable t) {
				addPurchaseStatus.setValue(1);
				addPurchaseError.setValue(Common.parseHtml("<br><br><span>An Unnexpected Error Occurred.</span><br><br><span>Check Your Internet Connection</span>"));
			}
		});
	}
}
