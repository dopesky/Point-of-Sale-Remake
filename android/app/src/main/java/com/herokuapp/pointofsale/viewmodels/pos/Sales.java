package com.herokuapp.pointofsale.viewmodels.pos;

import android.app.Application;
import androidx.lifecycle.AndroidViewModel;
import androidx.lifecycle.LiveData;
import androidx.lifecycle.MutableLiveData;
import android.content.Context;
import android.content.SharedPreferences;
import androidx.annotation.NonNull;

import com.google.gson.internal.LinkedTreeMap;
import com.herokuapp.pointofsale.api.retrofit.Annotations;
import com.herokuapp.pointofsale.resources.Common;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Objects;

import okhttp3.internal.annotations.EverythingIsNonNull;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

import static com.herokuapp.pointofsale.api.retrofit.Common.getErrorObject;
import static com.herokuapp.pointofsale.api.retrofit.Common.getRetrofitInstance;

public class Sales extends AndroidViewModel {

	private MutableLiveData<ArrayList> saleProducts;
	public LiveData<ArrayList> getSaleProducts(){return saleProducts;}

	private  MutableLiveData<ArrayList<LinkedTreeMap<String, String>>> selectedData;
	public LiveData<ArrayList<LinkedTreeMap<String, String>>> getSelectedData(){return selectedData;}

	private SharedPreferences preferences;
	private MutableLiveData<SharedPreferences> userdata;
	public LiveData<SharedPreferences> getUserData(){return userdata;}
	private MutableLiveData<LinkedTreeMap> userDetails;
	public LiveData<LinkedTreeMap> getCurrentUserDetails(){return userDetails;}

	private MutableLiveData<Integer> addSaleStatus;
	public LiveData<Integer> getAddSaleStatus(){return addSaleStatus;}
	private MutableLiveData<String> addSaleError;
	public LiveData<String> getAddSaleError(){return addSaleError;}


	public Sales(@NonNull Application application) {
		super(application);
		preferences = application.getSharedPreferences(Common.USERDATA, Context.MODE_PRIVATE);
		preferences.registerOnSharedPreferenceChangeListener((sharedPreferences, key) -> {
			if(sharedPreferences != null) {
				userdata.setValue(sharedPreferences);
			}
		});

		userdata = new MutableLiveData<>();
		saleProducts = new MutableLiveData<>();
		userDetails = new MutableLiveData<>();
		addSaleStatus = new MutableLiveData<>();
		addSaleError = new MutableLiveData<>();
		selectedData = new MutableLiveData<>();

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

	public void fetchSaleProducts(){
		String owner_id = Objects.requireNonNull(userDetails.getValue()).get("owner_id") != null ?
				Objects.requireNonNull(userDetails.getValue().get("owner_id")).toString() :
				Objects.requireNonNull(userDetails.getValue().get("id_owner")).toString();
		Annotations service = getRetrofitInstance().create(Annotations.class);
		Call<HashMap> request = service.getProductsForSale(owner_id);
		request.enqueue(new Callback<HashMap>() {
			@EverythingIsNonNull
			@Override
			public void onResponse(Call<HashMap> call, Response<HashMap> response) {
				HashMap map = (response.isSuccessful()) ? response.body() : getErrorObject(response);

				if (map != null && Double.parseDouble(Objects.requireNonNull(map.get("status")).toString()) == (double)202 ) {
					saleProducts.setValue((ArrayList) map.get("response"));
				}else{
					saleProducts.setValue(null);
				}
			}

			@EverythingIsNonNull
			@Override
			public void onFailure(Call<HashMap> call, Throwable t) {
				saleProducts.setValue(null);
			}
		});
	}

	public void addSales(String methodID){
		if(selectedData.getValue() == null || selectedData.getValue().isEmpty()){
			addSaleStatus.setValue(1);
			addSaleError.setValue("No Sales Made Yet!");
			return;
		}
		LinkedTreeMap<String, String> dataSelected = Common.makePosRequestBody(selectedData.getValue(), methodID);
		Annotations service = getRetrofitInstance().create(Annotations.class);
		Call<HashMap> request = service.addSale(dataSelected, preferences.getString("user_id", "-1"));
		request.enqueue(new Callback<HashMap>() {
			@EverythingIsNonNull
			@Override
			public void onResponse(Call<HashMap> call, Response<HashMap> response) {
				HashMap map = (response.isSuccessful()) ? response.body() : getErrorObject(response);

				if (map != null && Double.parseDouble(Objects.requireNonNull(map.get("status")).toString()) == (double)202 ) {
					addSaleStatus.setValue(0);
				}else if(map != null){
					addSaleStatus.setValue(1);
					addSaleError.setValue(Common.parseHtml(Objects.requireNonNull(map.get("errors")).toString()));
				}else{
					addSaleStatus.setValue(1);
					addSaleError.setValue("An Unnexpected Error Occurred");
				}
			}

			@EverythingIsNonNull
			@Override
			public void onFailure(Call<HashMap> call, Throwable t) {
				addSaleStatus.setValue(1);
				addSaleError.setValue(Common.parseHtml("<br><br><span>An Unnexpected Error Occurred.</span><br><br><span>Check Your Internet Connection</span>"));
			}
		});
	}

	public void addSelectedData(ArrayList<LinkedTreeMap<String, String>> selectedData){
		this.selectedData.setValue(selectedData);
	}

	public void addStatus(int status){
		addSaleStatus.setValue(status);
	}
}
