<?php   
/*
	Plugin Name: WP URL Shortener
	Description: Generate shortlinks for post/pages
	Plugin URI: https://wordpress.org/plugins/wp-url-shortener/
	Version: 1.2
	Author: Bassem Rabia
	Author URI: mailto:bassem.rabia@hotmail.co.uk
	License: GPLv2
*/  

/* ----------------------------------------------*/  

	$plugin_name = 'WP URL Shortener';
	$plugin_version = '1.0';
   
	add_filter('manage_posts_columns', 'add_url_columns', 10, 2);
	function add_url_columns($posts_columns, $post_type){
		$posts_columns['url_columns'] = 'Bitly Url'; 
		return $posts_columns;
	}
	function url_bitly($input_url){ 
		$wp_url_shortener = get_option('wp_url_shortener'); 
		$login = $wp_url_shortener[0];
		$api_key = $wp_url_shortener[1];
		if(empty($login) OR empty($api_key)){
			echo 'Invalid <a href="admin.php?page=wp-url-shortener/wp-url-shortener.php">Login data !</a>';
		}else{
			if(!filter_var($input_url, FILTER_VALIDATE_URL)){
				echo 'Invalid URL.'; 
			}
			else{
				$url_enc = urlencode($input_url);
				$version = '2.0.1';  
				$format = 'json';
				$data = file_get_contents('http://api.bit.ly/shorten?version='.$version.'&login='.$login.'&apiKey='.$api_key.'&longUrl='.$url_enc.'&format='.$format);
				$json = json_decode($data, true);  
				// print_r( $json ) ; 
				foreach($json['results'] as $val){
					echo $val['shortUrl'];
				}
			}
		} 
	}
	function my_custom_columns($column){
		global $post;
		switch($column){
			case 'url_columns':?><span><?php url_bitly(get_permalink());?></span><?php break; 
		}
	}

	add_action('manage_posts_custom_column' , 'my_custom_columns');   
	add_action('admin_menu', 'wp_url_shortener_menu');  
	function wp_url_shortener_menu() {
		add_options_page( 
			'Url Shortener',
			'Url Shortener',
			'manage_options',
			'url_shortener_settings',
			'wp_url_shortener_settings_page'
		);
	}
	function wp_url_shortener_settings_page(){
		if(isset($_POST['wp_url_shortener_user_name'])){
			$wp_url_shortener = [];
			array_push($wp_url_shortener, $_POST['wp_url_shortener_user_name']);
			array_push($wp_url_shortener, $_POST['wp_url_shortener_api_key']); 
			// echo '<pre>'; print_r($wp_url_shortener);
			update_option('wp_url_shortener', $wp_url_shortener);
		}
		
		$wp_url_shortener = get_option('wp_url_shortener'); 
		// echo 'dd<pre>'; print_r($wp_url_shortener);
	
		$url_css = $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/css/style.css';
		echo '<link rel="stylesheet" type="text/css" href='.$url_css.' />';
		?>  
		<div class="wrap columns-2 wp-url-shortener">
			<div id="faqPage" class="icon32"></div>  
			<h2><?php echo $plugin_name .' '.$plugin_version; ?></h2>
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="postbox-container-1" class="postbox-container">
						<div class="postbox">
							<h3><span><?php _e('User manual', 'wp-url-shortener'); ?></span></h3>
							<div class="inside"> 
								<ol>
									<li><?php _e('Install', 'wp-url-shortener'); ?>
									</li>
									<li><?php _e('Activate', 'wp-url-shortener'); ?>
									</li>
									<li>
										<?php _e('Configure', 'wp-url-shortener'); ?> 
									</li>
									<li>
										<?php _e('Done', 'wp-url-shortener'); ?>
									</li>
								</ol>
							</div>
						</div>
					</div>
					<div id="postbox-container-2" class="postbox-container wp-url-shortener">
						<div class="stuffbox">
							<h3><label>Bitly <?php _e('Configuration', 'wp-url-shortener');?></label></h3>
							<div class="inside" style="overflow: auto;">  
							<form action="" method="post">
								<table cellpadding="3" cellspacing="2">
									<tr>
										<td><?php _e('User Name', 'wp-url-shortener');?></td>
										<td>
											<input class="input" type="text" name="wp_url_shortener_user_name" value="<?php echo $wp_url_shortener[0];?>" />
										</td>
									</tr>
									<tr>
										<td><?php _e('API Key', 'wp-url-shortener');?></td>
										<td>
											<input class="input" type="text" name="wp_url_shortener_api_key" value="<?php echo $wp_url_shortener[1];?>" />
										</td>
									</tr>
									<tr>
										<td></td>
										<td>
											<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'wp-url-shortener') ?>" />
										</td>
									</tr>
								</table>
							</form> 
							</div> 
						</div>
						<div class="stuffbox">
							<h3><label><?php _e('We highly recommend', 'wp-url-shortener');?></label></h3>
							<div class="inside plugins" style="overflow: auto;">  
								<div class="plugin"><a target="_blank" href="https://wordpress.org/plugins/wp-live-support/"><img src="<?php echo plugins_url('images/wp-live-support.png', __FILE__);?>"/></a></div>
								<div class="plugin"><a target="_blank" href="https://wordpress.org/plugins/plug-and-play/"><img src="<?php echo plugins_url('images/plug-and-play.png', __FILE__);?>"/></a></div>
								<div class="plugin"><a target="_blank" href="https://wordpress.org/plugins/facebook-ogg-meta-tags/"><img src="<?php echo plugins_url('images/facebook-ogg-meta-tags.png', __FILE__);?>"/></a></div>
							</div>
						</div>
					</div> 
				</div>
			</div>
		</div> 
	<?php 
	} 
?>