<?php
/*

 * Plugin Name: AMP Static Pages 

 */

add_action('admin_menu', 'amp_plugin_create_menu');

function amp_plugin_create_menu() {
    add_menu_page('AMP Static Pages', 'AMP Static', 'administrator', __FILE__, 'amp_plugin_settings_page', plugins_url('/images/icon.png', __FILE__));
}

function amp_plugin_settings_page() {
    ?>
    <div class="wrap">
        <h1>AMP Static Pages</h1>
        <div class="log log-extended-false" extended="false">
            <div class="log-extention-button" onclick="toggle_log()">
                <span class="dashicons right dashicons-arrow-right-alt2"></span>
                <span class="dashicons left dashicons-arrow-left-alt2"></span>
            </div>
            <div class="log-text">
                <p class="button-primary clear-log" onclick="clear_log()">Clear</p>
            </div>
        </div>
        <div class="controls">
            <select class="selected-tab" onchange="openPostType(jQuery(this).val());">
                <?php foreach (get_post_types() as $PostType) { 
                    if(empty(get_posts(array('numberposts' => '-1', 'post_type' => $PostType)))) {continue;} ?>
                    <option value="<?php echo $PostType; ?>"><?php echo $PostType; ?></option>
                <?php } ?>
            </select>
            <p class="button-primary" onclick="reload_sections(jQuery('.selected-tab').val());">Load URL Responses</p>
        </div>
        <div class="Tabs">
            <?php 
            $first_tab = true;
            foreach (get_post_types() as $PostType) {
                $display = "none";
                if(empty(get_posts(array('numberposts' => '-1', 'post_type' => $PostType)))) {
                    continue;          
                } else if($first_tab) {
                    $display = "block";
                    $first_tab = false;
                }
                    ?>
                <div class="Tab" id="<?php echo $PostType; ?>" style="display:<?php echo $display; ?>;">
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <td class="check-column">
                                    <input type="checkbox">
                                </td>
                                <td>
                                    Post name
                                </td>
                                <td class="aligncenter">
                                    Url response
                                </td>
                                <td class="aligncenter">
                                    Last modified
                                </td>
                                <td class="aligncenter">
                                    AMP file exists
                                </td>
                                <td>
                                </td>
                            </tr>
                        </thead>
                        <?php 
                        foreach (get_posts(array('numberposts' => '-1', 'post_type' => $PostType)) as $POST) { ?>
                            <tr>
                                <th scope="row" class="check-column">	
                                    <input type="checkbox">
                                </th>
                                <td>
                                    <a href="<?php echo draft_permalink($POST); ?>amp/" target="_blank">
                                        <?php echo get_the_title($POST); ?>
                                    </a>
                                    <span class="loading" style="visibility:hidden">
                                        <img src="/wp-admin/images/wpspin_light.gif" alt="Loading icon" title="Loading" style="height:19px;width:19px;margin:0 0 -4px 10px;"/>
                                    </span>
                                </td>
                                <td class="get_response_code aligncenter" data-url="<?php echo draft_permalink($POST); ?>amp/">
                                </td>
                                <td class="last-modified aligncenter">
                                    <?php echo (file_exists(get_amp_file_path($POST->ID)) ? date("d F Y H:i:s.",filemtime(get_amp_file_path($POST->ID))) : ""); ?>
                                </td>
                                <td class="aligncenter">
                                    <?php echo (file_exists(get_amp_file_path($POST->ID)) ? "<span class='state' style='font-weight:bold;color:green'>true</span>" : "<span class='state' style='font-weight:bold;color:red'>false</span>"); ?>
                                </td>
                                <td class="aligncenter">
                                    <p class="button-primary" onclick="set_url_response(jQuery(this).parents('tr').find('.get_response_code'))">Response</p>
                                    <p class="button-primary create update <?php echo (file_exists(get_amp_file_path($POST->ID)) ? "" : "disabled"); ?>" onclick="create_static_amp_page('<?php echo $POST->ID; ?>', this)"><?php echo (file_exists(get_amp_file_path($POST->ID)) ? "Update" : "Create"); ?></p>
                                    <p class="button-primary delete<?php echo (file_exists(get_amp_file_path($POST->ID)) ? "" : " disabled"); ?>" onclick="delete_static_amp_page('<?php echo $POST->ID; ?>', this)">Delete</p>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            <?php } ?>
        </div>
    </div>
<?php } 
add_action('admin_footer', 'amp_static_scripts'); // Write our JS below here

function amp_static_scripts() {
        ?>
    <style>
        .aligncenter {
            text-align:center !important;
        }
        .disabled2 {
            color: #66c6e4!important;
            background: #008ec2!important;
            border-color: #007cb2!important;
            -webkit-box-shadow: none!important;
            box-shadow: none!important;
            text-shadow: 0 -1px 0 rgba(0,0,0,.1)!important;
            cursor: default;
        }
        
        .log {
            position: fixed;
            right: 0;
            top: 0;
            bottom: 0;
            background-color: white;
            width:22px;
        }
        
        .log-extention-button {
            width:20px;
            height:200%;
            border-left:1px solid black;
            border-right:1px solid black;
        }
        
        .log-extended-false .log-extention-button .right {
            display:none;
        }
        .log-extended-false .log-text {
            display:none;
        }        
        .log-extended-true .log-extention-button .left {
            display:none;
        }
        .log-extention-button .left,.log-extention-button .right {
            margin-top: calc(100vh/2);
        }
        
        .log-text {
            height: 100%;
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            width: calc(50% - 42px);
            padding: 40px 10px;
            overflow:scroll;
        }
        .clear-log {
            
        }
    </style>
    <script type="text/javascript" >
        function toggle_log() {
            if(jQuery('.log').hasClass("log-extended-true")) {
               jQuery('.log').removeClass('log-extended-true'); 
               jQuery('.log').addClass('log-extended-false'); 
               jQuery('.log').css('width','22px'); 
            } else {
               jQuery('.log').addClass('log-extended-true'); 
               jQuery('.log').removeClass('log-extended-false'); 
               jQuery('.log').css('width','50%'); 
            }
        }
        
        function clear_log() {
            jQuery('.log-text p').each(function(i,e) {
                if(!jQuery(e).hasClass('clear-log')) {
                    jQuery(e).remove();
                }
            });
        }
        function reload_sections(tab_id) {
            jQuery('#' + tab_id + ' .get_response_code').each(function(i,e) {
                set_url_response(e);
            });
        }
        
        function set_url_response(e) {
            jQuery(e).parents('tr').find('.loading').css('visibility', 'visible');
            jQuery(e).html('');
            jQuery(document).ready(function ($) {
                var data = {
                    'action': 'amp_get_response_code', 
                    'url': jQuery(e).data('url')
                };
                jQuery.post(ajaxurl, data, function (response) {
                    Result = JSON.parse(response);
                    if(Result.ResponseCode  == 200) {
                        jQuery(e).html('<span style="color:green;font-weight:bold;" title="'+ Result.ResponseCode +'">Sucess (' + jQuery(e).data('url') + ')</span>');
                        jQuery(e).parents('tr').find('.create').removeClass('disabled2');
                        jQuery(e).parents('tr').find('.create').removeClass('disabled');
                    } else {
                        jQuery(e).html('<span style="color:red;font-weight:bold;" title="'+ Result.ResponseCode +'">Error (' + jQuery(e).data('url') + ')</span>');
                        jQuery(e).parents('tr').find('.create').addClass('disabled2');
                    }
                    jQuery(e).parents('tr').find('.loading').css('visibility', 'hidden');
                    jQuery('.log-text').append("<p>'" + jQuery(e).data('url') + "' responded with '" + Result.ResponseCode + "'</p>");
                });
            });
        }
        
        $LoadTab_url_responses = [];

        function openPostType(PostType) {
            jQuery('.Tab').css('display', 'none');
            jQuery('#' + PostType).css('display', 'block');
        }

        function create_static_amp_page(PostID, Parent) {
            if(!jQuery(Parent).hasClass("disabled") && !jQuery(Parent).hasClass("disabled2")) {
                jQuery(Parent).addClass("disabled");
                jQuery(Parent).parents('tr').find('.loading').css('visibility', 'visible');
                jQuery(document).ready(function ($) {
                    var data = {
                        'action': 'amp_create_action',
                        'ID': PostID
                    };
                    jQuery.post(ajaxurl, data, function (response) {
                        jQuery(Parent).parents('tr').find('.state').css('color', 'green');
                        jQuery(Parent).parents('tr').find('.state').text('true');
                        jQuery(Parent).parents('tr').find('.create').text('Update');
                        jQuery(Parent).parents('tr').find('.delete').removeClass('disabled');
                        jQuery(Parent).removeClass('disabled');
                        jQuery(Parent).parents('tr').find('.loading').css('visibility', 'hidden');
                    });
                });
            } else if(jQuery(Parent).hasClass("disabled2")) {
                alert('This requires a successful url response. Please load/reload the response.');
            }
            set_url_response(jQuery(Parent).parents('tr').find('.get_response_code'));
        }

        function delete_static_amp_page(PostID, Parent) {
            if(!jQuery(Parent).hasClass("disabled")) {
                jQuery(document).ready(function ($) {
                jQuery(Parent).parents('tr').find('.create').addClass('disabled');
                jQuery(Parent).parents('tr').find('.loading').css('visibility', 'visible');
                    var data = {
                        'action': 'amp_delete_action',
                        'ID': PostID
                    };
                    jQuery.post(ajaxurl, data, function (response) {
                        jQuery(Parent).parents('tr').find('.state').css('color', 'red');
                        jQuery(Parent).parents('tr').find('.state').text('false');
                        jQuery(Parent).parents('tr').find('.create').text('Create');
                        jQuery(Parent).parents('tr').find('.create').removeClass('disabled');
                        jQuery(Parent).parents('tr').find('.delete').addClass('disabled');
                        jQuery(Parent).parents('tr').find('.loading').css('visibility', 'hidden');
                    });
                });
            }
            set_url_response(jQuery(Parent).parents('tr').find('.get_response_code'));
        }

    </script> <?php
}

add_action('wp_ajax_amp_get_response_code','amp_get_response_code');

function amp_get_response_code() {
    $url = $_POST['url'];
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
    curl_setopt($ch, CURLOPT_NOBODY, true);    // we don't need body
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_TIMEOUT,10);
    $output = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $result = [];
    $result['ResponseCode'] = $httpcode;
    
    echo json_encode($result);
    wp_die();
}

add_action('wp_ajax_amp_create_action', 'amp_create_action');

function amp_create_action() {
    create_amp_page($_POST['ID']);
}

add_action('wp_ajax_amp_delete_action', 'amp_delete_action');

function amp_delete_action() {
    delete_amp_page($_POST['ID']);
}

function get_amp_file_path($ID) {
    $saved_post = get_post($ID);
    $NewFilePath = $_SERVER['DOCUMENT_ROOT'];
    $NewFilePath .= str_replace(get_site_url(), "", draft_permalink($saved_post));
    return $NewFilePath . "amp/index.html";
}

function get_amp_folder($ID) {
    $saved_post = get_post($ID);
    $NewFilePath = $_SERVER['DOCUMENT_ROOT'];
    $NewFilePath .= str_replace(get_site_url(), "", draft_permalink($saved_post));
    return $NewFilePath . "amp/";
}

function delete_amp_page($ID) {
    delete_directory(get_amp_folder($ID));
    echo "1";
    wp_die();
}

function create_amp_page($ID) {
    $saved_post = get_post($ID);
    $NewFilePath = $_SERVER['DOCUMENT_ROOT'];
    $NewFilePath .= str_replace(get_site_url(), "", draft_permalink($saved_post));
    if (get_post_status($ID) == "publish") {
        mkdir($NewFilePath, 0755, true);
        $Files = explode("/", str_replace(get_site_url(), "", draft_permalink($saved_post)));
        $FullPaths = [];
        $Count = 0;
        foreach ($Files as $File) {
            if ($File != "") {
                if ($Count == 0) {
                    $FullPaths[$Count] = "/" . $File;
                } else {
                    $FullPaths[$Count] = $FullPaths[($Count - 1)] . "/" . $File;
                }
                $Count++;
            }
        }
        $Root = $_SERVER['DOCUMENT_ROOT'];
        $PHPINDEX = "<?php define('WP_USE_THEMES', true);require('" . $Root . "/wp-blog-header.php' );";
        $Paths = "";
        foreach ($FullPaths as $Path) {

            if (!file_exists($Root . $Path . "/index.php")) {

                file_put_contents($Root . $Path . "/index.php", $PHPINDEX);
            }
        }

        if (file_exists($NewFilePath . "amp/index.html")) {
            unlink($NewFilePath . "amp/index.html");
        }

        rmdir($NewFilePath . "amp/");
        $AMP_Content = get_remote_data(draft_permalink($saved_post) . "amp/");
        mkdir($NewFilePath . "amp/", 0755, true);
        file_put_contents($NewFilePath . "amp/index.html", $AMP_Content);
    }
    echo "1";
    wp_die();
}

function delete_directory($dirname) {
    if (is_dir($dirname)) {
        $dir_handle = opendir($dirname);
    } elseif (!$dir_handle) {
        return false;
    }
    while ($file = readdir($dir_handle)) {
        if ($file != "." && $file != "..") {
            if (!is_dir($dirname . "/" . $file)) {
                unlink($dirname . "/" . $file);
            } else {
                delete_directory($dirname . '/' . $file);
            }
        }
    }
    closedir($dir_handle);
    rmdir($dirname);
    return true;
}

function draft_permalink($POST) {
    if ($POST->post_status != "publish") {
        $my_post = clone $POST;
        $my_post->post_status = 'published';
        $my_post->post_name = sanitize_title($my_post->post_name ? $my_post->post_name : $my_post->post_title, $my_post->ID);
        $permalink = rtrim(get_permalink($my_post), "/") . "/";
    } else {

        $permalink = rtrim(get_permalink($POST), "/") . "/";
    }
    return $permalink;
}

function get_remote_data($url, $post_paramtrs = false) {
    $c = curl_init();
    curl_setopt($c, CURLOPT_URL, $url);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    if ($post_paramtrs) {

        curl_setopt($c, CURLOPT_POST, TRUE);
        curl_setopt($c, CURLOPT_POSTFIELDS, "var1=bla&" . $post_paramtrs);
    } curl_setopt($c, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($c, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; rv:33.0) Gecko/20100101 Firefox/33.0");
    curl_setopt($c, CURLOPT_COOKIE, 'CookieName1=Value;');
    curl_setopt($c, CURLOPT_MAXREDIRS, 10);
    $follow_allowed = ( ini_get('open_basedir') || ini_get('safe_mode')) ? false : true;

    if ($follow_allowed) {

        curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
    }curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 9);
    curl_setopt($c, CURLOPT_REFERER, $url);
    curl_setopt($c, CURLOPT_TIMEOUT, 60);
    curl_setopt($c, CURLOPT_AUTOREFERER, true);
    curl_setopt($c, CURLOPT_ENCODING, 'gzip,deflate');
    $data = curl_exec($c);
    $status = curl_getinfo($c);
    curl_close($c);
    preg_match('/(http(|s)):\/\/(.*?)\/(.*\/|)/si', $status['url'], $link);
    if ($status['http_code'] == 200) {
        return $data;
    } elseif ($status['http_code'] == 301 || $status['http_code'] == 302) {
        if (!$follow_allowed) {
            if (empty($redirURL)) {
                if (!empty($status['redirect_url'])) {
                    $redirURL = $status['redirect_url'];
                }
            } if (empty($redirURL)) {
                preg_match('/(Location:|URI:)(.*?)(\r|\n)/si', $data, $m);
                if (!empty($m[2])) {
                    $redirURL = $m[2];
                }
            } if (empty($redirURL)) {
                preg_match('/href\=\"(.*?)\"(.*?)here\<\/a\>/si', $data, $m);
                if (!empty($m[1])) {
                    $redirURL = $m[1];
                }
            } if (!empty($redirURL)) {
                $t = debug_backtrace();
                return call_user_func($t[0]["function"], trim($redirURL), $post_paramtrs);
            }
        }
    }
    return "ERRORCODE22 with $url!!<br/>Last status codes<b/>:" . json_encode($status) . "<br/><br/>Last data got<br/>:$data";
}
?>