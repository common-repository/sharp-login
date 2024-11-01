<div id="sharplogin_settings" class="sharplogin_settings">
    <form method="post" action="options.php">
        <?php settings_fields('sharplogin_settingss'); ?>
        <?php do_settings_sections('sharplogin_settingss'); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">
                    <label for="sharplogin_settings[login_logo]">Login Logo</label>
                </th>
                <td>
                    <input type="text" class="regular-text process_custom_images" id="sharplogin_settings_logo" name="sharplogin_settings[login_logo]" value="<?php echo $login_logo; ?>" placeholder="http://"><button class="set_custom_logo button">Select Logo</button>
                    <!-- <input type="text" id="sharplogin_settings[login_logo]" name="sharplogin_settings[login_logo]" value="<?php echo $login_logo; ?>" class="regular-text" /> -->
                    <!-- <input type="button" class="button-secondary" value="Upload Image" id="upload_image_button" /> -->
                    <p class="description">Upload your logo image here. Recommended size: <strong>300 x 100</strong> pixels.</p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="sharplogin_settings[login_bg_img]">Login Background Image</label>
                </th>
                <td>
                    <input type="text" id="sharplogin_settings[login_bg_img]" name="sharplogin_settings[login_bg_img]" value="<?php echo isset($settings['login_bg_img']) ? esc_url($settings['login_bg_img']) : ""; ?>" class="regular-text" />
                    <p class="description">Enter Login Background image URL here</p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="sharplogin_settings[login_title]">Login Title</label>
                </th>
                <td>
                    <input type="text" id="sharplogin_settings[login_title]" name="sharplogin_settings[login_title]" value="<?php echo isset($settings['login_title']) ? esc_attr($settings['login_title']) : "";  ?>" class="regular-text" />
                    <p class="description">Enter your login title here. This will be displayed on the login page.</p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="sharplogin_page">Login URL</label></th>
                <td>
                    <code> <?php echo trailingslashit(home_url()); ?></code><input id="sharplogin_page" type="text" name="sharplogin_page" value="<?php echo esc_attr(get_site_option('sharplogin_page', 'login')); ?>">
                    <p class="description">Protect your website by changing the login URL and preventing access to the wp-login.php page and the wp-admin directory to non-connected people</p>
                </td>
            </tr>
        </table>
        <!-- wordpress form submit nounce -->
        <?php wp_nonce_field('sharplogin_settings_nonce', 'sharplogin_settings_nonce_field'); ?>
        <?php submit_button('Update', 'primary', 'sharplogin_submit'); ?>
    </form>
</div>