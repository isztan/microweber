<?   



$orig_params = $params;
if(isset($params['id'])){
unset($params['id']);	
}
$opts = false;
if(isset($orig_params['for_module_id'])){
	
	
	$chck =   get_options('module=' . $orig_params['for_module_id']);
	if (isset($chck[0]) and isset($chck[0]['id'])) {
	$opts = $chck;
	 
	}

}
if($opts == false and isset($params['for_module'])){
$params['module'] = $params['for_module'];	
$opts = get_options($params);

}

?>
 
<? if(is_arr($opts)): ?>
<? foreach($opts as $params): ?>
<? include( $config['path_to_module'].'edit.php'); ?>
<? endforeach; ?>
<?  else: ?>
<? _e("No options found"); ?>
<? endif; ?>