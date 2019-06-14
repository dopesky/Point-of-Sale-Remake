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
	Call<HashMap> registerUser(@Path("type") String userType, @Field("email") String email);
}
