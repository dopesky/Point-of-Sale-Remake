		<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/navbar-css/normalize.css')?>" />
		<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/navbar-css/demo.css')?>" />
		<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/navbar-css/component.css')?>" />
		<script src="<?=base_url('assets/js/navbar-js/modernizr.custom.js')?>"></script>
		<div class="container">
			<input type="hidden">
			<ul id="gn-menu" class="gn-menu-main">
				<li class="gn-trigger">
					<a class="gn-icon gn-icon-menu" title="Menu"><span>Menu</span></a>
					<nav class="gn-menu-wrapper">
						<div class="gn-scroller">
							<ul class="gn-menu">
								<li class="gn-search-item">
									<input placeholder="Search" type="search" class="gn-search">
									<a class="gn-icon gn-icon-search"><span>Search</span></a>
								</li>
								<li><a href="<?=site_url('owner')?>" title="Dashboard" class="my-icon gn-icon-home">Dashboard</a></li>
								<li><a href="<?=site_url('owner/manage_employees')?>" title="Manage Employees" class="my-icon gn-icon-user-add">Manage Employees</a></li>
								<li><a class="gn-icon gn-icon-cog" title="Settings">Settings</a></li>
							</ul>
						</div><!-- /gn-scroller -->
					</nav>
				</li>
				<li><a href="<?=base_url()?>" title="Home"><img src="<?=base_url('assets/img/logo.png')?>" width="60" height="45"></a></li>
				<li><a href="<?=base_url('auth/log_out')?>" class="my-icon-menu gn-icon-logout font-lg pl-3 pr-3" title="Sign Out"><span>Logout</span></a></li>
			</ul>
		</div>
		<script src="<?=base_url('assets/js/navbar-js/classie.js')?>"></script>
		<script src="<?=base_url('assets/js/navbar-js/gnmenu.js')?>"></script>
		<script>
			new gnMenu( document.getElementById( 'gn-menu' ) );
			$('body').addClass('with-background')
		</script>