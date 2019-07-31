package com.herokuapp.pointofsale.resources;

import android.content.Context;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.TextView;
import android.widget.Toast;

import com.herokuapp.pointofsale.R;

import java.util.HashMap;
import java.util.Objects;


public class CustomToast {
	private static final HashMap<String,Integer[]> toastReference = new HashMap<String,Integer[]>(){
		{
			put("info", new Integer[]{R.drawable.toast_info_background, R.drawable.ic_info_black_24dp});
			put("warning", new Integer[]{R.drawable.toast_warning_background, R.drawable.ic_help_black_24dp});
			put("danger", new Integer[]{R.drawable.toast_danger_background, R.drawable.ic_cancel_black_24dp});
			put("success", new Integer[]{R.drawable.toast_success_background, R.drawable.ic_check_circle_black_24dp});
		}
	};
	public static void showToast(Context context, String message, String type){
		View toastView = LayoutInflater.from(context).inflate(R.layout.custom_toast_view, null);
		toastView.setBackground(context.getDrawable(Objects.requireNonNull(toastReference.get(type))[0]));
		TextView tv = toastView.findViewById(R.id.customToastText);
		tv.setText(message);
		tv.setCompoundDrawablesRelativeWithIntrinsicBounds(Objects.requireNonNull(toastReference.get(type))[1],0,0,0);

		// Initiate the Toast instance.
		Toast toast = new Toast(context.getApplicationContext());
		// Set custom view in toast.
		toast.setView(toastView);
		toast.setDuration(Toast.LENGTH_LONG);
		toast.setGravity(Gravity.TOP, 0,250);
		toast.show();
	}
}
