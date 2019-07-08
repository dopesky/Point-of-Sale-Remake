package com.herokuapp.pointofsale.ui.resources;


import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.graphics.drawable.Drawable;
import android.net.Uri;
import android.support.v7.widget.Toolbar;
import android.widget.ImageView;

import com.bumptech.glide.Glide;
import com.herokuapp.pointofsale.R;
import com.herokuapp.pointofsale.ui.owner.ManageEmployees;
import com.herokuapp.pointofsale.ui.owner.ManageProducts;
import com.herokuapp.pointofsale.ui.owner.OwnerDashboard;
import com.herokuapp.pointofsale.ui.pos.Purchases;
import com.mikepenz.fontawesome_typeface_library.FontAwesome;
import com.mikepenz.materialdrawer.AccountHeader;
import com.mikepenz.materialdrawer.DrawerBuilder;
import com.mikepenz.materialdrawer.AccountHeaderBuilder;
import com.mikepenz.materialdrawer.model.DividerDrawerItem;
import com.mikepenz.materialdrawer.model.ProfileDrawerItem;
import com.mikepenz.materialdrawer.model.SecondaryDrawerItem;
import com.mikepenz.materialdrawer.model.interfaces.IDrawerItem;
import com.mikepenz.materialdrawer.util.AbstractDrawerImageLoader;
import com.mikepenz.materialdrawer.util.DrawerImageLoader;

import java.util.ArrayList;

public class NavigationBars {

	public static final String BLANK_PROFILE_IMAGE = "https://res.cloudinary.com/dopesky/image/upload/v1558329489/point_of_sale/site_data/blank-profile-picture-973460_960_720_gcn9y2.png";

	private static SharedPreferences getUserdata(Context context){
		return context.getSharedPreferences(Common.USERDATA, Context.MODE_PRIVATE);
	}

	private static void getImageLoader(Context context){
		DrawerImageLoader.init(new AbstractDrawerImageLoader() {
			@Override
			public void set(ImageView imageView, Uri uri, Drawable placeholder, String tag) {
				Glide.with(context).load(uri).placeholder(placeholder).centerCrop().into(imageView);
			}

			@Override
			public void cancel(ImageView imageView) {
				Glide.with(context).clear(imageView);
			}

			@Override
			public Drawable placeholder(Context ctx, String tag) {
				return super.placeholder(ctx, tag);
			}
		});
	}

	private static AccountHeader getAccountHeader(Activity activity){
		getImageLoader(activity);
		String fullName = Common.capitalize(
				getUserdata(activity).getString("lname", "")
					+ " " +
				getUserdata(activity).getString("fname", "")
		);
		return new AccountHeaderBuilder()
				 .withActivity(activity)
				 .withHeaderBackground(R.drawable._background).withHeaderBackgroundScaleType(ImageView.ScaleType.CENTER_CROP)
				 .addProfiles(
				 		new ProfileDrawerItem()
								.withName(fullName)
								.withEmail(getUserdata(activity).getString("email", "").toLowerCase())
								.withIcon(getUserdata(activity).getString("photo", BLANK_PROFILE_IMAGE))
				 )
				.withOnAccountHeaderListener((view, profile, current) -> {
					CustomToast.showToast(activity, " Hurray Wewe Ni Boss!", "success");
					return false;
				})
				.build();
	}

	private static IDrawerItem[] getDrawerItems(int level, Activity activity){
		ArrayList<IDrawerItem> items = new ArrayList<>();
		switch (level){
			case 4:
				items.add(new SecondaryDrawerItem()
						.withIdentifier(1)
						.withName("Dashboard")
						.withIcon(FontAwesome.Icon.faw_home)
						.withSelectable(false).withOnDrawerItemClickListener((view, position, drawerItem) ->{
							if(activity instanceof OwnerDashboard) return false;
							Intent intent = new Intent(activity, OwnerDashboard.class);
							activity.startActivity(intent);
							return false;
						}));
				items.add(new DividerDrawerItem());
				items.add(new SecondaryDrawerItem()
						.withIdentifier(1)
						.withName("Manage Employees")
						.withIcon(FontAwesome.Icon.faw_users_cog)
						.withSelectable(false)
						.withOnDrawerItemClickListener((view, position, drawerItem) ->{
							if(activity instanceof ManageEmployees) return false;
							Intent intent = new Intent(activity, ManageEmployees.class);
							activity.startActivity(intent);
							return false;
						}));
				items.add(new DividerDrawerItem());
				items.add(new SecondaryDrawerItem()
						.withIdentifier(2)
						.withName("Manage Products")
						.withIcon(FontAwesome.Icon.faw_shopping_cart)
						.withSelectable(false)
						.withOnDrawerItemClickListener((view, position, drawerItem) ->{
							if(activity instanceof ManageProducts) return false;
							Intent intent = new Intent(activity, ManageProducts.class);
							activity.startActivity(intent);
							return false;
						}));
				items.add(new DividerDrawerItem());
			case 3:
			case 2:
			case 1:
				items.add(new SecondaryDrawerItem()
						.withIdentifier(3)
						.withName("Purchases")
						.withIcon(FontAwesome.Icon.faw_shopping_basket)
						.withSelectable(false)
						.withOnDrawerItemClickListener((view, position, drawerItem) ->{
							if(activity instanceof Purchases) return false;
							Intent intent = new Intent(activity, Purchases.class);
							activity.startActivity(intent);
							return false;
						}));
				items.add(new DividerDrawerItem());
				items.add(new SecondaryDrawerItem()
						.withIdentifier(4)
						.withName("Sales")
						.withIcon(FontAwesome.Icon.faw_cart_arrow_down)
						.withSelectable(false));
				items.add(new DividerDrawerItem());
				items.add(new SecondaryDrawerItem()
						.withIdentifier(4)
						.withName("Logout")
						.withIcon(FontAwesome.Icon.faw_sign_out_alt)
						.withSelectable(false)
						.withOnDrawerItemClickListener((view, position, drawerItem) ->{
							Common.logoutUser(activity);
							Common.launchLauncherActivity(activity);
							return false;
						}));
				items.add(new DividerDrawerItem());
				break;
		}
		IDrawerItem[] drawerItems = new IDrawerItem[items.size()];
		items.toArray(drawerItems);
		return drawerItems;
	}

	public static void getNavBar(Activity activity, Toolbar toolbar, String level){
		IDrawerItem[] items = getDrawerItems(Integer.parseInt(level), activity);
		new DrawerBuilder().withActivity(activity).withToolbar(toolbar).withActionBarDrawerToggleAnimated(true)
				.withAccountHeader(getAccountHeader(activity), true)
				.addDrawerItems(items)
				.withSelectedItem(-1)
				.withStickyFooter(R.layout.footer)
				.withFooterClickable(false)
				.build();
	}
}
