package com.herokuapp.pointofsale.ui.auth;

import androidx.appcompat.app.AppCompatActivity;
import androidx.core.app.ActivityCompat;
import androidx.core.app.ActivityOptionsCompat;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.view.animation.BounceInterpolator;
import android.view.animation.OvershootInterpolator;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.google.android.material.card.MaterialCardView;
import com.herokuapp.pointofsale.R;
import com.herokuapp.pointofsale.resources.Common;
import com.herokuapp.pointofsale.ui.owner.OwnerDashboard;

public class Launcher extends AppCompatActivity {

	private ImageView loadingImage;
	private boolean show;
	private Runnable runnable;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_launcher);
		show = true;
		runnable = this::run;

		loadingImage = findViewById(R.id.loading_image);
		animate();
	}

	private void animate(){
		MaterialCardView cardView = (MaterialCardView) loadingImage.getParent().getParent().getParent();
		LinearLayout header = (LinearLayout) ((LinearLayout) loadingImage.getParent()).getChildAt(0);
		TextView footer = (TextView) ((LinearLayout) loadingImage.getParent()).getChildAt(2);
		cardView.setAlpha(0f);
		cardView.setTranslationY(-200f);
		header.setTranslationX(-500f);
		header.setAlpha(0f);
		footer.setTranslationX(500f);
		footer.setAlpha(0f);
		loadingImage.setAlpha(0f);

		cardView.animate().setStartDelay(300).alpha(1f).setDuration(1000).start();
		cardView.animate().setStartDelay(400).setInterpolator(new BounceInterpolator()).translationY(0f).setDuration(1000).start();

		header.animate().setStartDelay(600).alpha(1f).setDuration(800).start();
		header.animate().setStartDelay(700).translationX(1f).setInterpolator(new OvershootInterpolator()).setDuration(800).start();

		footer.animate().setStartDelay(600).alpha(1f).setDuration(800).start();
		footer.animate().setStartDelay(700).translationX(1f).setInterpolator(new OvershootInterpolator()).setDuration(800).start();

		loadingImage.animate().alpha(1f).setStartDelay(600).setDuration(1000).withEndAction(runnable);
	}

	@Override
	public void onPause(){
		super.onPause();
		show = false;
	}

	@Override
	public void onResume(){
		super.onResume();
		if(!show){
			show = true;
			runnable = this::run;
			new Thread(runnable).start();
		}
	}

	private void goToActivity(Activity context){
		SharedPreferences preferences = context.getSharedPreferences(Common.USERDATA, Context.MODE_PRIVATE);
		Intent intent = null;
		if(!preferences.contains("2FA")){
			intent = new Intent(context, MainActivity.class);
		}else if(preferences.getBoolean("2FA", false)){
			intent = new Intent(context, TwoFactor.class);
		}else if(!preferences.getBoolean("2FA", true) && preferences.contains("level")) {
			intent = new Intent(context, OwnerDashboard.class);
		}

		if(intent == null) return;

		intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK | Intent.FLAG_ACTIVITY_TASK_ON_HOME);
		ActivityOptionsCompat options =
				ActivityOptionsCompat.makeScaleUpAnimation(loadingImage, loadingImage.getWidth()/2, loadingImage.getHeight()/2, 0, 0);
		ActivityCompat.startActivity(context, intent, options.toBundle());
		context.finish();
	}

	private void run() {
		if(!show) return;
		try {
			Thread.sleep(1000);
		} catch (InterruptedException e) {
			e.printStackTrace();
		} finally {
			goToActivity(this);
		}
	}
}
