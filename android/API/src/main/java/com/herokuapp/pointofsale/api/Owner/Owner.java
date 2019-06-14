package com.herokuapp.pointofsale.api.Owner;

import android.os.AsyncTask;

import com.herokuapp.pointofsale.api.Common.Common;

import java.io.IOException;

public class Owner extends AsyncTask<String, Void, Object> {

	private final String apiKey;

	protected Owner(String apiKey){
		this.apiKey = apiKey;
	}

	private Object getOwnerEmployees(String userID) throws IOException {
		Common common = new Common(this.apiKey);
		return common.executeGetRequest(Common.SERVER_URL + "jsons/get_employees_by_owner_user_id/"+ userID, false);
	}

	@Override
	protected Object doInBackground(String[] data) {
		String action = data[0];
		switch (action) {
			case "fetch employees":
				try {
					return this.getOwnerEmployees(data[1]);
				} catch (IOException ioe) {
					return ioe;
				}
			default:
				return new NullPointerException("Functionality Not Found!");
		}
	}
}
