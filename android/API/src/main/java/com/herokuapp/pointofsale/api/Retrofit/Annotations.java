package com.herokuapp.pointofsale.api.Common;

import java.util.HashMap;

import retrofit2.Call;
import retrofit2.http.Field;
import retrofit2.http.FormUrlEncoded;
import retrofit2.http.POST;
import retrofit2.http.Path;

public interface Annotations {
	@FormUrlEncoded
	@POST("auth/register/{type}")
	Call<HashMap> registerUser(@Path("type") String userType, @Field("email") String email, @Field("country") String country);

	@FormUrlEncoded
	@POST("auth/login")
	Call<HashMap> loginUser(@Field("password") String password, @Field("email") String email);

	@FormUrlEncoded
	@POST("auth/request_reset")
	Call<HashMap> requestPasswordReset(@Field("email") String email);
}
