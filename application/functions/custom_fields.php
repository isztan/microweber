<?

/*document_ready('test_document_ready_api');

 function test_document_ready_api($layout) {

 //   $layout = modify_html($layout, $selector = '.editor_wrapper', 'append', 'ivan');
 //$layout = modify_html2($layout, $selector = '<div class="editor_wrapper">', '');
 return $layout;
 }*/

/**
 * make_custom_field
 *
 * @desc make_custom_field
 * @access      public
 * @category    forms
 * @author      Microweber
 * @link        http://microweber.com
 * @param string $field_type
 * @param string $field_id
 * @param array $settings
 */
api_expose('make_custom_field');

function make_custom_field($field_id = 0, $field_type = 'text', $settings = false) {
	$data = false;
	$form_data = array();
	if (is_array($field_id)) {

		if (!empty($field_id)) {
			$data = $field_id;
			return make_field($field_id, false, 'y');
		}
	} else {
		if ($field_id != 0) {

			return make_field($field_id);

			//
			// error('no permission to get data');
			//  $form_data = db_get_id('table_custom_fields', $id = $field_id, $is_this_field = false);
		}
	}
	//return make_field($field_id);
	/*
	 if (isset($data) and is_array($data)) {
	 if (!empty($data)) {
	 if (isset($data['custom_field_type'])) {
	 $field_type = $data['custom_field_type'];
	 }
	 if (isset($data['type'])) {
	 $field_type = $data['type'];
	 }
	 }
	 }

	 if (isset($data['field_id'])) {
	 $copy_from = $data['field_id'];
	 if (is_admin() == true) {

	 $table_custom_field = MW_TABLE_PREFIX . 'custom_fields';
	 $form_data = db_get_id($table_custom_field, $id = $copy_from, $is_this_field = false);
	 if (isset($form_data['custom_field_type'])) {
	 $field_type = $data['type'] = $form_data['custom_field_type'];
	 }
	 //d($field_type);
	 }
	 // d($form_data);
	 }

	 if (isset($data['copy_from'])) {
	 $copy_from = $data['copy_from'];
	 if (is_admin() == true) {

	 $table_custom_field = MW_TABLE_PREFIX . 'custom_fields';
	 $form_data = db_get_id($table_custom_field, $id = $copy_from, $is_this_field = false);
	 $field_type = $form_data['custom_field_type'];
	 $form_data['id'] = 0;
	 }
	 //d($form_data);
	 }
	 $settings = false;
	 if (isset($data['settings'])) {
	 $settings = $data['settings'];
	 }
	 $dir = INCLUDES_DIR;
	 $dir = $dir . DS . 'custom_fields' . DS;
	 $field_type = str_replace('..', '', $field_type);
	 // d($field_type);
	 if ($settings == true) {
	 $file = $dir . $field_type . '_settings.php';
	 } else {
	 $file = $dir . $field_type . '.php';
	 }

	 return make_field($form_data, false, $settings);

	 define_constants();
	 $l = new MwView($file);

	 $l -> params = $data;
	 $l -> data = $form_data;
	 // var_dump($l);
	 //$l->set($l);

	 $l = $l -> __toString();
	 // var_dump($l);
	 $l = parse_micrwober_tags($l, $options = array('parse_only_vars' => 1));

	 return $l;*/

}

api_expose('save_custom_field');

function save_custom_field($data) {
	$id = user_id();
	if ($id == 0) {
		error('Error: not logged in.');
	}
	$id = is_admin();
	if ($id == false) {
		error('Error: not logged in as admin.');
	}
	$data_to_save = ($data);

	$table_custom_field = MW_TABLE_PREFIX . 'custom_fields';

	if (isset($data_to_save['for'])) {
		$data_to_save['to_table'] = guess_table_name($data_to_save['for']);
	}
	if (isset($data_to_save['cf_id'])) {
		$data_to_save['id'] = intval($data_to_save['cf_id']);

		$table_custom_field = MW_TABLE_PREFIX . 'custom_fields';
		$form_data_from_id = db_get_id($table_custom_field, $data_to_save['id'], $is_this_field = false);
		if (isset($form_data_from_id['id'])) {
			if (!isset($data_to_save['to_table'])) {
				$data_to_save['to_table'] = $form_data_from_id['to_table'];
			}
			if (!isset($data_to_save['to_table_id'])) {
				$data_to_save['to_table_id'] = $form_data_from_id['to_table_id'];
			}
		}

		if (isset($data_to_save['copy_to_table_id'])) {

			$cp = db_copy_by_id($table_custom_field, $data_to_save['cf_id']);
			$data_to_save['id'] = $cp;
			$data_to_save['to_table_id'] = $data_to_save['copy_to_table_id'];
			//$data_to_save['id'] = intval($data_to_save['cf_id']);
		}

	}

	if (!isset($data_to_save['to_table'])) {
		$data_to_save['to_table'] = 'table_content';
	}
	$data_to_save['to_table'] = db_get_assoc_table_name($data_to_save['to_table']);
	if (!isset($data_to_save['to_table_id'])) {
		$data_to_save['to_table_id'] = '0';
	}
	if (isset($data['options'])) {
		$data_to_save['options'] = encode_var($data['options']);
	}
	//  $data_to_save['debug'] = 1;

	$save = save_data($table_custom_field, $data_to_save);

	cache_clean_group('custom_fields');
	//	$save = make_field($save);
	return $save;

	//exit
}

api_expose('reorder_custom_fields');

function reorder_custom_fields($data) {

	$adm = is_admin();
	if ($adm == false) {
		error('Error: not logged in as admin.');
	}

	$table = MW_TABLE_PREFIX . 'custom_fields';

	foreach ($data as $value) {
		if (is_arr($value)) {
			$indx = array();
			$i = 0;
			foreach ($value as $value2) {
				$indx[$i] = $value2;
				$i++;
			}

			db_update_position($table, $indx);
			return true;
			// d($indx);
		}
	}
}

api_expose('remove_field');

function remove_field($id) {
	$uid = user_id();
	if ($uid == 0) {
		error('Error: not logged in.');
	}
	$uid = is_admin();
	if ($uid == false) {
		exit('Error: not logged in as admin.');
	}
	if (is_array($id)) {
		extract($id);
	} else {

	}

	$id = intval($id);
	if (isset($cf_id)) {
		$id = intval($cf_id);
	}

	if ($id == 0) {

		return false;
	}

	$custom_field_table = MW_TABLE_PREFIX . 'custom_fields';
	$q = "DELETE FROM $custom_field_table where id='$id'";

	db_q($q);

	cache_clean_group('custom_fields');

	return true;
}

/**
 * make_field
 *
 * @desc make_field
 * @access      public
 * @category    forms
 * @author      Microweber
 * @link        http://microweber.com
 * @param string $field_type
 * @param string $field_id
 * @param array $settings
 */
function make_field($field_id = 0, $field_type = 'text', $settings = false) {
			 
	if (is_array($field_id)) {
		if (!empty($field_id)) {
			$data = $field_id;
		}
	} else {
		if ($field_id != 0) {
			$data = db_get_id('table_custom_fields', $id = $field_id, $is_this_field = false);
		}
	}
	if (isset($data['settings']) or (isset($_REQUEST['settings']) and trim($_REQUEST['settings']) == 'y')) {

		$settings == true;
	}

	if (isset($data['copy_from'])) {
		$copy_from = intval($data['copy_from']);
		if (is_admin() == true) {

			$table_custom_field = MW_TABLE_PREFIX . 'custom_fields';
			$form_data = db_get_id($table_custom_field, $id = $copy_from, $is_this_field = false);
			if (is_arr($form_data)) {

				$field_type = $form_data['custom_field_type'];
				$data['id'] = 0;
				if (isset($data['save_on_copy'])) {

					$cp = $form_data;
					$cp['id'] = 0;
					$cp['copy_of_field'] = $copy_from;
					if (isset($data['to_table'])) {
						$cp['to_table'] = ($data['to_table']);
					}
					if (isset($data['to_table_id'])) {
						$cp['to_table_id'] = ($data['to_table_id']);
					}
					save_custom_field($cp);
					$data = $cp;
				} else {
					$data = $form_data;
				}

			}

		}
		//d($form_data);
	} else if (isset($data['field_id'])) {

		$data = db_get_id('table_custom_fields', $id = $data['field_id'], $is_this_field = false);
	}

	if (isset($data['custom_field_type'])) {
		$field_type = $data['custom_field_type'];
	}

	if (!isset($data['custom_field_required'])) {
		$data['custom_field_required'] = 'n';
	}

	if (isset($data['type'])) {
		$field_type = $data['type'];
	}

	if (isset($data['field_type'])) {
		$field_type = $data['field_type'];
	}

	if (isset($data['field_values']) and !isset($data['custom_field_value'])) {
		$data['custom_field_values'] = $data['field_values'];
	}

	$data['custom_field_type'] = $field_type;

	if (isset($data['custom_field_value']) and strtolower($data['custom_field_value']) == 'array') {
		if (isset($data['custom_field_values']) and is_string($data['custom_field_values'])) {
			$try = base64_decode($data['custom_field_values']);
			if ($try != false) {
				$data['custom_field_values'] = unserialize($try);
			}
		}
	}
	if (isset($data['options']) and $data['options'] != '') {
		$data['options'] = decode_var($data['options']);
	}

	$dir = INCLUDES_DIR;
	$dir = $dir . DS . 'custom_fields' . DS;
	$field_type = str_replace('..', '', $field_type);
	if ($settings == true or isset($data['settings'])) {
		$file = $dir . $field_type . '_settings.php';
	} else {
		$file = $dir . $field_type . '.php';
	}
	if (is_file($file)) {
		$l = new MwView($file);
		//
		$l -> settings = $settings;

		if (isset($data) and !empty($data)) {
			$l -> data = $data;
		} else {
			$l -> data = array();
		}

		$layout = $l -> __toString();

		return $layout;
	}
}