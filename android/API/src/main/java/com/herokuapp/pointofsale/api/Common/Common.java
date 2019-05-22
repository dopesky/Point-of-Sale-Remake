package com.herokuapp.pointofsale.api.Common;

import com.google.gson.*;
import java.io.*;
import java.util.*;
import java.net.*;

public class Common {
	private final String apiKey;
	public final static String SERVER_URL = "https://hci-pos.herokuapp.com/server/";

	public Common(String apiKey){
		this.apiKey = apiKey;
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
