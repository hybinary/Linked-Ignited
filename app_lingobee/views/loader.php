<?php if ( $module != "email") $this->load->view('templates/header');?>

<?php if ( file_exists(APPPATH.'views/modules/'.$module.'/'.$view)): ?>
	<?php $this->load->view('modules/'.$module.'/'.$view);?>
<?php endif;?>

<?php $this->load->view('templates/footer');?>