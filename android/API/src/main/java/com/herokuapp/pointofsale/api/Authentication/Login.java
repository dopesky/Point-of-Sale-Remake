package com.herokuapp.pointofsale.api.Authentication;

import android.os.AsyncTask;

import com.herokuapp.pointofsale.api.Common.Common;

import java.io.IOException;
import java.util.HashMap;

public class Login extends AsyncTask<String, Void, Object> {
	private final String apiKey;

	protected Login(String apiKey){
		this.apiKey = apiKey;
	}

	private Object loginUser(String email, String password) throws IOException {
		Common common = new Common(this.apiKey);
		HashMap<String,String> map = new HashMap<>();
		map.put("email", email);
		map.put("password", password);
		return common.executePostRequest(Common.SERVER_URL + "auth/login" , map, false);
	}

	@Override
	protected Object doInBackground(String[] data) {
		try {
			return this.loginUser(data[0], data[1]);
		} catch (IOException ioe) {
			return ioe;
		}
	}
}
