<div id="sharplogin_failed_login" class="sharplogin_failed_login">
    <h2>Login Attempts</h2>
    <form method="post" action="options.php">
        <?php settings_fields('sl_login_attempts_settings'); ?>
        <?php do_settings_sections('sl_login_attempts_settings'); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">
                    <label for="sl_login_attempts_enabled">
                        <?php _e('Enabled', 'sharplogin'); ?>
                    </label>
                </th>
                <td>
                    <input type="checkbox" name="sl_login_attempts_settings[sl_login_attempts_enabled]" id="sl_login_attempts_enabled" value="1" <?php checked(1, $sl_login_enabled); ?> />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="sl_login_attempts_max_attempts">
                        <?php _e('Max Attempts', 'sharplogin'); ?>
                    </label>
                </th>
                <td>
                    <input type="text" name="sl_login_attempts_settings[sl_login_attempts_max_attempts]" id="sl_login_attempts_max_attempts" value="<?php echo $sl_login_attempts_max_attempts; ?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="sl_login_attempts_lockout_time">
                        <?php _e('Lockout Time', 'sharplogin'); ?>
                    </label>
                </th>
                <td>
                    <input type="text" name="sl_login_attempts_settings[sl_login_attempts_lockout_time]" id="sl_login_attempts_lockout_time" value="<?php echo $sl_login_attempts_lockout_time; ?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="sl_login_attempts_lockout_time_unit">
                        <?php _e('Lockout Time Unit', 'sharplogin'); ?>
                    </label>
                </th>
                <td>
                    <select name="sl_login_attempts_settings[sl_login_attempts_lockout_time_unit]" id="sl_login_attempts_lockout_time_unit">
                        <option value="minutes" <?php selected('minutes', $sl_login_attempts_lockout_time_unit); ?>><?php _e('Minutes', 'sharplogin'); ?></option>
                        <option value="hours" <?php selected('hours', $sl_login_attempts_lockout_time_unit); ?>><?php _e('Hours', 'sharplogin'); ?></option>
                        <option value="days" <?php selected('days', $sl_login_attempts_lockout_time_unit); ?>><?php _e('Days', 'sharplogin'); ?></option>
                    </select>
                </td>
            </tr>
            <!-- <tr valign="top">
                <th scope="row">
                    <label for="sl_login_attempts_lockout_message">
                        <?php _e('Lockout Message', 'sharplogin'); ?>
                    </label>
                </th>
                <td>
                    <textarea name="sl_login_attempts_settings[sl_login_attempts_lockout_message]" id="sl_login_attempts_lockout_message" rows="5" cols="50"><?php echo $sl_login_attempts_lockout_message; ?></textarea>
                </td>
            </tr> -->
        </table>
        <?php submit_button(); ?>
    </form>

    </form>
</div>