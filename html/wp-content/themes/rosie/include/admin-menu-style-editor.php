<?php
$padding_str = $width_str = '';
for($count=0;$count <= 500;$count=$count + 5){$padding_str .= '<option value="'. $count .'px">'. $count .'px</option>';}
$width_str .= '<optgroup label="'. __('In Percentage', 'vp_textdomain') .'">';
for($count=100;$count >= 5;$count=$count - 5){$width_str .= '<option value="'. $count .'%">'. $count .'%</option>';}
$width_str .= '</optgroup>';
$width_str .= '<optgroup label="'. __('In Pixels', 'vp_textdomain') .'">';
for($count=1140;$count >= 200;$count=$count - 100){$width_str .= '<option value="'. $count .'px">'. $count .'px</option>';}
$width_str .= '</optgroup>';

echo '<div id="ozyMegaMenuStyleWindow"><div>
			<p>
				<label for="custom-menu-html-shortcode">'. __('HTML or Shortcode', 'vp_textdomain') .'<br />
					<span>
						<textarea id="custom-menu-html-shortcode" class="widefat"></textarea>
					</span>
				</label>
			</p>
			<p>
				<label for="custom-menu-bg-color">'. __('Background Color', 'vp_textdomain') .'<br />
					<span>
						<input id="custom-menu-bg-color" type="text" class="widefat ozy-simple-color-picker"/>
					</span>
				</label>
			</p>
			<p>
				<label for="custom-menu-fn-color">'. __('Foreground Color', 'vp_textdomain') .'<br />
					<span>
						<input id="custom-menu-fn-color" type="text" class="widefat ozy-simple-color-picker"/>
					</span>
				</label>
			</p>
			<p>
				<label for="custom-menu-bg-image">'. __('Background Image', 'vp_textdomain') .'<br />
					<input id="custom-menu-bg-image" style="width:700px" type="text" class="widefat"/><input type="button" class="upload-image-button button" value="..."/>
					<a target="_blank" id="custom-menu-bg-image-preview" href="javascript:void(0);">'. __('Preview Image', 'vp_textdomain') .'<img src="'. OZY_BASE_URL .'images/blank.gif" alt=""/></a>
				</label>
			</p>
			<p>
				<label for="custom-menu-bg-repeat">'. __('Background Repeat', 'vp_textdomain') .'<br />
					<select id="custom-menu-bg-repeat" class="widefat">
						<option value="no-repeat">no-repeat</option>
						<option value="repeat">repeat all</option>
						<option value="repeat-x">repeat x</option>
						<option value="repeat-y">repeat y</option>
					</select>
				</label>
			</p>
			<p>
				<label for="custom-menu-bg-size">'. __('Background Size', 'vp_textdomain') .'<br />
					<select id="custom-menu-bg-size" class="widefat">
						<option value="auto">auto</option>
						<option value="contain">contain</option>
						<option value="cover">cover</option>
					</select>
				</label>
			</p>
			<p>
				<label for="custom-menu-bg-position-x">'. __('Background Position X', 'vp_textdomain') .'<br />
					<select id="custom-menu-bg-position-x" class="widefat">
						<option value="right">right</option>
						<option value="left">left</option>
						<option value="center">center</option>
					</select>
				</label>
			</p>
			<p>
				<label for="custom-menu-bg-position-y">'. __('Background Position Y', 'vp_textdomain') .'<br />
					<select id="custom-menu-bg-position-y" class="widefat">
						<option value="bottom">bottom</option>
						<option value="top">top</option>
						<option value="center">center</option>
					</select>
				</label>
			</p>
			<p>
				<label for="custom-menu-dropdown-width">'. __('Dropdown Width', 'vp_textdomain') .'<br />
					<select id="custom-menu-dropdown-width" class="widefat">
						<option value="auto">auto</option>
						'. $width_str .'
					</select>
				</label>
			</p>
			<p>
				<label for="custom-menu-dropdown-padding-top">'. __('Padding', 'vp_textdomain') .'</label><br />
					Top <select id="custom-menu-dropdown-padding-top" class="">'. $padding_str .'</select>
					Right <select id="custom-menu-dropdown-padding-right" class="">'. $padding_str .'</select>
					Bottom <select id="custom-menu-dropdown-padding-bottom" class="">'. $padding_str .'</select>
					Left <select id="custom-menu-dropdown-padding-left" class="">'. $padding_str .'</select>
			</p>
			<p>
				<a href="javascript:void(0);" class="button-primary" id="custom-menu-bg-apply">'. __('Apply Changes', 'vp_textdomain') .'</a>
				<br/>
				<i>'. __('Please note, you have to use "Save Menu" in order to get this changes applied', 'vp_textdomain') .'</i>
			</p>					
</div></div>';
?>