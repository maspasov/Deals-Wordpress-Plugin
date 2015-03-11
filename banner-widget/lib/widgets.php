<?php
/*
* Base Theme Banner Widget Class. Extend this class when adding new widgets instead of WP_Widget.
* Handles updating, displaying the form in wp-admin and $before_widget/$after_widget
*/
class ThemeBannerWidgetBase extends WP_Widget {
	// Should $before_widget and $after_widget be printed
	var $print_wrappers = true;
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		foreach ($this->custom_fields as $field) {
			if ($field['type'] == 'integer') {
				$instance[$field['name']] = intval($new_instance[$field['name']]);
			} else {
				$instance[$field['name']] = $new_instance[$field['name']];
			}
		}
		
		return $instance;
	}
 
	function form($instance) {
		$defaults = array();
		foreach ($this->custom_fields as $field) {
			$defaults[$field['name']] = $field['default'];
		}
		$instance = wp_parse_args( (array) $instance, $defaults);
		?>
		<?php if (empty($this->custom_fields)) : ?>
			<p>There are no available options for this widget</p>
		<?php endif; ?>
		<?php foreach ($this->custom_fields as $field) : ?>
			<?php call_user_func_array('banner_widget_field_'.$field['type'], array($this, $instance, $field['name'], $field['title'], $field))?>
		<?php endforeach; ?>
		<?php
	}
	
	function widget($args, $instance) {
        extract($args);
        if ($this->print_wrappers) {
        	echo $before_widget;
        }
        $this->front_end($args, $instance);
        if ($this->print_wrappers) {
        	echo $after_widget;
        }
    }
    
    /*abstract*/ function front_end($args, $instance) {
    	
    }
}

/*
* Field rendering functions. Called in the admin when showing the widget form.
*/
function banner_widget_field_text($obj, $instance, $fieldname, $fieldtitle) {
	$value = $instance[$fieldname];
	?>
	<p>
		<label for="<?php echo $obj->get_field_id($fieldname); ?>"><?php echo $fieldtitle; ?>:</label>
		<input class="widefat" id="<?php echo $obj->get_field_id($fieldname); ?>" name="<?php echo $obj->get_field_name($fieldname); ?>" type="text" value="<?php echo esc_attr($value); ?>" />
	</p>
	<?php
}
function banner_widget_field_integer($obj, $instance, $fieldname, $fieldtitle) {
	$value = intval($instance[$fieldname]);
	?>
	<p>
		<label for="<?php echo $obj->get_field_id($fieldname); ?>"><?php echo $fieldtitle; ?>:</label>
		<input class="widefat" id="<?php echo $obj->get_field_id($fieldname); ?>" name="<?php echo $obj->get_field_name($fieldname); ?>" type="text" value="<?php echo esc_attr($value); ?>" />
	</p>
	<?php
}
function banner_widget_field_textarea($obj, $instance, $fieldname, $fieldtitle) {
	$value = $instance[$fieldname];
	?>
	<p>
		<label for="<?php echo $obj->get_field_id($fieldname); ?>"><?php echo $fieldtitle; ?>:</label>
		<textarea class="widefat" id="<?php echo $obj->get_field_id($fieldname); ?>" name="<?php echo $obj->get_field_name($fieldname); ?>" style="height: 150px;" type="text"><?php echo esc_attr($value); ?></textarea>
	</p>
	<?php
}
function banner_widget_field_select($obj, $instance, $fieldname, $fieldtitle, $field_array) {
	$value = $instance[$fieldname];
	?>
	<p>
		<label for="<?php echo $obj->get_field_id($fieldname); ?>"><?php echo $fieldtitle; ?>:</label><br />
		<select name="<?php echo $obj->get_field_name($fieldname); ?>" id="<?php echo $obj->get_field_id($fieldname); ?>" style="width: 100%;">
			<?php foreach ($field_array['options'] as $val => $name) : ?>
				<option value="<?php echo $val; ?>" <?php echo ($val == esc_attr($value)) ? 'selected="selected"' : ''; ?>><?php echo $name; ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<?php
}
function banner_widget_field_set($obj, $instance, $fieldname, $fieldtitle, $field_array) {
	$value = $instance[$fieldname];
	if (!$value) {
		$value = array();
	}
	?>
	<p>
		<label for="<?php echo $obj->get_field_id($fieldname); ?>"><?php echo $fieldtitle; ?>:</label><br />
		<?php foreach ($field_array['options'] as $val => $name) : ?>
			<input type="checkbox" name="<?php echo $obj->get_field_name($fieldname); ?>[]" value="<?php echo $val; ?>" <?php echo (!(in_array($val, $value) === FALSE)) ? 'checked="checked"' : ''; ?>>&nbsp;<?php echo $name; ?><br />
		<?php endforeach; ?>
	</p>
	<?php
}

function banner_widget_field_media($obj, $instance, $fieldname, $fieldtitle, $field_array) {
	$value = $instance[$fieldname];
	?>
	<p class="widg_media">
		<label for="<?php echo $obj->get_field_id($fieldname); ?>"><?php echo $fieldtitle; ?>:</label>
		<input class="widefat" id="<?php echo $obj->get_field_id($fieldname); ?>" name="<?php echo $obj->get_field_name($fieldname); ?>" type="text" value="<?php echo esc_attr($value); ?>" /><br />
		<a id="<?php echo $obj->get_field_id($fieldname); ?>_media" class="button-primary thickbox widg_openmedia" type="button" rel="<?php echo $obj->get_field_id($fieldname); ?>" style="display: inline-block; margin-top: 7px;" href="media-upload.php?type=image&amp;TB_iframe=true&amp;width=631&amp;height=600">Upload/Select a photo</a>
	</p>
	<?php
}

?>