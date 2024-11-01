<?php
/*
Plugin Name: WP-EyeEm
Plugin URI: http://wpeyeem.chilibean.de/
Description: This plugin easily embeds your EyeEm Photostream on your WordPress blog.
Author: Guido Helms
Version: 0.0.1 Alpha
Author URI: http://www.chilibean.de/
License: GPLv3
*/

include_once 'WP_EyeEm.class.php';

$plugin_dir = basename(dirname(__FILE__));
load_plugin_textdomain('wp-eyeem', false, $plugin_dir . "/languages/");


function showEyeEmPhotos()
{
	$username = get_option('wp-eyeem-username');
	$maxsize = get_option('wp-eyeem-maxwidth');

	if(!empty($username))
	{
		if (empty($maxsize))
			$maxsize = null;
		try {
			
			$eyeem = new WP_EyeEm($username, $maxsize);
			echo outputEyeEmToString($eyeem);
		}
		catch (Exception $e)
		{
			echo sprintf(__("User with id %s not found.", "wp-eyeem"),$username);
		}
    }
}

function outputEyeEmToString(WP_Eyeem $eyeem)
{
	$ret = "";
	
	
	if ($eyeem->getUser()->totalPhotos > 0)
	{
		$ret .= "<div>";
		$ret .= sprintf(__("The 20 newest photos by %s on EyeEm", "wp-eyeem"), $eyeem->getUser()->nickname);
		$ret .= "</div>";
			
		foreach ($eyeem->getPhotos() as $photo)
		{
			$ret .= "
			<div class=\"eyeem_post\">
			<a href=\"" . $photo->webUrl . "\" title=\"" . $photo->caption . " by " . $eyeem->getUser()->nickname . " on EyeEm\" target=\"_blank\"><img class=\"eyeem_photo\" title=\"" . $photo->caption . " by " . $eyeem->getUser()->nickname . " on EyeEm\" alt=\"" . $photo->caption . " by " . $eyeem->getUser()->nickname . " on EyeEm\" src=\"" . $eyeem->getMaxWidthPhotoUrl($photo) . "\" /></a>
			<div class=\"eyeem_caption\"><a href=\"" . $photo->webUrl . "\" target=\"_blank\">" . $photo->caption . "</a> by <a href=\"" . $eyeem->getUser()->webUrl . "\" target=\"_blank\">" . $eyeem->getUser()->nickname . "</a> on <a href=\"http://www.eyeem.com\" target=\"_blank\">EyeEm</a></div>
			</div>
			";
		}
	
	}
	else
	{
		$ret .= "<p>";
		$ret .= __("User has no photos", "wp-eyeem");
		$ret .= "</p>";
	}
	
	return $ret;
}

add_shortcode('wpeyeem', 'showEyeEmPhotos');

function wp_eyeem_admin()
{
	add_menu_page(
			"WP-EyeEm",
			"WP-EyeEm",
			"administrator",
			basename(__FILE__),
			"eyeem_admin_page"
	);
}

add_action('admin_menu', 'wp_eyeem_admin');

function eyeem_admin_page()
{
	if($_POST['wp-eyeem-hidden'] == 'Y')
	{
		$username = $_POST['wp-eyeem-username'];
		$maxsize = $_POST['wp-eyeem-maxwidth'];
		
		update_option('wp-eyeem-username', $username);
		update_option('wp-eyeem-maxwidth', $maxsize);
	}

	$username = get_option('wp-eyeem-username');
	$maxsize = get_option('wp-eyeem-maxwidth');
	?>
    <div class="wrap">
        <h2>WP-EyeEm Configuration</h2>
        <form name="wp-eyeem-config-form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
            <input type="hidden" name="wp-eyeem-hidden" value="Y" />
            <h4>
                <?php echo __('Plugin configuration', 'wp-eyeem'); ?>
            </h4>
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row">
                            <label for="wp-eyeem-username">
                                <?php
                                    echo __("EyeEm userid: ", 'wp-eyeem'); 
                                ?>
                            </label>
                        </th>
                        <td>
                            <input type="text" id="wp-eyeem-username" name="wp-eyeem-username" value="<?php echo $username; ?>" size="20" />
                            <br />
                            <em><?php echo __("Please enter your EyeEm userid <br />ex.: <strong>1234</strong>", 'wp-eyeem'); ?></em>
                        </td>
                    </tr>
                    <tr valign="top">
                    	<th scope="row">
                    		<label for="wp-eyeem-maxwidth">
                    			<?php 
                    				echo __("Max. Imagewidth: ", 'wp-eyeem');
                    			?>
                    		</label>
                    	</th>
                    	<td>
                    		<input type="text" id="wp-eyeem-maxwidth" name="wp-eyeem-maxwidth" value="<?php echo $maxsize; ?>" size="5" /> pixel
                    		<br />
                    		<em><?php echo __("Please enter the max width of your images. Leave it empty if you want the standard width of 640 pixel<br />ex.: <strong>550</strong> pixel", 'wp-eyeem'); ?></em>
                    	</td>
                    </tr>
                </tbody>
            </table>
            <p class="submit">  
                <input type="submit" name="Submit" value="<?php echo __('Update Options', 'wp-eyeem'); ?>" /> 
            </p> 
        </form>
    </div>
<?php
}

function add_eyeem_style()
{
	if(!is_admin())
	{
		wp_enqueue_style('WP_EyeEm_Style', plugin_dir_url( __FILE__ ) . 'style.css');
	}
}

add_action('init', 'add_eyeem_style');

?>