<div class="wrap">
<h2><?php _e("UpCloo General Options", "wp_upcloo");?></h2>
    <p>
<?php _e("UpCloo is a new service created for web site with a lot of contents (from 10.000 to milions).", "wp_upcloo");?>
    </p>
    <p>
<?php _e("If you manage contents on your web site (news, pages, reviews, products, comments...) then you're likely to spend a lot of time (and money!) to create correlations between different contents.", "wp_upcloo");?>
    </p>
    <p>
<?php _e("UpCloo can index your pages and send, whenever a visitor goes on you web site, the best correlations at the moment through a simple xml feed, so you can show links and correlations inside or outside the text of your page.", "wp_upcloo");?>
    </p>
    <p>
        <strong>
<?php _e("Don't worry anymore about \"more like this\" and \"maybe you're interested at\": UpCloo manages all your correlations with a cloudy, smart and brilliant semantic engine.", "wp_upcloo");?>
        </strong>
    </p>
    <div>
    <h2><?php _e("UpCloo Security", "wp_upcloo");?></h2>
        <?php _e("All information that you send to UpCloo Cloud System are secured using RSA 1024 bit.", "wp_upcloo");?>
    </div>
    <h2><?php _e("UpCloo Application Configuration", "wp_upcloo");?></h2>
    <h3 id="upcloo-app-config"><?php _e("Login parmeters", "wp_upcloo");?></h3>
    <form method="post" action="options.php#upcloo-app-config">
        <?php wp_nonce_field('update-options'); ?>
        <table class="form-table">
            <tbody>
            <tr valign="top">
                <th width="92" scope="row"><?php echo _e("Enter your User Key", "wp_upcloo");?></th>
                <td width="406">
                    <input name="upcloo_userkey" type="text" value="<?php echo get_option('upcloo_userkey', "wp_upcloo"); ?>" />
                    <strong>(eg. your-business-name)</strong></td>
            </tr>
            <tr valign="top">
                <th width="92" scope="row"><?php _e("Enter your Site Key", "wp_upcloo");?></th>
                <td width="406">
                    <input name="upcloo_sitekey" type="text" value="<?php echo get_option('upcloo_sitekey', "wp_upcloo"); ?>" />
                    <strong>(eg. your-site-name)</strong></td>
            </tr>
            <tr valign="top">
                <th width="92" scope="row"><?php echo _e("Enter your Password", "wp_upcloo");?></th>
                <td width="406">
                    <input name="upcloo_password" type="password" value="" />
                    <strong>
                        <?php _e("(eg. You account password [blank for security reasons])", "wp_upcloo");?>
                    </strong>
                </td>
            </tr>

            <input type="hidden" name="action" value="update" />
            <input type="hidden" name="page_options" value="upcloo_userkey,upcloo_sitekey,upcloo_password" />
            </tbody>
        </table>
        <p class="submit">
            <script type="text/javascript">
                ;var confirmThat = function() {
                    if (!confirm("Do you want to set this password?")) {
                        return false;
                    }
                };
            </script>
            <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" onclick="javascript:confirmThat()" />
        </p>
    </form>

    <h3 id="upcloo-other-features"><?php _e("Other features", "wp_upcloo");?></h3>
    <form method="post" action="options.php#upcloo-other-features">
        <?php wp_nonce_field('update-options'); ?>

        <table class="form-table" >
            <tr valign="top">
                <th width="92" scope="row"><?php _e("Index Posts", "wp_upcloo");?></th>
                <td width="406">
                    <?php $index_post = get_option("upcloo_index_post");?>
                    <input type="checkbox" name="upcloo_index_post" value="1" <?php checked("1" == $index_post); ?> />
                    <strong><?php _e("Index Posts", "wp_upcloo");?></strong></td>
            </tr>
            <tr valign="top">
                <th width="92" scope="row"><?php _e("Index Pages", "wp_upcloo");?></th>
                <td width="406">
                    <?php $index_page = get_option("upcloo_index_page");?>
                    <input type="checkbox" name="upcloo_index_page" value="1" <?php checked("1" == $index_page); ?> />
                    <strong><?php _e("Index Pages", "wp_upcloo");?></strong></td>
            </tr>
            <tr valign="top">
                <th width="92" scope="row"><?php _e("Use Categories during Indexing", "wp_upcloo");?></th>
                <td width="406">
                    <?php $index_category = get_option("upcloo_index_category");?>
                    <input type="checkbox" name="upcloo_index_category" value="1" <?php checked("1" == $index_category); ?> />
                    <strong><?php _e("Use categories during index creation", "wp_upcloo");?></strong></td>
            </tr>
            <tr valign="top">
                <th width="92" scope="row"><?php _e("Use Tags during Indexing", "wp_upcloo");?></th>
                <td width="406">
                    <?php $index_tag = get_option("upcloo_index_tag");?>
                    <input type="checkbox" name="upcloo_index_tag" value="1" <?php checked("1" == $index_tag); ?> />
                    <strong><?php _e("Use tags during index creation", "wp_upcloo");?></strong></td>
            </tr>
            <tr valign="top">
                <th width="92" scope="row"><?php _e("Show also in pages", "wp_upcloo");?></th>
                <td width="406">
                    <?php $show_on_page = get_option("upcloo_show_on_page");?>
                    <input type="checkbox" name="upcloo_show_on_page" value="1" <?php checked("1" == $show_on_page); ?> />
                    <strong><?php _e("Show related contents on pages", "wp_uplcoo");?></strong></td>
            </tr>
            <tr valign="top">
                <th width="92" scope="row"><?php _e("Max Number of Links", "wp_upcloo");?></th>
                <td width="406">
                    <?php $show_on_page = get_option("upcloo_max_show_link");?>
                    <input name="upcloo_max_show_links" type="text" value="<?php echo get_option('upcloo_max_show_links', ""); ?>" />
                    <strong><?php _e("Let blank for all", "wp_uplcoo");?></strong></td>
        </table>

        <input type="hidden" name="action" value="update" />
        <input type="hidden" name="page_options" value="upcloo_index_category,upcloo_index_tag,upcloo_index_page,upcloo_index_post,upcloo_show_on_page,upcloo_max_show_links" />

        <p class="submit">
            <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
        </p>
    </form>
    
    <h3 id="upcloo-roi-monitor"><?php _e("ROI Monitor Parameters", "wp_upcloo");?></h3>
    <p class="warning">
    	<?php _e("Consider that you have Google Analytics Tracker script activated and visibile on your pages or almost where UpCloo is engaged.", "wp_upcloo"); ?>
    </p>
    <form method="post" action="options.php#upcloo-roi-monitor">
        <?php wp_nonce_field('update-options'); ?>
        <table class="form-table">
            <tbody>
            	<tr valign="top">
                    <th width="92" scope="row"><?php echo _e("Enable UTM Tagging", "wp_upcloo");?></th>
                    <td width="406">
                    	<input name="upcloo_utm_tag" type="hidden" value="0" />
                        <input name="upcloo_utm_tag" type="checkbox" <?php echo ((get_option('upcloo_utm_tag', "wp_upcloo")) ? 'checked' : ''); ?> />
                        <strong>(Enable Google UTM Tag Feature)</strong>
                    </td>
                </tr>
                <tr valign="top">
                    <th width="92" scope="row"><?php echo _e("Enter base UTM Campaign", "wp_upcloo");?></th>
                    <td width="406">
                        <input name="upcloo_utm_campaign" type="text" value="<?php echo get_option('upcloo_utm_campaign', "wp_upcloo"); ?>" />
                        <strong><?php echo _e("(eg. upcloo-check)");?></strong>
                    </td>
                </tr>
                <tr valign="top">
                    <th width="92" scope="row"><?php echo _e("Enter base UTM Medium", "wp_upcloo");?></th>
                    <td width="406">
                        <input name="upcloo_utm_medium" type="text" value="<?php echo get_option('upcloo_utm_medium', "wp_upcloo"); ?>" />
                        <strong><?php echo _e("(eg. mywebsite)");?></strong>
                    </td>
                </tr>
                <tr valign="top">
                    <th width="92" scope="row"><?php echo _e("Enter base UTM Source", "wp_upcloo");?></th>
                    <td width="406">
                        <input name="upcloo_utm_source" type="text" value="<?php echo get_option('upcloo_utm_source', "wp_upcloo"); ?>" />
                        <strong><?php echo _e("(eg. upcloo-base-links)");?></strong>
                    </td>
                </tr>
            </tbody>
        </table>
            
        <input type="hidden" name="action" value="update" />
        <input type="hidden" name="page_options" value="upcloo_utm_campaign,upcloo_utm_tag, upcloo_utm_source, upcloo_utm_medium" />

        <p class="submit">
            <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
        </p>
    </form>
</div>
