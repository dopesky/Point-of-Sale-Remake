package com.herokuapp.pointofsale.models.owner;

import android.app.Application;
import androidx.lifecycle.AndroidViewModel;
import androidx.lifecycle.LiveData;
import androidx.lifecycle.MutableLiveData;
import android.content.Context;
import android.content.SharedPreferences;
import androidx.databinding.BaseObservable;
import androidx.databinding.Bindable;
import androidx.annotation.NonNull;

import com.google.gson.internal.LinkedTreeMap;
import com.herokuapp.pointofsale.api.Retrofit.Annotations;
import com.herokuapp.pointofsale.ui.resources.Common;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Objects;

import okhttp3.internal.annotations.EverythingIsNonNull;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

import static com.herokuapp.pointofsale.api.Retrofit.Common.getErrorObject;
import static com.herokuapp.pointofsale.api.Retrofit.Common.getRetrofitInstance;

public class Owner extends AndroidViewModel {
	private HashMap<String,String> employeeEditData;
	private HashMap<String,String> productEditData;
	private SharedPreferences preferences;
	private MutableLiveData<SharedPreferences> userdata;
	public LiveData<SharedPreferences> getUserData(){return userdata;}
	private MutableLiveData<LinkedTreeMap> userDetails;
	public LiveData<LinkedTreeMap> getCurrentUserDetails(){return userDetails;}

	private MutableLiveData<ArrayList> employees;
	public  LiveData<ArrayList> getEmployees(){return employees;}
	private MutableLiveData<String> getEmployeesError;
	public LiveData<String> getEmployeesError(){return getEmployeesError;}

	private MutableLiveData<ArrayList> products;
	public  LiveData<ArrayList> getProducts(){return products;}
	private MutableLiveData<String> getProductsError;
	public LiveData<String> getProductsError(){return getProductsError;}

	private MutableLiveData<ArrayList> departments;
	public LiveData<ArrayList> getDepartments(){return departments;}
	private MutableLiveData<String> getDepartmentsError;

	private MutableLiveData<ArrayList> categories;
	public LiveData<ArrayList> getCategories(){return categories;}
	private MutableLiveData<String> getCategoriesError;

	private MutableLiveData<String> updateEmployeeError;
	public LiveData<String> getUpdateEmployeeError(){return updateEmployeeError;}
	private MutableLiveData<Integer> updateEmployeeStatus;
	public LiveData<Integer> getUpdateEmployeeStatus(){return updateEmployeeStatus;}

	private MutableLiveData<String> addEmployeeError;
	public LiveData<String> getAddEmployeeError(){return addEmployeeError;}
	private MutableLiveData<Integer> addEmployeeStatus;
	public LiveData<Integer> getAddEmployeeStatus(){return addEmployeeStatus;}

	private MutableLiveData<String> addProductError;
	public LiveData<String> getAddProductError(){return addProductError;}
	private MutableLiveData<Integer> addProductStatus;
	public LiveData<Integer> getAddProductStatus(){return addProductStatus;}

	private MutableLiveData<String> updateProductError;
	public LiveData<String> getUpdateProductError(){return updateProductError;}
	private MutableLiveData<Integer> updateProductStatus;
	public LiveData<Integer> getUpdateProductStatus(){return updateProductStatus;}

	public Owner(@NonNull Application application){
		super(application);
		preferences = application.getSharedPreferences(Common.USERDATA, Context.MODE_PRIVATE);
		preferences.registerOnSharedPreferenceChangeListener((sharedPreferences, key) -> {
			if(sharedPreferences != null) {
				userdata.setValue(sharedPreferences);
			}
		});

		userdata = new MutableLiveData<>();
		employees = new MutableLiveData<>();
		products = new MutableLiveData<>();
		getEmployeesError = new MutableLiveData<>();
		getProductsError = new MutableLiveData<>();
		departments = new MutableLiveData<>();
		getDepartmentsError = new MutableLiveData<>();
		categories = new MutableLiveData<>();
		getCategoriesError = new MutableLiveData<>();
		updateEmployeeError = new MutableLiveData<>();
		updateEmployeeStatus = new MutableLiveData<>();
		updateProductError = new MutableLiveData<>();
		updateProductStatus = new MutableLiveData<>();
		addEmployeeStatus = new MutableLiveData<>();
		addEmployeeError = new MutableLiveData<>();
		addProductStatus = new MutableLiveData<>();
		addProductError = new MutableLiveData<>();
		userDetails = new MutableLiveData<>();
		employeeEditData = new HashMap<>();
		productEditData = new HashMap<>();

		userdata.setValue(preferences);
		getUserDetails();
	}

	public LiveData<HashMap<String, String>> getEmployeeData(){
		MutableLiveData<HashMap<String, String>> data =  new MutableLiveData<>();
		data.setValue(employeeEditData);
		return data;
	}

	public LiveData<HashMap<String, String>> getProductData(){
		MutableLiveData<HashMap<String, String>> data =  new MutableLiveData<>();
		data.setValue(productEditData);
		return data;
	}

	private void getUserDetails(){
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

	public void fetchEmployees(){
		Annotations service = getRetrofitInstance().create(Annotations.class);
		Call<HashMap> request = service.getOwnerEmployees(preferences.getString("user_id", "-1"));
		request.enqueue(new Callback<HashMap>() {
			@EverythingIsNonNull
			@Override
			public void onResponse(Call<HashMap> call, Response<HashMap> response) {
				HashMap map = (response.isSuccessful()) ? response.body() : getErrorObject(response);

				if (map != null && Double.parseDouble(Objects.requireNonNull(map.get("status")).toString()) == (double)202 ) {
					employees.setValue((ArrayList) map.get("response"));
				} else if(map != null) {
					getEmployeesError.setValue(Common.parseHtml(Objects.requireNonNull(map.get("errors")).toString()));
				}else{
					getEmployeesError.setValue("An Unnexpected Error Occurred");
				}
			}

			@EverythingIsNonNull
			@Override
			public void onFailure(Call<HashMap> call, Throwable t) {
				getEmployeesError.setValue(Common.parseHtml("<br><br><span>An Unnexpected Error Occurred.</span><br><br><span>Check Your Internet Connection</span>"));
			}
		});
	}

	public void fetchProducts(){
		Annotations service = getRetrofitInstance().create(Annotations.class);
		Call<HashMap> request = service.getOwnerProducts(preferences.getString("user_id", "-1"));
		request.enqueue(new Callback<HashMap>() {
			@EverythingIsNonNull
			@Override
			public void onResponse(Call<HashMap> call, Response<HashMap> response) {
				HashMap map = (response.isSuccessful()) ? response.body() : getErrorObject(response);

				if (map != null && Double.parseDouble(Objects.requireNonNull(map.get("status")).toString()) == (double)202 ) {
					products.setValue((ArrayList) map.get("response"));
				} else if(map != null) {
					getProductsError.setValue(Common.parseHtml(Objects.requireNonNull(map.get("errors")).toString()));
				}else{
					getProductsError.setValue("An Unnexpected Error Occurred");
				}
			}

			@EverythingIsNonNull
			@Override
			public void onFailure(Call<HashMap> call, Throwable t) {
				getProductsError.setValue(Common.parseHtml("<br><br><span>An Unnexpected Error Occurred.</span><br><br><span>Check Your Internet Connection</span>"));
			}
		});
	}

	public void fetchDepartments(){
		Annotations service = getRetrofitInstance().create(Annotations.class);
		Call<HashMap> request = service.getDepartments();
		request.enqueue(new Callback<HashMap>() {
			@EverythingIsNonNull
			@Override
			public void onResponse(Call<HashMap> call, Response<HashMap> response) {
				HashMap map = (response.isSuccessful()) ? response.body() : getErrorObject(response);

				if (map != null && Double.parseDouble(Objects.requireNonNull(map.get("status")).toString()) == (double)202 ) {
					departments.setValue((ArrayList) map.get("response"));
				} else if(map != null) {
					getDepartmentsError.setValue(Common.parseHtml(Objects.requireNonNull(map.get("errors")).toString()));
				}else{
					getDepartmentsError.setValue("An Unnexpected Error Occurred");
				}
			}

			@EverythingIsNonNull
			@Override
			public void onFailure(Call<HashMap> call, Throwable t) {
				getDepartmentsError.setValue(Common.parseHtml("<br><br><span>An Unnexpected Error Occurred.</span><br><br><span>Check Your Internet Connection</span>"));
			}
		});
	}

	public void fetchCategories(){
		Annotations service = getRetrofitInstance().create(Annotations.class);
		Call<HashMap> request = service.getCategories();
		request.enqueue(new Callback<HashMap>() {
			@EverythingIsNonNull
			@Override
			public void onResponse(Call<HashMap> call, Response<HashMap> response) {
				HashMap map = (response.isSuccessful()) ? response.body() : getErrorObject(response);

				if (map != null && Double.parseDouble(Objects.requireNonNull(map.get("status")).toString()) == (double)202 ) {
					categories.setValue((ArrayList) map.get("response"));
				} else if(map != null) {
					getCategoriesError.setValue(Common.parseHtml(Objects.requireNonNull(map.get("errors")).toString()));
				}else{
					getCategoriesError.setValue("An Unnexpected Error Occurred");
				}
			}

			@EverythingIsNonNull
			@Override
			public void onFailure(Call<HashMap> call, Throwable t) {
				getCategoriesError.setValue(Common.parseHtml("<br><br><span>An Unnexpected Error Occurred.</span><br><br><span>Check Your Internet Connection</span>"));
			}
		});
	}

	public void employ(){
		String userID = preferences.getString("user_id", "-1");
		String fname = employeeEditData.get("fname");
		String lname = employeeEditData.get("lname");
		String email = employeeEditData.get("email");
		String departmentID = employeeEditData.get("department_id");
		Annotations service = getRetrofitInstance().create(Annotations.class);
		Call<HashMap> request = service.addEmployee(userID, fname, lname, email, departmentID);
		request.enqueue(new Callback<HashMap>() {
			@EverythingIsNonNull
			@Override
			public void onResponse(Call<HashMap> call, Response<HashMap> response) {
				HashMap map = (response.isSuccessful()) ? response.body() : getErrorObject(response);

				if (map != null && Double.parseDouble(Objects.requireNonNull(map.get("status")).toString()) == (double)202 ) {
					addEmployeeStatus.setValue(0);
				} else if(map != null) {
					addEmployeeStatus.setValue(1);
					addEmployeeError.setValue(Common.parseHtml(Objects.requireNonNull(map.get("errors")).toString()));
				}else{
					addEmployeeStatus.setValue(1);
					addEmployeeError.setValue("An Unexpected Error Occurred");
				}
			}

			@EverythingIsNonNull
			@Override
			public void onFailure(Call<HashMap> call, Throwable t) {
				addEmployeeStatus.setValue(1);
				addEmployeeError.setValue(Common.parseHtml("<br><br><span>An Unexpected Error Occurred.</span><br><br><span>Check Your Internet Connection</span>"));
			}
		});
	}

	public void updateEmployee(){
		String userID = preferences.getString("user_id", "-1");
		String fname = employeeEditData.get("fname");
		String lname = employeeEditData.get("lname");
		String email = employeeEditData.get("email");
		String departmentID = employeeEditData.get("department_id");
		String employeeID = employeeEditData.get("employee_id");
		Annotations service = getRetrofitInstance().create(Annotations.class);
		Call<HashMap> request = service.updateEmployeeDetails(userID, employeeID, fname, lname, email, departmentID);
		request.enqueue(new Callback<HashMap>() {
			@EverythingIsNonNull
			@Override
			public void onResponse(Call<HashMap> call, Response<HashMap> response) {
				HashMap map = (response.isSuccessful()) ? response.body() : getErrorObject(response);

				if (map != null && Double.parseDouble(Objects.requireNonNull(map.get("status")).toString()) == (double)202 ) {
					updateEmployeeStatus.setValue(0);
				} else if(map != null) {
					updateEmployeeStatus.setValue(1);
					updateEmployeeError.setValue(Common.parseHtml(Objects.requireNonNull(map.get("errors")).toString()));
				}else{
					updateEmployeeStatus.setValue(1);
					updateEmployeeError.setValue("An Unexpected Error Occurred");
				}
			}

			@EverythingIsNonNull
			@Override
			public void onFailure(Call<HashMap> call, Throwable t) {
				updateEmployeeStatus.setValue(1);
				updateEmployeeError.setValue(Common.parseHtml("<br><br><span>An Unexpected Error Occurred.</span><br><br><span>Check Your Internet Connection</span>"));
			}
		});
	}

	public void unemployReemploy(){
		String action = Integer.parseInt(Objects.requireNonNull(employeeEditData.get("active"))) == 1 ? "unemploy" : "reemploy";
		String userID = preferences.getString("user_id", "-1");
		Annotations service = getRetrofitInstance().create(Annotations.class);
		Call<HashMap> request = service.unemployReemployEmployee(action, userID, employeeEditData.get("employee_id"));
		request.enqueue(new Callback<HashMap>() {
			@EverythingIsNonNull
			@Override
			public void onResponse(Call<HashMap> call, Response<HashMap> response) {
				HashMap map = (response.isSuccessful()) ? response.body() : getErrorObject(response);

				if (map != null && Double.parseDouble(Objects.requireNonNull(map.get("status")).toString()) == (double)202 ) {
					updateEmployeeStatus.setValue(0);
				} else if(map != null) {
					updateEmployeeStatus.setValue(1);
					updateEmployeeError.setValue(Common.parseHtml(Objects.requireNonNull(map.get("errors")).toString()));
				}else{
					updateEmployeeStatus.setValue(1);
					updateEmployeeError.setValue("An Unnexpected Error Occurred");
				}
			}

			@EverythingIsNonNull
			@Override
			public void onFailure(Call<HashMap> call, Throwable t) {
				updateEmployeeStatus.setValue(1);
				updateEmployeeError.setValue(Common.parseHtml("<br><br><span>An Unnexpected Error Occurred.</span><br><br><span>Check Your Internet Connection</span>"));
			}
		});
	}

	public void addProduct(){
		String userID = preferences.getString("user_id", "-1");
		String product = productEditData.get("product");
		String category = productEditData.get("category_id");
		String cost = productEditData.get("cost");
		Annotations service = getRetrofitInstance().create(Annotations.class);
		Call<HashMap> request = service.addProduct(userID, product, category, cost);
		request.enqueue(new Callback<HashMap>() {
			@EverythingIsNonNull
			@Override
			public void onResponse(Call<HashMap> call, Response<HashMap> response) {
				HashMap map = (response.isSuccessful()) ? response.body() : getErrorObject(response);

				if (map != null && Double.parseDouble(Objects.requireNonNull(map.get("status")).toString()) == (double)202 ) {
					addProductStatus.setValue(0);
				} else if(map != null) {
					addProductStatus.setValue(1);
					addProductError.setValue(Common.parseHtml(Objects.requireNonNull(map.get("errors")).toString()));
				}else{
					addProductStatus.setValue(1);
					addProductError.setValue("An Unexpected Error Occurred");
				}
			}

			@EverythingIsNonNull
			@Override
			public void onFailure(Call<HashMap> call, Throwable t) {
				addProductStatus.setValue(1);
				addProductError.setValue(Common.parseHtml("<br><br><span>An Unexpected Error Occurred.</span><br><br><span>Check Your Internet Connection</span>"));
			}
		});
	}

	public void updateProduct(){
		String userID = preferences.getString("user_id", "-1");
		String product = productEditData.get("product");
		String cost = productEditData.get("cost");
		String categoryID = productEditData.get("category_id");
		String productID = productEditData.get("product_id");
		Annotations service = getRetrofitInstance().create(Annotations.class);
		Call<HashMap> request = service.updateProductDetails(userID, productID, product, categoryID, cost);
		request.enqueue(new Callback<HashMap>() {
			@EverythingIsNonNull
			@Override
			public void onResponse(Call<HashMap> call, Response<HashMap> response) {
				HashMap map = (response.isSuccessful()) ? response.body() : getErrorObject(response);

				if (map != null && Double.parseDouble(Objects.requireNonNull(map.get("status")).toString()) == (double)202 ) {
					updateProductStatus.setValue(0);
				} else if(map != null) {
					updateProductStatus.setValue(1);
					updateProductError.setValue(Common.parseHtml(Objects.requireNonNull(map.get("errors")).toString()));
				}else{
					updateProductStatus.setValue(1);
					updateProductError.setValue("An Unexpected Error Occurred");
				}
			}

			@EverythingIsNonNull
			@Override
			public void onFailure(Call<HashMap> call, Throwable t) {
				updateProductStatus.setValue(1);
				updateProductError.setValue(Common.parseHtml("<br><br><span>An Unexpected Error Occurred.</span><br><br><span>Check Your Internet Connection</span>"));
			}
		});
	}

	public void enableDisableProduct(){
		String action = Integer.parseInt(Objects.requireNonNull(productEditData.get("active"))) == 1 ? "deactivate" : "reactivate";
		String userID = preferences.getString("user_id", "-1");
		Annotations service = getRetrofitInstance().create(Annotations.class);
		Call<HashMap> request = service.disableEnableProduct(action, userID, productEditData.get("product_id"));
		request.enqueue(new Callback<HashMap>() {
			@EverythingIsNonNull
			@Override
			public void onResponse(Call<HashMap> call, Response<HashMap> response) {
				HashMap map = (response.isSuccessful()) ? response.body() : getErrorObject(response);

				if (map != null && Double.parseDouble(Objects.requireNonNull(map.get("status")).toString()) == (double)202 ) {
					updateProductStatus.setValue(0);
				} else if(map != null) {
					updateProductStatus.setValue(1);
					updateProductError.setValue(Common.parseHtml(Objects.requireNonNull(map.get("errors")).toString()));
				}else{
					updateProductStatus.setValue(1);
					updateProductError.setValue("An Unnexpected Error Occurred");
				}
			}

			@EverythingIsNonNull
			@Override
			public void onFailure(Call<HashMap> call, Throwable t) {
				updateProductStatus.setValue(1);
				updateProductError.setValue(Common.parseHtml("<br><br><span>An Unnexpected Error Occurred.</span><br><br><span>Check Your Internet Connection</span>"));
			}
		});
	}

	public class DataBinder extends BaseObservable {
		@Bindable
		public String getFirstName(){
			return (employeeEditData.containsKey("fname")) ? employeeEditData.get("fname") : "";
		}

		public void setFirstName(String fname){
			employeeEditData.put("fname", fname);
		}

		@Bindable
		public String getLastName(){
			return (employeeEditData.containsKey("lname")) ? employeeEditData.get("lname") : "";
		}

		public void setLastName(String lname){
			employeeEditData.put("lname", lname);
		}

		@Bindable
		public String getEmail(){
			return (employeeEditData.containsKey("email")) ? employeeEditData.get("email") : "";
		}

		public void setEmail(String email){
			employeeEditData.put("email", email);
		}

		@Bindable
		public String getDepartment(){
			return (employeeEditData.containsKey("department_id")) ? employeeEditData.get("department_id") : "";
		}

		public void setDepartment(String department){
			employeeEditData.put("department_id", department);
		}

		@Bindable
		public String getEmployeeActive(){
			return (employeeEditData.containsKey("active")) ? employeeEditData.get("active") : "";
		}

		public void setEmployeeActive(String active){
			employeeEditData.put("active", active);
		}

		@Bindable
		public String getEmployeeID(){
			return (employeeEditData.containsKey("employee_id")) ? employeeEditData.get("employee_id") : "";
		}

		public void setEmployeeID(String employeeID){
			employeeEditData.put("employee_id", employeeID);
		}

		@Bindable
		public String getProductName(){
			return (productEditData.containsKey("product")) ? productEditData.get("product") : "";
		}

		public void setProductName(String product){
			productEditData.put("product", product);
		}

		@Bindable
		public String getCost(){
			return (productEditData.containsKey("cost")) ? productEditData.get("cost") : "";
		}

		public void setCost(String cost){
			productEditData.put("cost", cost);
		}

		@Bindable
		public String getCategory(){
			return (productEditData.containsKey("category_id")) ? productEditData.get("category_id") : "";
		}

		public void setCategory(String category){
			productEditData.put("category_id", category);
		}

		@Bindable
		public String getProductActive(){
			return (productEditData.containsKey("active")) ? productEditData.get("active") : "";
		}

		public void setProductActive(String active){
			productEditData.put("active", active);
		}

		@Bindable
		public String getProductID(){
			return (productEditData.containsKey("product_id")) ? productEditData.get("product_id") : "";
		}

		public void setProductID(String productID){
			productEditData.put("product_id", productID);
		}
	}
}
