package com.herokuapp.pointofsale.ui.settings.fragments;

import android.app.Activity;
import android.content.ContentResolver;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.net.Uri;
import android.os.Bundle;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.constraintlayout.widget.ConstraintLayout;
import androidx.databinding.DataBindingUtil;
import androidx.fragment.app.Fragment;
import androidx.lifecycle.Observer;
import androidx.lifecycle.ViewModelProviders;

import android.provider.MediaStore;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ProgressBar;

import com.bumptech.glide.Glide;
import com.google.android.material.button.MaterialButton;
import com.google.android.material.card.MaterialCardView;
import com.google.gson.internal.LinkedTreeMap;
import com.herokuapp.pointofsale.R;
import com.herokuapp.pointofsale.databinding.FragmentMyProfileBinding;
import com.herokuapp.pointofsale.resources.Common;
import com.herokuapp.pointofsale.resources.CustomToast;
import com.herokuapp.pointofsale.viewmodels.settings.Settings;
import com.mikepenz.fontawesome_typeface_library.FontAwesome;
import com.mikepenz.iconics.IconicsDrawable;

import java.io.File;
import java.util.Objects;

public class MyProfile extends Fragment {
	private Settings settingsVM;
	private FragmentMyProfileBinding binding;
	private boolean isSaving;
	private String saveFilePath;

	private Observer<LinkedTreeMap> userDataObserver = userdata -> {
		if(userdata != null && binding != null && binding.getSettingsDB() != null){
			Glide.with(this).load(binding.getSettingsDB().getImageSRC()).placeholder(R.drawable.image_pre_loader).into(binding.profileImage);
			ProgressBar bar = Objects.requireNonNull(getView()).findViewById(R.id.progress_bar);
			MaterialCardView card = (MaterialCardView) ((ConstraintLayout) bar.getParent()).getChildAt(1);
			bar.setVisibility(View.GONE);
			card.setVisibility(View.VISIBLE);
		}
	};

	private Observer<Integer> updateStatusObserver = status -> {
		if(status != null && status == 0){
			CustomToast.showToast(getActivity(), "Update Made Successfully!", "success");
			settingsVM.resetErrors();
		}
		isSaving = false;
		binding.captureButton.setIcon(new IconicsDrawable(Objects.requireNonNull(getActivity()))
				.icon(FontAwesome.Icon.faw_camera).actionBar());
		binding.captureButton.setText(R.string.capture);
		binding.captureButton.setAlpha(1.0f);
		binding.detailsSaveButton.setText(R.string.save);
		binding.detailsSaveButton.setAlpha(1.0f);
		Glide.with(this).load(binding.getSettingsDB().getImageSRC()).placeholder(R.drawable.image_pre_loader).into(binding.profileImage);
	};

	private Observer<String> updateErrorObserver = error -> {
		if(error != null && error.length() > 1){
			CustomToast.showToast(getActivity(), " " + error, "danger");
			settingsVM.resetErrors();
		}
	};

	public static MyProfile newInstance() {
		return new MyProfile();
	}

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		isSaving = false;
	}

	@Override
	public View onCreateView(@NonNull LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
		binding = DataBindingUtil.inflate(inflater, R.layout.fragment_my_profile, container, false);
		binding.profileImage.setOnClickListener(this::uploadImage);
		binding.captureButton.setIcon(
				new IconicsDrawable(Objects.requireNonNull(getActivity())).icon(FontAwesome.Icon.faw_camera).actionBar());
		if(!getActivity().getPackageManager().hasSystemFeature(PackageManager.FEATURE_CAMERA_ANY)){
			binding.captureButton.setVisibility(View.GONE);
		}
		binding.captureButton.setOnClickListener(this::takePhoto);
		binding.detailsSaveButton.setOnClickListener(this::updateUserDetails);
		return binding.getRoot();
	}

	@Override
	public void onActivityCreated(@Nullable Bundle savedInstance){
		super.onActivityCreated(savedInstance);

		settingsVM = ViewModelProviders.of(this).get(Settings.class);
		binding.setLifecycleOwner(getViewLifecycleOwner());
		binding.setSettingsDB(settingsVM.new DataBinder());
		settingsVM.getCurrentUserData().observe(getViewLifecycleOwner(), userDataObserver);
		settingsVM.getUpdateStatus().observe(getViewLifecycleOwner(), updateStatusObserver);
		settingsVM.getUpdateError().observe(getViewLifecycleOwner(), updateErrorObserver);
	}

	@Override
	public void onActivityResult(int requestCode, int resultCode, Intent data){
		super.onActivityResult(requestCode, resultCode, data);
		if(requestCode == 1 && resultCode == Activity.RESULT_OK){
			if(data == null){
				prepareUIForUpload();
				Uri imageUri = Common.getUriFromFile(getActivity(), new File(saveFilePath), ContentResolver.SCHEME_CONTENT);
				settingsVM.uploadPhoto(Common.getImageUploadObject(Objects.requireNonNull(getActivity()), imageUri));
				return;
			}

			Uri uploadedPhoto = data.getData();

			if(uploadedPhoto != null){
				prepareUIForUpload();
				settingsVM.uploadPhoto(Common.getImageUploadObject(Objects.requireNonNull(getActivity()), uploadedPhoto));
			}
		}
	}

	private void prepareUIForUpload(){
		isSaving = true;
		binding.captureButton.setIcon(new IconicsDrawable(Objects.requireNonNull(getActivity()))
				.icon(FontAwesome.Icon.faw_cloud_upload_alt).actionBar());
		binding.captureButton.setText(R.string.uploading);
		binding.captureButton.setAlpha(0.6f);
		binding.profileImage.setImageResource(R.drawable.image_pre_loader);
	}

	private void uploadImage(View view){
		if(isSaving) return;
		Intent intent = new Intent(Intent.ACTION_GET_CONTENT);
		intent.addCategory(Intent.CATEGORY_OPENABLE);
		intent.setType("image/*");
		if(getActivity() != null && intent.resolveActivity(getActivity().getPackageManager()) != null){
			startActivityForResult(intent, 1);
		}
	}

	private void takePhoto(View view){
		if(isSaving || binding.captureButton.getVisibility() == View.GONE) return;
		Intent intent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
		intent.setFlags(Intent.FLAG_GRANT_WRITE_URI_PERMISSION);
		File saveFile = Common.generateTempFile(Objects.requireNonNull(getActivity()), ".jpg");
		if (saveFile != null && getActivity() != null && intent.resolveActivity(getActivity().getPackageManager()) != null) {
			saveFilePath = saveFile.getAbsolutePath();
			intent.putExtra(MediaStore.EXTRA_OUTPUT, Common.getUriFromFile(getActivity(), saveFile, ContentResolver.SCHEME_CONTENT));
			startActivityForResult(intent, 1);
		}
	}

	private void updateUserDetails(View view){
		if(isSaving) return;
		boolean errors = this.validateData();
		if(!errors){
			Common.shakeElement(getActivity(), view);
			return;
		}
		isSaving = true;
		MaterialButton button = (MaterialButton) view;
		button.setAlpha((float)0.6);
		button.setText(R.string.saving);
		settingsVM.updateUserDetails();
	}

	private boolean validateData(){
		Settings.DataBinder binder = binding.getSettingsDB();
		if(binder == null) return false;
		binding.firstNameEditText.setError(null);
		binding.lastNameEditText.setError(null);
		binding.companyNameEditText.setError(null);
		if(binder.getFirstName().trim().isEmpty()){
			binding.firstNameEditText.setError("This is a Required Field!");
			return false;
		}
		if(binder.getLastName().trim().isEmpty()){
			binding.lastNameEditText.setError("This is a Required Field!");
			return false;
		}
		if(binder.isOwner() && binder.getCompanyName().trim().isEmpty()){
			binding.companyNameEditText.setError("This is a Required Field!");
			return false;
		}
		if(!binder.getFirstName().toLowerCase().trim().matches("^[a-z '-]+$")){
			binding.firstNameEditText.setError("This Field Contains Invalid Characters!");
			return false;
		}
		if(!binder.getLastName().toLowerCase().trim().matches("^[a-z '-]+$")){
			binding.lastNameEditText.setError("This Field Contains Invalid Characters!");
			return false;
		}
		if(binder.isOwner() && !binder.getCompanyName().toLowerCase().trim().matches("^[a-z '-]+$")){
			binding.companyNameEditText.setError("This Field Contains Invalid Characters!");
			return false;
		}
		return true;
	}
}
