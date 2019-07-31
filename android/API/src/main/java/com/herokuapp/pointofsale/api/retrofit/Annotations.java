package com.herokuapp.pointofsale.api.retrofit;

import java.util.HashMap;
import java.util.Map;

import okhttp3.MultipartBody;
import okhttp3.RequestBody;
import retrofit2.Call;
import retrofit2.http.Field;
import retrofit2.http.FieldMap;
import retrofit2.http.FormUrlEncoded;
import retrofit2.http.GET;
import retrofit2.http.Multipart;
import retrofit2.http.POST;
import retrofit2.http.Part;
import retrofit2.http.Path;

public interface Annotations {
	@GET("jsons/get_user_details/{user_id}")
	Call<HashMap> getUserDetails(@Path("user_id") String user_id);

	@FormUrlEncoded
	@POST("auth/register/{type}")
	Call<HashMap> registerUser(@Path("type") String userType, @Field("email") String email, @Field("country") String country);

	@FormUrlEncoded
	@POST("auth/login")
	Call<HashMap> loginUser(@Field("password") String password, @Field("email") String email);

	@FormUrlEncoded
	@POST("auth/request_reset")
	Call<HashMap> requestPasswordReset(@Field("email") String email);

	@GET("auth/verify_google_auth/{token}/{user_id}")
	Call<HashMap> verifyGoogleAuth(@Path("token") String token, @Path("user_id") String user_id);

	@GET("auth/send_email_otp/{user_id}")
	Call<HashMap> sendOTPEmail(@Path("user_id") String user_id);

	@GET("auth/verify_email_token/{token}/{user_id}")
	Call<HashMap> verifyEmailCode(@Path("token") String token, @Path("user_id") String user_id);

	@GET("jsons/get_valid_departments")
	Call<HashMap> getDepartments();

	@GET("jsons/get_valid_categories")
	Call<HashMap> getCategories();

	@GET("jsons/get_valid_payment_methods")
	Call<HashMap> getValidPaymentMethods();

	@GET("jsons/get_employees_by_owner_user_id/{user_id}")
	Call<HashMap> getOwnerEmployees(@Path("user_id") String user_id);

	@FormUrlEncoded
	@POST("owner/add_employee/{user_id}")
	Call<HashMap> addEmployee(@Path("user_id") String user_id, @Field("first_name") String first_name, @Field("last_name") String last_name, @Field("email") String email, @Field("department_id") String department_id);

	@FormUrlEncoded
	@POST("owner/update_employee_details/{user_id}")
	Call<HashMap> updateEmployeeDetails(@Path("user_id") String user_id, @Field("employee_id") String employee_id, @Field("first_name") String first_name, @Field("last_name") String last_name, @Field("email") String email, @Field("department_id") String department_id);

	@FormUrlEncoded
	@POST("owner/unemploy_reemploy_employee/{action}")
	Call<HashMap> unemployReemployEmployee(@Path("action") String action, @Field("user_id") String user_id, @Field("employee_id") String employee_id);

	@GET("jsons/get_products_by_owner_user_id/{user_id}")
	Call<HashMap> getOwnerProducts(@Path("user_id") String user_id);

	@FormUrlEncoded
	@POST("owner/add_product")
	Call<HashMap> addProduct(@Field("user_id") String user_id, @Field("product") String product, @Field("category") String category, @Field("cost") String cost);

	@FormUrlEncoded
	@POST("owner/update_product_details")
	Call<HashMap> updateProductDetails(@Field("user_id") String user_id, @Field("product_id") String product_id, @Field("product") String product, @Field("category") String category, @Field("cost") String cost);

	@FormUrlEncoded
	@POST("owner/remove_readd_product/{action}")
	Call<HashMap> disableEnableProduct(@Path("action") String action, @Field("user_id") String user_id, @Field("product_id") String product_id);

	@GET("jsons/get_inventory_for_purchases/{owner_id}")
	Call<HashMap> getProductsForPurchase(@Path("owner_id") String owner_id);

	@GET("jsons/get_inventory_for_sales/{owner_id}")
	Call<HashMap> getProductsForSale(@Path("owner_id") String owner_id);

	@FormUrlEncoded
	@POST("pos/add_purchase")
	Call<HashMap> addPurchase(@FieldMap Map<String, String> data, @Field("user_id") String user_id);

	@FormUrlEncoded
	@POST("pos/add_sale")
	Call<HashMap> addSale(@FieldMap Map<String, String> data, @Field("user_id") String user_id);

	@Multipart
	@POST("settings/update_owner_details")
	Call<HashMap> updateOwnerDetails(@Part("user_id") RequestBody user_id, @Part("first_name") RequestBody fname, @Part("last_name") RequestBody lname, @Part("company") RequestBody company, @Part MultipartBody.Part photo);

	@Multipart
	@POST("settings/update_employee_details")
	Call<HashMap> updateEmployeeDetails(@Part("user_id") RequestBody user_id, @Part("first_name") RequestBody fname, @Part("last_name") RequestBody lname, @Part MultipartBody.Part photo);
}
