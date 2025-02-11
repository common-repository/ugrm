<?php
add_action('admin_menu', 'UGRM_Menu');



function UGRM_Menu() {
  //create entry in settings menu
  add_options_page('LDAP Mappings', 'LDAP Mappings', 'manage_options', 'LDAPMappings', 'UGRM_page_builder');
}

//Render the options page
function UGRM_page_builder() {
  //First, ensure the user has manage_options level access
  if (!current_user_can('manage_options')) {
    wp_die( __('You do not have sufficient permissions to access this page.') );
  }
  //Check for form posting and update options 
  if (isset($_POST['UGRM_hidden']) && $_POST['UGRM_hidden'] == 'go_for_launch') {
    update_option('UGRM_admin_role', UGRM_define_post_var('UGRM_admin_role'));
    update_option('UGRM_editor_role', UGRM_define_post_var('UGRM_editor_role'));
    update_option('UGRM_author_role', UGRM_define_post_var('UGRM_author_role'));
    update_option('UGRM_contributor_role', UGRM_define_post_var('UGRM_contributor_role'));
    update_option('UGRM_subscriber_role', UGRM_define_post_var('UGRM_subscriber_role'));
        
    if((isset($_POST['UGRM_return_target_to_HTTPS']) && $_POST['UGRM_return_target_to_HTTPS'])== 'checked') {
      update_option('UGRM_return_target_to_HTTPS', UGRM_define_post_var('UGRM_return_target_to_HTTPS'));
    }
    else {
      update_option('UGRM_return_target_to_HTTPS', '');
    }
    
   ?>
    <div id="message" class="updated fade">
    <p><strong><?php _e('Options Saved.', 'UGRM_header'); ?> </strong></p></div>
  <?php
   }
?>
 <div class="wrap">
  <h2><?php _e('LDAP to Wordpress Role Mappings', 'UGRM_header'); ?></h2>
   <p>This plugin extends the functionality of the Wordpress Shibboleth plugin and provides mapping of Wordpress roles to
   LDAP mappings. The highest level of access should prevail when a user exists in multiple groups.</p>
   <p>Populate the fields below with the DN of the groups of your choice.</p>
   
   <form method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
   <input type="hidden" name="UGRM_hidden" value="go_for_launch" />
   <table class="form-table">
     <tr valign="top">
       <th scope="row">LDAP DN for Admin Role</th>
       <td><input type="text" style="width:780px;" name="UGRM_admin_role" value="<?php echo get_option('UGRM_admin_role'); ?>"/></td>
    </tr>
    
     <tr valign="top">
       <th scope="row">LDAP DN for Editor Role</th>
       <td><input type="text" style="width:780px;" name="UGRM_editor_role" value="<?php echo get_option('UGRM_editor_role'); ?>"/></td>
    </tr>
    
     <tr valign="top">
       <th scope="row">LDAP DN for Author Role</th>
       <td><input type="text" style="width:780px;" name="UGRM_author_role" value="<?php echo get_option('UGRM_author_role'); ?>"/></td>
    </tr>    
    
     <tr valign="top">
       <th scope="row">LDAP DN for Contributor Role</th>
       <td><input type="text" style="width:780px;" name="UGRM_contributor_role" value="<?php echo get_option('UGRM_contributor_role'); ?>"/></td>
    </tr>    
    
     <tr valign="top">
       <th scope="row">LDAP DN for Subscriber Role</th>
       <td><input type="text" style="width:780px;" name="UGRM_subscriber_role" value="<?php echo get_option('UGRM_subscriber_role'); ?>"/></td>
    </tr>
     
     <tr valign="top">
        <th scope="row">Force Shibboleth return target to HTTPS</th>
        <td><input type="checkbox" name="UGRM_return_target_to_HTTPS" value="checked" <?php echo get_option("UGRM_return_target_to_HTTPS") ; ?>/></td>
     </tr>
  </table>
  <p>The force return target to HTTPS option allows you to override the default Shibboleth behavior of constructing the return target using current protocol. For example,
      if you clicked the login link from HTTP, your return target would use HTTP. Check the box above to always use a return target using HTTPS regardless if the login link was
      clicked on an HTTP page. This allows you to seemlessly deliver content pages via HTTP and enforce HTTPS for management pages.
  </p>
  
  <p class="submit">
    <input type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
  </p>
  
</div>
<?php
}

//Let's remove any nasty user input.
function UGRM_input_validate($input) {
  $newinput = trim($input);
  /*
  //This function was originally written to pass clean input and replace dirty input with Empty string.
  //I've commented the if block below rather than removing to preserve the regex. It is nifty and might be usefull in the future.
  if(!preg_match('/^.*[a-z0-9\-]$/i', $newinput)) {
    $newinput = '';
  }
  */
  $newinput = preg_replace('/[^a-z0-9\-]$/i', '', $newinput);
  return $newinput;
}

//This function does the dirty work of retrieving & cleaning input from $_POST[]
function UGRM_define_post_var($input_post_var) {
  if (isset($_POST[$input_post_var])) {
    $clean_output = UGRM_input_validate($_POST[$input_post_var]);
    return $clean_output;  
  } 
  else {
    die("$input_post_var is not present in POST");
  }
}

?>