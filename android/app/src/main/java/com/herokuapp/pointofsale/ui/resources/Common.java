package com.herokuapp.pointofsale.Resources;

import android.support.v4.text.HtmlCompat;

import java.util.ArrayList;
import java.util.Arrays;

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
}
