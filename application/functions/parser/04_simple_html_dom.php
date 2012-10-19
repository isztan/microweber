<?php

require_once (APPPATH . 'functions' . DIRECTORY_SEPARATOR . 'parser' . DIRECTORY_SEPARATOR . 'simple_html_dom.php');
$pattern = '@# match nested tag
(?(DEFINE)
    (?<comment>     <!--.*?-->)
    (?<cdata>       <![CDATA[.*?]]>)
    (?<empty>       <\w+[^>]*?/>)
    (?<inline>      <(script|style)[^>]+>.*?</\g{-1}>)
    (?<nested>      <(\w+)[^>]*(?<!/)>(?&innerHTML)</\g{-1}>)
    (?<unclosed>        <\w+[^>]*(?<!/)>)
    (?<text>        [^<]+)
)
(?<outerHTML><(?<tagName>div)\s?(?<attributes>[^>]*?class\h*=\h*(?<quote>"|\')[^(?&quote)\v>]*\bedit\b[^(?&quote)\v>]*(?&quote)[^>]*)> # opening tag
(?<innerHTML>
    (?: (?&comment) | (?&cdata) | (?&empty) | (?&inline) | (?&nested) | (?&unclosed) | (?&text) )*
)
</(?&tagName)>) # closing tag
@six';


$pattern = '@# match nested tag
(?(DEFINE)
    (?<comment>     <!--.*?-->)
    (?<cdata>       <![CDATA[.*?]]>)
    (?<empty>       <\w+[^>]*?/>)
    (?<inline>      <(script|style)[^>]+>.*?</\g{-1}>)
    (?<nested>      <(\w+)[^>]*(?<!/)>(?&innerHTML)</\g{-1}>)
    (?<unclosed>        <\w+[^>]*(?<!/)>)
    (?<text>        [^<]+)
)
(?<outerHTML><(?<tagName>div)\s?(?<attributes>[^>]*?class\h*=\h*(?<quote>"|\')[^(?&quote)\v>]*\b\s*?edit.*?\h*\b[^(?&quote)\v>]*(?&quote)[^>]*)> # opening tag
(?<innerHTML>
    (?: (?&comment) | (?&cdata) | (?&empty) | (?&inline) | (?&nested) | (?&unclosed) | (?&text) )*
)
</(?&tagName)>) # closing tag
@six';


$the_trim = function_exists('mb_trim') ? 'mb_trim' : 'trim';

$all_edits = array();



preg_match_all($pattern, $layout, $matches);

$matches = (array_intersect_key($matches, array(
            'outerHTML' => 1
        )));
if (isarr($matches)) {

    foreach ($matches as $ve) {
        foreach ($ve as $ve1) {
            $all_edits[] = $ve1;
        }
    }
}
if (isarr($all_edits)) {
    foreach ($all_edits as $key => $value) {

        $value1 = false;
        if (is_arr($value)) {
            $value1 = $value[0];
            // $value2 = $value['innerHTML'];
        } else {
            $value1 = $value;
        }

        static $dom;
        if ($dom == false) {
            // Create a DOM object
            $dom = new simple_html_dom();
        }
        $dom->load($value1);

        $div = $dom->find('div.edit', 0);

        if ($div != false) {


            $inner = $div->innertext();

$name = $div->getAttribute('id');
 d($name);
 break;
            $name = $div->field;

            if (strval($name) == '') {
                $name = $div->id;
            }




            if (strval($name) == '') {
                $name = $div->data_field;
            }
            d($name);
            break;
            $rel = $node->getAttribute('rel');
            if ($rel == false) {
                $rel = 'page';
            }


            $option_group = $node->getAttribute('data-option_group');
            if ($option_group == false) {
                $option_group = 'editable_region';
            }
            $data_id = $node->getAttribute('data-id');


            $option_mod = $node->getAttribute('data-module');
            if ($option_mod == false) {
                $option_mod = $node->getAttribute('data-type');
            }
            if ($option_mod == false) {
                $option_mod = $node->getAttribute('type');
            }



            $get_global = false;
            //  $rel = 'page';
            $field = $name;
            $use_id_as_field = $name;

            if ($rel == 'global') {
                $get_global = true;
            } else {
                $get_global = false;
            }

            if ($rel == 'module') {
                $get_global = true;
            }
            if ($get_global == false) {
                //  $rel = 'page';
            }
            if ($rel == 'content') {
                if ($data_id != false) {
                    $data_id = intval($data_id);
                    $data = get_content_by_id($data_id);
                    $data ['custom_fields'] = get_custom_fields_for_content($data_id, 0);
                }
            } else if ($rel == 'page') {
                $data = get_page(PAGE_ID);
                $data ['custom_fields'] = get_custom_fields_for_content($data ['id'], 0);
            } else if (isset($attr ['post'])) {
                $data = get_post($attr ['post']);
                if ($data == false) {
                    $data = get_page($attr ['post']);
                    $data ['custom_fields'] = get_custom_fields_for_content($data ['id'], 0);
                }
            } else if (isset($attr ['category'])) {
                $data = get_category($attr ['category']);
            } else if (isset($attr ['global'])) {
                $get_global = true;
            }
            $cf = false;
            $field_content = false;

            if ($get_global == true) {


                if ($option_mod != false) {
                    //   d($field);

                    $field_content = get_option($field, $option_group, $return_full = false, $orderby = false, $option_mod);
                    //
                } else {
                    $field_content = get_option($field, $option_group, $return_full = false, $orderby = false);
                }
            } else {

                if ($use_id_as_field != false) {
                    if (isset($data [$use_id_as_field])) {
                        $field_content = $data [$use_id_as_field];
                    }
                    if ($field_content == false) {
                        if (isset($data ['custom_fields'] [$use_id_as_field])) {
                            $field_content = $data ['custom_fields'] [$use_id_as_field];
                        }
                        // d($field_content);
                    }
                }

                //  if ($field_content == false) {
                if (isset($data [$field])) {

                    $field_content = $data [$field];
                }
                //}
            }

            if ($field_content == false and isset($data ['custom_fields']) and !empty($data ['custom_fields'])) {
                foreach ($data ['custom_fields'] as $kf => $vf) {

                    if ($kf == $field) {

                        $field_content = ($vf);
                    }
                }
            }

            $pwithindiv = array();
            preg_match_all($pattern, $value1, $pwithindiv);

            $pwithindiv = (array_intersect_key($pwithindiv, array(
                        'innerHTML' => 1
                    )));

            if (isarr($pwithindiv)) {
                if (isset($pwithindiv["innerHTML"][0])) {

                    $value1 = $pwithindiv["innerHTML"][0];

                    $value1 = $the_trim($value1);
                }
            }
            if ($value1 != false and $field_content != false and $field_content != '') {
                $field_content = parse_micrwober_tags($field_content);
                $layout = str_replace($value1, $field_content, $layout);
            }
            $xpath = null;



            //  $layout = str_replace($value, 'aaa', $layout);
            //d($value);
        }
        break;



        if ($dom_edits != false) {

        }
    }
}