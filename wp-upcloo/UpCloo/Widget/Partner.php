<?php
/**
 * UpCloo Widget
 *
 * This class enable the UpCloo widget for
 * get related contents usinv virtual site keys.
 *
 * @author Corley S.r.l.
 * @package UpCloo_Widget
 * @license MIT
 *
 * Copyright (C) 2012 Corley SRL
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
class UpCloo_Widget_Partner
    extends WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            "upcloo_partner_widget",
            __("UpCloo Network Widget", 'wp_upcloo'),
            array(
                'description' => __('The UpCloo Virtual Partner SiteKey Widget', 'wp_upcloo')
            )
        );
    }

    public function form($instance)
    {
        if ( $instance ) {
        	$title = esc_attr($instance[ 'upcloo_v_title' ]);
            $vsitekey = esc_attr($instance[ 'upcloo_v_sitekey' ]);
            $maxLinks = $instance['upcloo_v_max_links'];
        } else {
        	$title = __('Related', 'wp_upcloo');
            $vsitekey = '';
            $maxLinks = '';
        }
        ?>

        <label for="<?php echo $this->get_field_id('upcloo_v_title'); ?>"><?php _e('Title:', 'wp_upcloo'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('upcloo_v_title'); ?>" name="<?php echo $this->get_field_name('upcloo_v_title'); ?>" type="text" value="<?php echo $title; ?>" />

        <label for="<?php echo $this->get_field_id('upcloo_v_sitekey'); ?>"><?php _e('Virtual Partner:', 'wp_upcloo'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('upcloo_v_sitekey'); ?>" name="<?php echo $this->get_field_name('upcloo_v_sitekey'); ?>" type="text" value="<?php echo $vsitekey; ?>" />

        <label for="<?php echo $this->get_field_id('upcloo_v_max_links'); ?>"><?php _e('Number of links:', 'wp_upcloo'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('upcloo_v_max_links'); ?>" name="<?php echo $this->get_field_name('upcloo_v_max_links'); ?>" type="text" value="<?php echo $maxLinks; ?>" />

        <?php
    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['upcloo_v_title'] = strip_tags($new_instance['upcloo_v_title']);
        $instance['upcloo_v_sitekey'] = strip_tags($new_instance['upcloo_v_sitekey']);
        $instance['upcloo_v_max_links'] = strip_tags($new_instance['upcloo_v_max_links']);
        return $instance;
    }

    public function widget($args, $instance)
    {
        if (is_single($post)) {
            global $post;
            $sitekey = get_option("upcloo_sitekey");

            $virtualSiteKey = $instance["upcloo_v_sitekey"];

            echo $before_widget;
            $datax = array();

            if ($virtualSiteKey) {
                $manager = UpCloo_Manager::getInstance();
                $manager->setCredential(get_option(UPCLOO_USERKEY), get_option(UPCLOO_SITEKEY), get_option(UPCLOO_PASSWORD));
                $datax = $manager->get("{$post->post_type}_{$post->ID}", $virtualSiteKey);
            }

			if (is_array($datax)) {
    			foreach ($datax as $index => $doc) {
    			    if (is_numeric($instance["upcloo_v_max_links"]) && $index >= $instance["upcloo_v_max_links"]) {
    			        unset($datax[$index]);
    			        continue;
    			    }

    			    $datax[$index]["url"] = trim((string)$datax[$index]["url"]);
    			}

    			if ($datax) :
        			if (function_exists(UPCLOO_USER_WIDGET_CALLBACK)) :
        			    echo call_user_func(UPCLOO_USER_WIDGET_CALLBACK, $datax);
        			else :
?>
    <li class="widget-container widget_upcloo">
        <h3 class="widget-title"><?php echo $instance["upcloo_v_title"]?></h3>
        <div>
            <ul>
            <?php
                foreach ($datax as $index => $doc):
            ?>
            	<li>
            		<a href="<?php echo $doc["url"]?>" <?php echo ((upcloo_is_external_site($doc["url"])) ? 'target="_blank"': "")?>>
            		    <?php echo $doc["title"]; ?>
        		    </a>
    		    </li>
            <?php
                endforeach;
            ?>
            </ul>
        </div>
    </li>
<?php
                    endif;
                endif;
			}
            echo $after_widget;
        }
    }
}

