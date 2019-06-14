package com.herokuapp.pointofsale.Owner;

import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;

import com.herokuapp.pointofsale.R;
import com.herokuapp.pointofsale.Resources.OwnerEmployeesAdapter;
import com.herokuapp.pointofsale.Resources.Common;
import com.herokuapp.pointofsale.Resources.CustomToast;
import com.herokuapp.pointofsale.api.Owner.Owner;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Objects;
import com.google.gson.internal.LinkedTreeMap;

public class OwnerDashboard extends AppCompatActivity {
	private RecyclerView showEmployees;
	private OwnerEmployeesAdapter adapter;
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_owner_dashboard);
		getSharedPreferences(Common.USERDATA, MODE_PRIVATE).edit().clear().apply();
		showEmployees = (RecyclerView) findViewById(R.id.recyclerview);
		Owner employees = OwnerDashboard.getEmployees(this);
		employees.execute("fetch employees", "1");
	}

	private static Owner getEmployees(OwnerDashboard context){
		return new Owner(context.getString(R.string.API_KEY)){
			@Override
			protected void onPostExecute(Object response){
				try {
					HashMap map = (HashMap) response;
					if ((double) map.get("status") == (double) 202 || (double) map.get("status") == (double) 200) {
						ArrayList<LinkedTreeMap<String,String>> data = (ArrayList<LinkedTreeMap<String,String>>)map.get("response");
						context.adapter = new OwnerEmployeesAdapter(context, data);
						context.showEmployees.setAdapter(context.adapter);
						context.showEmployees.setLayoutManager(new LinearLayoutManager(context));
					} else {
						CustomToast.showToast(context, " " + Common.parseHtml(Objects.requireNonNull(map.get("errors"))), "danger");
					}
				} catch (ClassCastException cce) {
					//IOException ioe = (IOException) response;
					CustomToast.showToast(context, " " + cce.getMessage(), "danger");
				}
			}
		};

	}
}
