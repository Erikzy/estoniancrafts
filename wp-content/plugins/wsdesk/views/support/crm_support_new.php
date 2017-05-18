<?php
if (!defined('ABSPATH')) {
    exit;
}
ob_start();
$raiser_default = eh_crm_get_settingsmeta(0, 'ticket_raiser');
if($raiser_default == "registered")
{
    if(!is_user_logged_in())
    {
        echo '<div class="form-elements"><span>You must Login to Raise Ticket</span><br><a class="btn btn-primary" href="'. wp_login_url().'">Login</a></div>';
        echo '<div class="form-elements"><span>Need an Account?</span><br><a class="btn btn-primary" href="'. wp_registration_url().'">Register</a></div>';
        return ob_get_clean();
    }
}
$args = array("type" => "field");
$fields = array("slug", "title", "settings_id");
$avail_fields = eh_crm_get_settings($args, $fields);
$selected = eh_crm_get_settingsmeta(0, 'selected_fields');
if(empty($selected))
{
    $selected =array();
}
$default_fields = array('request_email', 'request_title', 'request_description');
$selected_fields = array_merge($default_fields, $selected);
$input_width = eh_crm_get_settingsmeta(0, 'input_width');
$title= eh_crm_get_settingsmeta(0, 'new_ticket_form_title');
echo '<div class="main_new_suppot_request_form">';
$css = '<style>
                .support_form
                {
                    width: ' . $input_width . '% !important;
                }
                </style>';
echo $css . '<form class="support_form" id="eh_crm_ticket_form">';
echo ($title!=='')?'<h3>'.$title.'</h3>':'';
for ($i = 0; $i < count($selected_fields); $i++) {
    for ($j = 0; $j < count($avail_fields); $j++) {
        if ($avail_fields[$j]['slug'] === $selected_fields[$i]) {
            echo '<div class="form-elements">';
            $current_meta = eh_crm_get_settingsmeta($avail_fields[$j]['settings_id']);
            $required = (isset($current_meta['field_require'])?$current_meta['field_require']:'');
            $required = ($required === "yes")?'required':'';
            echo '<span>' . $avail_fields[$j]['title'] . ' </span>';
            echo ($required === 'required') ? '<span class="input_required">*</span>' : ''.'<br>';
            $default_values = (isset($current_meta['field_default'])?$current_meta['field_default']:'');
            switch ($current_meta['field_type']) {
                case 'text':
                    echo '<input type="text" autocomplete="off" name="' . $selected_fields[$i] . '" id="' . $selected_fields[$i] . '" value="' . $default_values . '" class="input_element form-control" placeholder="' . $current_meta['field_placeholder'] . '" ' . $required . '>';
                    break;
                case 'email':
                    $email = "";
                    if(is_user_logged_in() && $avail_fields[$j]['slug'] == "request_email")
                    {
                        $id = get_current_user_id();
                        $user = new WP_User($id);
                        $default_values = $user->user_email;
                    }
                    echo '<input type="email" name="' . $selected_fields[$i] . '" id="' . $selected_fields[$i] . '" value="' . $default_values . '" class="input_element form-control" placeholder="' . $current_meta['field_placeholder'] . '"' . $required . '>';
                    break;
                case 'number':
                    echo '<input type="number" name="' . $selected_fields[$i] . '" id="' . $selected_fields[$i] . '" value="' . $default_values . '" class="input_element form-control" placeholder="' . $current_meta['field_placeholder'] . '"' . $required . '>';
                    break;
                case 'password':
                    echo '<input type="password" name="' . $selected_fields[$i] . '" id="' . $selected_fields[$i] . '" value="' . $default_values . '" class="input_element form-control" placeholder="' . $current_meta['field_placeholder'] . '"' . $required . '>';
                    break;
                case 'select':
                    $field_values = $current_meta['field_values'];
                    echo '<select class="input_element form-control" name="' . $selected_fields[$i] . '" id="' . $selected_fields[$i] . '">';
                    foreach ($field_values as $key => $value) {
                        $select_default = '';
                        if ($default_values === $key) {
                            $select_default = 'selected';
                        }
                        echo '<option value="' . $key . '" ' . $select_default . '>' . $value . '</option>';
                    }
                    echo '</select>';
                    break;
                case 'radio':
                    $field_values = $current_meta['field_values'];
                    echo '<span style="vertical-align: middle;">';
                    foreach ($field_values as $key => $value) {
                        $radio_default = '';
                        if ($default_values === $key) {
                            $radio_default = 'checked';
                        }
                        echo '<input type="radio" name="' . $selected_fields[$i] . '" id="' . $selected_fields[$i] . '" style="margin-top: 0;" value="' . $key . '" ' . $radio_default . '>' . $value . '<br>';
                    }
                    echo "</span>";
                    break;
                case 'checkbox':
                    $field_values = $current_meta['field_values'];
                    echo '<span style="vertical-align: middle;">';
                    foreach ($field_values as $key => $value) {
                        $check_default = '';
                        if ($default_values === $key) {
                            $check_default = 'checked';
                        }
                        echo '<input type="checkbox" name="' . $selected_fields[$i] . '" id="' . $selected_fields[$i] . '" style="margin-top: 0;" value="' . $key . '" ' . $check_default . '> ' . $value . '<br>';
                    }
                    echo "</span>";
                    break;
                case 'textarea':
                    echo '<textarea name="' . $selected_fields[$i] . '" id="' . $selected_fields[$i] . '" class="input_element form-control" ' . $required . '>' . $default_values . '</textarea>';
                    break;
                case 'file':
                    $file_type = ($current_meta['file_type'] == "multiple")?"multiple":"";
                    echo '<input type="file" name="ticket_attachment" id="ticket_attachment" ' . $file_type . ' class="input_element form-control" ' . $required . ' style="height: auto;">';
                    break;
            }
            echo '<small>' . $current_meta['field_description'] . '</small>';
            echo '</div><br>';
        }
    }
}
echo '<input  type="submit" id="crm_form_submit" class="btn btn-primary" data-loading-text="Submitting..." value="Submit Request"> ';
echo '<input  type="reset" class="btn btn-primary" value="Reset Request"></form>';
//echo '</div><div class="powered_wsdesk"><span>Powered by </span><a href="https://wsdesk.com" target="_blank">WSDesk</a></div>';
return ob_get_clean();
