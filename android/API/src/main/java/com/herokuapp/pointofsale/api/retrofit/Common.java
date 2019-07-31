package com.herokuapp.pointofsale.api.retrofit;

import java.io.*;
import java.lang.annotation.Annotation;
import java.util.*;

import okhttp3.Interceptor;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.Response;
import okhttp3.ResponseBody;
import okhttp3.internal.annotations.EverythingIsNonNull;
import retrofit2.Converter;
import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class Common {
	private static final String APIKEY = "oE7uXOM0R60Be0OdWZ6ncITdQaNZ5uR9EmlcUEtokBzKvEWy8ZvfzV3RpCwI";
	private final static String SERVER_URL = "https://hci-pos.herokuapp.com/server/";
	private Retrofit retrofit;
	private static Common commonInstance;

	public static synchronized Retrofit getRetrofitInstance() {
		if(commonInstance == null){
			commonInstance = new Common();
			commonInstance.retrofit = new retrofit2.Retrofit.Builder()
					.baseUrl(Common.SERVER_URL)
					.addConverterFactory(GsonConverterFactory.create())
					.client(commonInstance.getClient())
					.build();
		}
		return commonInstance.retrofit;
	}

	private OkHttpClient getClient(){
		OkHttpClient.Builder httpClient = new OkHttpClient.Builder();
		httpClient.addInterceptor(new Interceptor() {
			@Override
			@EverythingIsNonNull
			public Response intercept(Chain chain) throws IOException {
				Request request = chain.request().newBuilder().addHeader("APIKEY", Common.APIKEY).build();
				return chain.proceed(request);
			}
		});
		httpClient.followRedirects(true);
		return httpClient.build();
	}

	public static HashMap getErrorObject(retrofit2.Response<?> response) {
		HashMap errorObject;
		Converter<ResponseBody, HashMap> converter = commonInstance.retrofit.responseBodyConverter(HashMap.class, new Annotation[0]);
		try {
			assert response.errorBody() != null;
			errorObject = converter.convert(response.errorBody());
		} catch (IOException e) {
			return new HashMap<>();
		}

		return errorObject;
	}
}
