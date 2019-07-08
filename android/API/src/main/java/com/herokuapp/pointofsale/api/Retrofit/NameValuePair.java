package com.herokuapp.pointofsale.api.Retrofit;

import java.util.HashMap;

public class NameValuePair {
	private String name;
	private String value;

	public NameValuePair(){}

	public NameValuePair(String name, String value){
		this.name = name;
		this.value = value;
	}

	public HashMap<String, String>  toHashMap(String name){
		HashMap<String, String> map = new HashMap<>();
		map.put(name+"['"+this.name+"']", value);
		return map;
	}

	public String getKey(){
		return name;
	}

	public String getValue(){
		return value;
	}
}
