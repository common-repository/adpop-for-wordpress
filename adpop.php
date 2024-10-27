<?php
/*
Plugin Name: AdPop for WordPress
Plugin URI: http://adpop.co
Description: Turn your external links into money. AdPop is an interstitital advertising program which let you make money without taking any ad space. AdPop converts all your outbound link to AdPop before reaches the target URL.
Version: 0.1
Author: AjariAkuWordPress
Author URI: http://adpop.co
License: GPL2
*/

add_action('wp_footer', 'adpop_get_script');

//get options
function adpop_get_options(){
    $exclude_untrimmed = explode(',', get_option('adpop_exclude'));
    
    $exclude = array();
    foreach ($exclude_untrimmed as $domain) {
      $exclude[] = trim($domain);
    }
    
    $options = array(
        'adpop_website_id' => get_option('adpop_website_id'),
        'adpop_exclude' => $exclude
    );
    return $options;
}

function adpop_get_script(){
    if(!get_option('adpop_enable')){
        return false;
    }
    //get plugin options
    $options = adpop_get_options();

    $script  = '<script type="text/javascript">';
    $script .= 'var website_id = '. $options['adpop_website_id'] .';';
    $script .= "var exclude_domains = [];";
    $script .= "exclude_domains.push('adpop.co');";
    foreach ($options['adpop_exclude'] as $domain) {
      $script .= "exclude_domains.push('". $domain ."');";
    }
    $script .= '</script>';
    $script .= '<script src="http://adpop.co/go.js" type="text/javascript"></script>';

    echo $script;
}

// Let's create the options menu
// create custom plugin settings menu
add_action('admin_menu', 'adpop_create_menu');

function adpop_create_menu() {

	//create new top-level menu
	add_options_page('AdPop Settings', 'AdPop Settings', 'administrator', __FILE__, 'adpop_settings_page', '', __FILE__);

	//call register settings function
	add_action( 'admin_init', 'adpop_register_mysettings' );
}


function adpop_register_mysettings() {
	//register our settings
	register_setting( 'adpop-settings-group', 'adpop_enable' );
	register_setting( 'adpop-settings-group', 'adpop_website_id' );
	register_setting( 'adpop-settings-group', 'adpop_exclude' );
}

function adpop_settings_page() {
?>
<div class="wrap">
<h2>AdPop for WordPress</h2>

<form method="post" action="options.php">
    <?php settings_fields( 'adpop-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Enable Plugin</th>
        <td><input type="checkbox" <?php if( get_option('adpop_enable' ) == 1){ echo 'checked'; }; ?> value="1" name="adpop_enable"/></td>
        </tr>

        <tr valign="top">
        <th scope="row">AdPop Website ID
        <?php 
        $website_id = (get_option('adpop_website_id') == '') ? '4' : get_option('adpop_website_id');
        ?>
        <td><input type="text" name="adpop_website_id" value="<?php echo $website_id; ?>" /> <br/> Visit <a href="http://adpop.co/websites">this page</a> and look for the website id to find the Website ID</th>
        </td>
        </tr>

        <tr valign="top">
        <th scope="row">Exclude these domains </th>
        <td>
            <?php 
            $exclude = (get_option('adpop_exclude') == '') ? parse_url(get_option('home'), PHP_URL_HOST) : get_option('adpop_exclude');
            ?>
            <input type="text" name="adpop_exclude" value="<?php echo $exclude; ?>" style="width: 350px;"/>
            <br/> Please specify domains which won't be converted to AdPop links here. <br>Separated by comma i.e google.com,wikipedia.org,yahoo.com
        </td>
        </tr>

    </table>

    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

    <p>
        Feedback, bug report, and suggestions are greatly appreciated. Please submit any question to <a href="http://help.adpop.co/customer/portal/questions/new">AdPop support</a>.
    </p>

</form>
</div>
<?php } ?>