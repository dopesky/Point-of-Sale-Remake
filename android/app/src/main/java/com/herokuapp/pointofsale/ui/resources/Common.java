package com.herokuapp.pointofsale.ui.resources;

import android.Manifest;
import android.animation.ObjectAnimator;
import android.app.Activity;
import android.arch.lifecycle.MutableLiveData;
import android.content.Context;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.graphics.Color;
import android.location.Address;
import android.location.Geocoder;
import android.location.Location;
import android.support.annotation.ColorInt;
import android.support.annotation.NonNull;
import android.support.v4.app.ActivityCompat;
import android.support.v4.text.HtmlCompat;
import android.support.v7.app.ActionBar;
import android.support.v7.app.AlertDialog;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.Toolbar;
import android.view.View;
import android.view.animation.Animation;
import android.view.animation.AnimationUtils;
import android.view.animation.LinearInterpolator;

import com.google.android.gms.location.FusedLocationProviderClient;
import com.google.android.gms.location.LocationServices;
import com.google.gson.internal.LinkedTreeMap;
import com.herokuapp.pointofsale.R;
import com.herokuapp.pointofsale.ui.auth.MainActivity;
import com.herokuapp.pointofsale.ui.owner.ManageEmployees;

import java.io.IOException;
import java.net.URLEncoder;
import java.text.NumberFormat;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Currency;
import java.util.HashMap;
import java.util.List;
import java.util.Locale;
import java.util.Random;

public class Common {
	public static final String USERDATA = "userdata" ;

	public static boolean checkEmail(String email){
		int findAt = email.indexOf("@");
		int findDot = email.lastIndexOf(".");
		return (findAt != -1 && findDot != -1 && (findAt + 2) < findDot && (findDot + 2) < email.length());
	}

	public static String parseHtml(Object obj){
		String request = obj.toString();
		String trimmed = (request.contains("<br>")) ? request.substring(request.indexOf("<span>")) : request;
		ArrayList<String> split = new ArrayList<>(Arrays.asList(trimmed.split("<br>")));
		split.removeAll(Arrays.asList("",null));
		split.trimToSize();
		StringBuilder builder = new StringBuilder();
		for(String temp : split){
			builder.append(temp).append("<br>");
		}
		trimmed = builder.substring(0, builder.length()-"<br>".length());
		return HtmlCompat.fromHtml(trimmed, HtmlCompat.FROM_HTML_MODE_LEGACY).toString();
	}

	public static String capitalize(String lowercase){
		StringBuilder builder = new StringBuilder();
		String[] split = lowercase.toLowerCase().split(" ");
		for(String temp:split){
			builder.append(String.valueOf(temp.charAt(0)).toUpperCase()).append(temp.substring(1)).append(" ");
		}
		return builder.substring(0, builder.length() - 1);
	}

	public static void shakeElement(Context context, View view){
		Animation shakeAnimation = AnimationUtils.loadAnimation(context, R.anim.shake);
		view.startAnimation(shakeAnimation);
	}

	public static void rotateElement(View view, float from, float to, int time){
		ObjectAnimator animator = ObjectAnimator.ofFloat(view, "rotation", from, to);
		animator.setDuration(time);
		animator.setInterpolator(new LinearInterpolator());
		animator.start();
	}

	public static void getUserCurrentLocation(Activity context, MutableLiveData<Location> location){
		if (ActivityCompat.checkSelfPermission(context, Manifest.permission.ACCESS_FINE_LOCATION) != PackageManager.PERMISSION_GRANTED) {
			ActivityCompat.requestPermissions(context, new String[]{Manifest.permission.ACCESS_FINE_LOCATION},	1);
		} else {
			FusedLocationProviderClient flpClient = LocationServices.getFusedLocationProviderClient(context);
			flpClient.getLastLocation().addOnSuccessListener(location1 -> {
				if(location1 != null) {
					location.setValue(location1);
				}
			});
		}
	}

	public static String getCountryFromLocation(Context context, Location location){
		if(location == null) return "kenya";
		Geocoder geocoder = new Geocoder(context, Locale.getDefault());
		try{
			List<Address> addresses = geocoder.getFromLocation(location.getLatitude(), location.getLongitude(), 1);
			return capitalize(addresses.get(0).getCountryName());
		}catch(IOException ioe){
			return "kenya";
		}
	}

	public static void setCustomActionBar(AppCompatActivity activity, Toolbar toolbar){
		activity.setSupportActionBar(toolbar);
	}

	public static void launchLauncherActivity(Activity context) {
		Intent intent = new Intent(context, MainActivity.class);
		intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK | Intent.FLAG_ACTIVITY_TASK_ON_HOME);
		context.startActivity(intent);
		context.overridePendingTransition(0,0);
	}

	public static void logoutUser(Context context) {
		context.getSharedPreferences(USERDATA, Context.MODE_PRIVATE).edit().clear().apply();
	}

	public static String getRandomDarkColor(int adapterPosition){
		String[] randomColors = {"#2196f3","#009688","#f44336","#4caf50","#e91e63","#9c27b0","#3f51b5","#ff9800","#795548","#ffc107"};
		return randomColors[adapterPosition % randomColors.length];
	}

	public static String formatCurrency(String currencyCode, String currencyString){
		Currency currency;
		try {
			currency = Currency.getInstance(currencyCode);
		}catch (NullPointerException|IllegalArgumentException ex){
			currency = Currency.getInstance(Locale.getDefault());
		}
		NumberFormat formatter = NumberFormat.getCurrencyInstance();
		formatter.setCurrency(currency);
		return formatter.format(Integer.valueOf(currencyString));
	}

	public static String formatNumber(String number){
		NumberFormat formatter = NumberFormat.getNumberInstance();
		return formatter.format(Float.parseFloat(number));
	}

	public static LinkedTreeMap<String, String> makePosRequestBody(ArrayList<LinkedTreeMap<String, String>> list){
		LinkedTreeMap<String, String> body = new LinkedTreeMap<>();
		for (int i = 0; i < list.size(); i++) {
			body.put("data["+i+"][product_id]", list.get(i).get("product_id"));
			body.put("data["+i+"][quantity]", list.get(i).get("amount"));
			body.put("data["+i+"][total_cost]", list.get(i).get("cost"));
			body.put("data["+i+"][discount]", list.get(i).get("discount"));
		}
		return body;
	}
}
