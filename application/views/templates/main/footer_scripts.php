		</div>
		<script>
			<?php if($this->session->flashdata('info')!==null){?>
				$('#site-info>div.toast>div.toast-body').html("<div><i class='fas fa-exclamation-circle'><i><strong> Info: </strong><?=$this->session->flashdata('info')?></div>").parent().toast('show')
			<?php }?>
		</script>
	</body>
</html>
