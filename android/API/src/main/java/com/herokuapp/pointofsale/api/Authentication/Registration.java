package com.herokuapp.pointofsale.api.Authentication;

import android.os.AsyncTask;

import com.herokuapp.pointofsale.api.Common.Common;

import java.io.IOException;
import java.util.HashMap;

public class Registration extends AsyncTask<String, Void, Object> {
	private final String apiKey;

	protected Registration(String apiKey){
		this.apiKey = apiKey;
	}

	private Object registerUser(String userType, String email) throws IOException {
		Common common = new Common(this.apiKey);
		HashMap<String,String> map = new HashMap<>();
		map.put("email", email);
		return common.executePostRequest(Common.SERVER_URL + "auth/register/" + userType , map, false);
	}

	private Object requestPasswordReset(String email) throws IOException{
		Common common = new Common(this.apiKey);
		HashMap<String,String> map = new HashMap<>();
		map.put("email", email);
		return common.executePostRequest(Common.SERVER_URL + "auth/request_reset", map, false);
	}
	@Override
	protected Object doInBackground(String[] objects) {
		String action = objects[0];
		String email = objects[1];
		switch (action){
			case("register"):
				try {
					return this.registerUser(email, objects[2]);
				} catch (IOException ioe) {
					return ioe;
				}
			case("reset"):
				try {
					return this.requestPasswordReset(email);
				} catch (IOException ioe) {
					return ioe;
				}
			default:
				return new NullPointerException("Functionality Not Found!");
		}
	}
}
