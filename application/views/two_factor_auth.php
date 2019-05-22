<div class="text-center mt-5" ng-app='main' ng-cloak ng-controller="twoFactorAuth">
	<h3 class="header-text">2-Step Auth <i class="fas fa-lock-open"></i></h3>
	<div class="row mt-3">
		<div class="col-12 col-md-2 col-lg-3"></div>
		<div class="col-12 col-md-8 col-lg-6 p-0">
			<div class="ml-3 mr-3 text-muted">
				<ul class="list-group text-left" id="list-group">
					<li class="list-group-item bg-translucent">
						<span class="d-flex flex-wrap justify-content-between align-items-center link" data-target="#google" data-toggle="collapse">
							<span><i class="fab fa-google text-info"></i> Google Authentication</span>
							<small class="text-info ml-4">Recommended</small>
						</span>
						<div id="google" class="collapse show" data-parent="#list-group">
							<div class="p-2 mt-1 mb-1">
								<?=$this->load->view('google_authenticator',NULL,true)?>
							</div>
						</div>
					</li>
					<li class="list-group-item bg-translucent">
						<span class="d-flex flex-wrap link" data-target="#email" data-toggle="collapse"><span><i class="fas fa-at text-info"></i> Email Authentication</span></span>
						<div id="email" class="collapse" data-parent="#list-group">
							<div class="p-2 mt-1 mb-1">
								<?=$this->load->view('email_authenticator',NULL,true)?>
							</div>
						</div>
					</li>
				</ul>
			</div>
		</div>
		<div class="col-12 col-md-2 col-lg-3"></div>
	</div>
	<div class="modal fade text-muted text-left" id="show-QR-code" data-backdrop="static">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h3>Google Authenticator QR Code</h3>
					<button class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<div class="timeline-seperator mb-3"><span>INFO</span></div>
					<div class="font-sm mb-3"><i class="fas fa-info-circle"></i> We have realized that you never copied the QR Code from us to your phone. Here is the QR Code and its image. Open Google Authenticator and add us to enable Google 2FA.</div>
					<div class="row">
						<div class="col-12">
							<div class="d-flex flex-wrap justify-content-center">
								<img ng-src="{{qrCodeUrl}}">
							</div>
							<div class="d-flex flex-wrap justify-content-center">
								<span><b>QR Code:</b> {{qrCode}}</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="<?=base_url('assets/js/two-factor.js')?>"></script>
<script>
	$('body').addClass('with-background')
</script>