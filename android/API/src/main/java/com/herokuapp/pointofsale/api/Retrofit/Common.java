package com.herokuapp.pointofsale.api.Common;

import com.google.gson.*;
import java.io.*;
import java.lang.annotation.Annotation;
import java.util.*;
import java.net.*;

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
	public final static String SERVER_URL = "https://hci-pos.herokuapp.com/server/";
	private static Retrofit retrofit;
	private final String apiKey;

	public Common(String apiKey){
		this.apiKey = apiKey;
	}

	public static Retrofit getRetrofitInstance() {
		if (retrofit == null) {
			OkHttpClient.Builder httpClient = new OkHttpClient.Builder();
			httpClient.addInterceptor(new Interceptor() {
				@Override
				@EverythingIsNonNull
				public Response intercept(Chain chain) throws IOException {
					Request request = chain.request().newBuilder().addHeader("APIKEY", Common.APIKEY).build();
					return chain.proceed(request);
				}
			});

			retrofit = new retrofit2.Retrofit.Builder()
					.baseUrl(Common.SERVER_URL)
					.addConverterFactory(GsonConverterFactory.create())
					.client(httpClient.build())
					.build();
		}
		return retrofit;
	}

	public static HashMap getErrorObject(retrofit2.Response<?> response) {
		HashMap errorObject;
		Converter<ResponseBody, HashMap> converter = retrofit.responseBodyConverter(HashMap.class, new Annotation[0]);
		try {
			assert response.errorBody() != null;
			errorObject = converter.convert(response.errorBody());
		} catch (IOException e) {
			return new HashMap<>();
		}

		return errorObject;
	}





	private HttpURLConnection getURLConnection(String url, String requestMethod) throws IOException{
		HttpURLConnection connection =
				(HttpURLConnection) new URL(url).openConnection();

		HttpURLConnection.setFollowRedirects(true);
		connection.setDoInput(true);
		connection.setDoOutput(true);
		connection.setRequestMethod(requestMethod);
		connection.setRequestProperty("Content-Type", "application/x-www-form-urlencoded");
		connection.setRequestProperty("APIKEY", this.apiKey);
		connection.setConnectTimeout(10000);
		connection.setReadTimeout(10000);
		connection.connect();
		return connection;
	}

	private String buildQuery(HashMap<String,String> data) throws IOException{
		if(data.isEmpty()) throw new IOException("Request Cannot be Sent with Empty Body.");
		StringBuilder request = new StringBuilder();
		for (String temp:data.keySet()) {
			request.append(temp).append("=").append(URLEncoder.encode(data.get(temp),"UTF-8")).append("&");
		}
		return request.substring(0, request.length() - 1);
	}

	private HashMap convertJsonToHashMap(String jsonString){
		Gson converter = new Gson();
		System.out.println(jsonString);
		return converter.fromJson(jsonString, HashMap.class);
	}

	private HashMap getResponse(HttpURLConnection connection){
		StringBuilder response = new StringBuilder();
		try{
			InputStream is = connection.getInputStream();
			Scanner in = new Scanner(is);
			if(in.hasNextLine()){
				while(in.hasNextLine()){
					response.append(in.nextLine());
				}
			}
		}catch(IOException ex){
			InputStream is = connection.getErrorStream();
			Scanner in = new Scanner(is);
			if(in.hasNextLine()){
				while(in.hasNextLine()){
					response.append(in.nextLine());
				}
			}
		}
		return this.convertJsonToHashMap(response.toString());
	}

	private HttpURLConnection setBody(HttpURLConnection connection, HashMap<String, String> data) throws IOException{
		String request = this.buildQuery(data);
		OutputStream os = connection.getOutputStream();
		os.write(request.getBytes());
		return connection;
	}

	public Object executeGetRequest(String url, boolean responseCode) throws IOException{
		HttpURLConnection connection = this.getURLConnection(url, "GET");
		HashMap response = this.getResponse(connection);
		return responseCode ? connection.getResponseCode() : response;
	}

	public Object executeGetRequest(String url, HashMap<String,String> data, boolean responseCode) throws IOException{
		if(data.isEmpty()) return this.executeGetRequest(url, responseCode);
		url = url + "?" + this.buildQuery(data);
		HttpURLConnection connection = this.getURLConnection(url, "GET");
		HashMap response = this.getResponse(connection);
		return responseCode ? connection.getResponseCode() : response;
	}

	public Object executePostRequest(String url, HashMap<String,String> data, boolean responseCode) throws IOException{
		HttpURLConnection connection = this.setBody(this.getURLConnection(url, "POST"), data);
		HashMap response = this.getResponse(connection);
		return responseCode ? connection.getResponseCode() : response;
	}
}
