<?php
/**
 * Admin Components Class
 *
 * @package TinyWpModules\Admin
 */

namespace TinyWpModules\Admin;

/**
 * Reusable UI Components
 */
class Components {

	/**
	 * Render a toggle switch
	 *
	 * @param array $args Switch arguments.
	 * @return string HTML output.
	 */
	public static function render_switch( $args = array() ) {
		$defaults = array(
			'id'          => '',
			'name'        => '',
			'value'       => '1',
			'checked'     => false,
			'label'       => '',
			'description' => '',
			'disabled'    => false,
			'class'       => '',
			'data_toggle' => '',
			'x_on_change' => '',
		);

		$args = wp_parse_args( $args, $defaults );
		$id = esc_attr( $args['id'] );
		$name = esc_attr( $args['name'] );
		$value = esc_attr( $args['value'] );
		$checked = $args['checked'] ? 'checked' : '';
		$disabled = $args['disabled'] ? 'disabled' : '';
		$class = esc_attr( $args['class'] );
		$data_toggle = ! empty( $args['data_toggle'] ) ? 'data-toggle="' . esc_attr( $args['data_toggle'] ) . '"' : '';
		$x_on_change = ! empty( $args['x_on_change'] ) ? esc_attr( $args['x_on_change'] ) : '';

		ob_start();
		?>
		<div class="tiny-wp-modules-switch-wrapper <?php echo $class; ?>">
			<label class="tiny-wp-modules-switch">
				<input type="checkbox" 
					   id="<?php echo $id; ?>" 
					   name="<?php echo $name; ?>" 
					   value="<?php echo $value; ?>" 
					   <?php echo $checked; ?> 
					   <?php echo $disabled; ?>
					   <?php echo $data_toggle; ?>
					   <?php if ( ! empty( $x_on_change ) ) : ?>@change="<?php echo $x_on_change; ?>"<?php endif; ?> />
				<span class="tiny-wp-modules-slider"></span>
			</label>
			<?php if ( ! empty( $args['label'] ) ) : ?>
				<span class="tiny-wp-modules-switch-label"><?php echo esc_html( $args['label'] ); ?></span>
			<?php endif; ?>
		</div>
		<?php if ( ! empty( $args['description'] ) ) : ?>
			<p class="description"><?php echo esc_html( $args['description'] ); ?></p>
		<?php endif; ?>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render a text input field
	 *
	 * @param array $args Input arguments.
	 * @return string HTML output.
	 */
	public static function render_input( $args = array() ) {
		$defaults = array(
			'type'        => 'text',
			'id'          => '',
			'name'        => '',
			'value'       => '',
			'placeholder' => '',
			'label'       => '',
			'description' => '',
			'disabled'    => false,
			'class'       => 'regular-text',
			'required'    => false,
			'content'     => '', // For info type
		);

		$args = wp_parse_args( $args, $defaults );
		$type = esc_attr( $args['type'] );
		$id = esc_attr( $args['id'] );
		$name = esc_attr( $args['name'] );
		$value = is_array( $args['value'] ) ? '' : esc_attr( $args['value'] ); // Handle array values
		$placeholder = esc_attr( $args['placeholder'] );
		$disabled = $args['disabled'] ? 'disabled' : '';
		$class = esc_attr( $args['class'] );
		$required = $args['required'] ? 'required' : '';

		ob_start();
		
		// Handle info type
		if ( $type === 'info' ) {
			echo '<div class="field-info">' . wp_kses_post( $args['content'] ) . '</div>';
			return ob_get_clean();
		}
		
		// Handle text type with base_url
		if ( $type === 'text' && isset( $args['base_url'] ) ) {
			echo '<div class="redirect-url-row">';
			echo '<span class="base-url">' . esc_html( $args['base_url'] ) . '</span>';
			echo '<input type="text" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '" placeholder="' . esc_attr( $placeholder ) . '" class="' . esc_attr( $class ) . '" />';
			echo '</div>';
			
			if ( ! empty( $args['description'] ) ) {
				echo '<p class="description">' . esc_html( $args['description'] ) . '</p>';
			}
			
			return ob_get_clean();
		}
		
		// Handle group_header type
		if ( $type === 'group_header' ) {
			return self::render_group_header( $args );
		}
		
		// Handle password type
		if ( $type === 'password' ) {
			echo '<div class="redirect-url-row">';
			echo '<span class="base-url">Password:</span>';
			echo '<input type="password" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '" placeholder="' . esc_attr( $placeholder ) . '" class="' . esc_attr( $class ) . '" />';
			echo '</div>';
			return ob_get_clean();
		}

		// Handle user_roles_simple type (without redirect URL layout)
		if ( $type === 'user_roles_simple' ) {
			$roles = self::get_available_user_roles();
			$selected_roles = is_array( $args['value'] ) ? $args['value'] : array();
			
			echo '<div class="user-roles-container">';
			echo '<div class="user-roles-grid">';
			
			foreach ( $roles as $role_slug => $role_name ) {
				$checked = in_array( $role_slug, $selected_roles ) ? true : false;
				
				// Use the reusable switch component for each role
				echo self::render_switch( array(
					'id' => $args['id'] . '_' . $role_slug,
					'name' => $args['name'] . '[' . $role_slug . ']',
					'value' => '1',
					'checked' => $checked,
					'label' => $role_name,
					'class' => 'role-switch'
				) );
			}
			
			echo '</div>'; // .user-roles-grid
			echo '</div>'; // .user-roles-container
			return ob_get_clean();
		}
		
		// Handle user_roles type
		if ( $type === 'user_roles' ) {
			$roles = self::get_available_user_roles();
			$selected_roles = is_array( $args['value'] ) ? $args['value'] : array();
			
			echo '<div class="user-roles-container">';
			
			// Show base URL, input field, and "for:" text for redirect fields
			echo '<div class="redirect-url-row">';
			echo '<span class="base-url">' . home_url() . '/</span>';
			
			// Use url_value and url_name if provided, otherwise use default
			$url_value = isset( $args['url_value'] ) ? $args['url_value'] : '';
			$url_name = isset( $args['url_name'] ) ? $args['url_name'] : $args['name'] . '_input';
			$url_id = isset( $args['url_name'] ) ? str_replace( 'tiny_wp_modules_settings[', '', str_replace( ']', '', $args['url_name'] ) ) : $args['id'] . '_input';
			
			$style = isset( $args['style'] ) ? ' style="' . esc_attr( $args['style'] ) . '"' : '';
			echo '<input type="text" id="' . esc_attr( $url_id ) . '" name="' . esc_attr( $url_name ) . '" value="' . esc_attr( $url_value ) . '" placeholder="e.g. dashboard" class="tiny-input"' . $style . ' />';
			echo '<span class="for-text">for:</span>';
			echo '</div>';
			
			// Show user roles checkboxes
			echo '<div class="user-roles-grid">';
			
			foreach ( $roles as $role_slug => $role_name ) {
				$checked = in_array( $role_slug, $selected_roles ) ? true : false;
				
				// Use the reusable switch component for each role
				echo self::render_switch( array(
					'id' => $args['id'] . '_' . $role_slug,
					'name' => $args['name'] . '[' . $role_slug . ']',
					'value' => '1',
					'checked' => $checked,
					'label' => $role_name,
					'class' => 'role-switch'
				) );
			}
			
			echo '</div>'; // .user-roles-grid
			echo '</div>'; // .user-roles-container
			return ob_get_clean();
		}
		
		?>
		<?php if ( ! empty( $args['label'] ) ) : ?>
			<label for="<?php echo $id; ?>"><?php echo esc_html( $args['label'] ); ?></label>
		<?php endif; ?>
		
		<?php if ( $type === 'textarea' ) : ?>
			<textarea id="<?php echo $id; ?>" 
					  name="<?php echo $name; ?>" 
					  placeholder="<?php echo $placeholder; ?>" 
					  class="<?php echo $class; ?>" 
					  <?php echo $disabled; ?> 
					  <?php echo $required; ?>><?php echo esc_textarea( $args['value'] ); ?></textarea>
		<?php else : ?>
			<input type="<?php echo $type; ?>" 
				   id="<?php echo $id; ?>" 
				   name="<?php echo $name; ?>" 
				   value="<?php echo $value; ?>" 
				   placeholder="<?php echo $placeholder; ?>" 
				   class="<?php echo $class; ?>" 
				   <?php echo $disabled; ?> 
				   <?php echo $required; ?> />
		<?php endif; ?>
		
		<?php if ( ! empty( $args['description'] ) ) : ?>
			<p class="description"><?php echo esc_html( $args['description'] ); ?></p>
		<?php endif; ?>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render a select dropdown
	 *
	 * @param array $args Select arguments.
	 * @return string HTML output.
	 */
	public static function render_select( $args = array() ) {
		$defaults = array(
			'id'          => '',
			'name'        => '',
			'options'     => array(),
			'selected'    => '',
			'label'       => '',
			'description' => '',
			'disabled'    => false,
			'class'       => '',
			'required'    => false,
		);

		$args = wp_parse_args( $args, $defaults );
		$id = esc_attr( $args['id'] );
		$name = esc_attr( $args['name'] );
		$selected = $args['selected'];
		$disabled = $args['disabled'] ? 'disabled' : '';
		$class = esc_attr( $args['class'] );
		$required = $args['required'] ? 'required' : '';

		ob_start();
		?>
		<?php if ( ! empty( $args['label'] ) ) : ?>
			<label for="<?php echo $id; ?>"><?php echo esc_html( $args['label'] ); ?></label>
		<?php endif; ?>
		<select id="<?php echo $id; ?>" 
				name="<?php echo $name; ?>" 
				class="<?php echo $class; ?>" 
				<?php echo $disabled; ?> 
				<?php echo $required; ?>>
			<?php foreach ( $args['options'] as $value => $label ) : ?>
				<option value="<?php echo esc_attr( $value ); ?>" 
						<?php selected( $selected, $value ); ?>>
					<?php echo esc_html( $label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php if ( ! empty( $args['description'] ) ) : ?>
			<p class="description"><?php echo esc_html( $args['description'] ); ?></p>
		<?php endif; ?>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render a button
	 *
	 * @param array $args Button arguments.
	 * @return string HTML output.
	 */
	public static function render_button( $args = array() ) {
		$defaults = array(
			'type'        => 'button',
			'text'        => '',
			'id'          => '',
			'class'       => 'tiny-btn tiny-btn-primary',
			'disabled'    => false,
			'icon'        => '',
			'attributes'  => array(),
		);

		$args = wp_parse_args( $args, $defaults );
		$type = esc_attr( $args['type'] );
		$text = esc_html( $args['text'] );
		$id = esc_attr( $args['id'] );
		$class = esc_attr( $args['class'] );
		$disabled = $args['disabled'] ? 'disabled' : '';
		$icon = $args['icon'];

		// Build additional attributes
		$attributes = '';
		foreach ( $args['attributes'] as $attr => $value ) {
			$attributes .= ' ' . esc_attr( $attr ) . '="' . esc_attr( $value ) . '"';
		}

		ob_start();
		?>
		<button type="<?php echo $type; ?>" 
				id="<?php echo $id; ?>" 
				class="<?php echo $class; ?>" 
				<?php echo $disabled; ?>
				<?php echo $attributes; ?>>
			<?php if ( ! empty( $icon ) ) : ?>
				<span class="dashicons dashicons-<?php echo esc_attr( $icon ); ?>"></span>
			<?php endif; ?>
			<?php echo $text; ?>
		</button>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render a card component
	 *
	 * @param array $args Card arguments.
	 * @return string HTML output.
	 */
	public static function render_card( $args = array() ) {
		$defaults = array(
			'title'       => '',
			'content'     => '',
			'class'       => '',
			'icon'        => '',
			'actions'     => array(),
		);

		$args = wp_parse_args( $args, $defaults );
		$title = $args['title'];
		$content = $args['content'];
		$class = esc_attr( $args['class'] );
		$icon = $args['icon'];

		ob_start();
		?>
		<div class="tiny-wp-modules-card <?php echo $class; ?>">
			<?php if ( ! empty( $title ) ) : ?>
				<div class="card-header">
					<?php if ( ! empty( $icon ) ) : ?>
						<span class="dashicons dashicons-<?php echo esc_attr( $icon ); ?>"></span>
					<?php endif; ?>
					<h3><?php echo esc_html( $title ); ?></h3>
				</div>
			<?php endif; ?>
			<div class="card-content">
				<?php echo $content; ?>
			</div>
			<?php if ( ! empty( $args['actions'] ) ) : ?>
				<div class="card-actions">
					<?php foreach ( $args['actions'] as $action ) : ?>
						<?php echo self::render_button( $action ); ?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render a status indicator
	 *
	 * @param array $args Status arguments.
	 * @return string HTML output.
	 */
	public static function render_status( $args = array() ) {
		$defaults = array(
			'status'      => 'info', // info, success, warning, error
			'text'        => '',
			'icon'        => '',
			'class'       => '',
		);

		$args = wp_parse_args( $args, $defaults );
		$status = esc_attr( $args['status'] );
		$text = esc_html( $args['text'] );
		$icon = $args['icon'];
		$class = esc_attr( $args['class'] );

		ob_start();
		?>
		<div class="tiny-wp-modules-status tiny-wp-modules-status-<?php echo $status; ?> <?php echo $class; ?>">
			<?php if ( ! empty( $icon ) ) : ?>
				<span class="dashicons dashicons-<?php echo esc_attr( $icon ); ?>"></span>
			<?php endif; ?>
			<span class="status-text"><?php echo $text; ?></span>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render a toggle module with configuration
	 *
	 * @param array $args Module arguments.
	 * @return string HTML output.
	 */
	public static function render_toggle_module( $args = array() ) {
		$defaults = array(
			'id'                    => '',
			'name'                  => '',
			'value'                 => '1',
			'checked'               => false,
			'label'                 => '',
			'description'           => '',
			'config_title'          => '',
			'config_description'    => '',
			'config_fields'         => array(),
			'class'                 => '',
			'data_toggle'           => '',
		);

		$args = wp_parse_args( $args, $defaults );
		$id = esc_attr( $args['id'] );
		$name = esc_attr( $args['name'] );
		$value = esc_attr( $args['value'] );
		$checked = $args['checked'] ? 'checked' : '';
		$class = esc_attr( $args['class'] );
		$data_toggle = ! empty( $args['data_toggle'] ) ? 'data-toggle="' . esc_attr( $args['data_toggle'] ) . '"' : '';
		$config_title = $args['config_title'];
		$config_description = $args['config_description'];
		$config_fields = $args['config_fields'];

		ob_start();
		?>
		<div class="tiny-wp-modules-switch-wrapper <?php echo $class; ?>">
			<label class="tiny-wp-modules-switch">
				<input type="checkbox" 
					   id="<?php echo $id; ?>" 
					   name="<?php echo $name; ?>" 
					   value="<?php echo $value; ?>" 
					   <?php echo $checked; ?> 
					   <?php echo $data_toggle; ?> />
				<span class="tiny-wp-modules-slider"></span>
			</label>
			<?php if ( ! empty( $args['label'] ) ) : ?>
				<span class="tiny-wp-modules-switch-label"><?php echo esc_html( $args['label'] ); ?></span>
			<?php endif; ?>
		</div>
		<?php if ( ! empty( $args['description'] ) ) : ?>
			<div class="setting-description">
				<?php echo esc_html( $args['description'] ); ?>
			</div>
		<?php endif; ?>
		
		<?php if ( ! empty( $config_title ) ) : ?>
			<!-- Configuration Card -->
			<details class="module-config-card" data-toggle-target="<?php echo esc_attr( $args['data_toggle'] ); ?>" <?php echo $checked ? 'open' : ''; ?>>
				<summary class="module-config-header">
					<h4><?php echo esc_html( $config_title ); ?></h4>
					<p><?php echo esc_html( $config_description ); ?></p>
				</summary>
				<div class="module-config-content">
					<?php foreach ( $config_fields as $field ) : ?>
						<div class="module-config-field">
							<label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
							<?php echo self::render_input( $field ); ?>
							<?php if ( ! empty( $field['description'] ) ) : ?>
								<span class="field-description"><?php echo esc_html( $field['description'] ); ?></span>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</details>
		<?php endif; ?>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render a reusable expandable module with toggle switch and collapse/expand link
	 *
	 * Usage Example:
	 * 
	 * echo Components::render_expandable_module( array(
	 *     'id' => 'my_module',
	 *     'name' => 'tiny_wp_modules_settings[my_module]',
	 *     'checked' => isset( $settings['my_module'] ) ? $settings['my_module'] : 0,
	 *     'label' => __( 'Enable My Module', 'tiny-wp-modules' ),
	 *     'description' => __( 'Description of what this module does.', 'tiny-wp-modules' ),
	 *     'data_toggle' => 'my-module-config',
	 *     'config_fields' => array(
	 *         array(
	 *             'type' => 'text',
	 *             'id' => 'my_field',
	 *             'name' => 'tiny_wp_modules_settings[my_field]',
	 *             'value' => isset( $settings['my_field'] ) ? $settings['my_field'] : '',
	 *             'placeholder' => __( 'Enter value', 'tiny-wp-modules' ),
	 *             'class' => 'tiny-input',
	 *             'label' => __( 'My Field', 'tiny-wp-modules' ),
	 *             'description' => __( 'Description of this field', 'tiny-wp-modules' )
	 *         )
	 *     )
	 * ) );
	 *
	 * @param array $args {
	 *     @type string $id                    Unique ID for the toggle switch
	 *     @type string $name                  Name attribute for the toggle switch
	 *     @type string $value                 Value for the toggle switch (default: '1')
	 *     @type bool   $checked               Whether the toggle is checked
	 *     @type string $label                 Label text for the toggle switch
	 *     @type string $description           Description text below the toggle
	 *     @type string $class                 Additional CSS classes
	 *     @type string $data_toggle           Data attribute for toggle target
	 *     @type array  $config_fields         Array of configuration fields
	 *     @type string $config_title          Title for the configuration section
	 *     @type string $config_description    Description for the configuration section
	 * }
	 * @return string HTML output
	 */
	public static function render_expandable_module( $args = array() ) {
		$defaults = array(
			'id' => '',
			'name' => '',
			'value' => '1',
			'checked' => false,
			'label' => '',
			'description' => '',
			'class' => '',
			'data_toggle' => '',
			'config_fields' => array(),
			'config_title' => '',
			'config_description' => ''
		);

		$args = wp_parse_args( $args, $defaults );

		// Validate required parameters
		if ( empty( $args['id'] ) || empty( $args['name'] ) || empty( $args['label'] ) ) {
			return '';
		}

		$checked = $args['checked'] ? 'checked' : '';
		$data_toggle = ! empty( $args['data_toggle'] ) ? 'data-toggle="' . esc_attr( $args['data_toggle'] ) . '"' : '';

		ob_start();
		?>
		<div class="expandable-module <?php echo esc_attr( $args['class'] ); ?>" 
			 x-data="{ 
			 	configVisible: <?php echo $args['checked'] ? 'true' : 'false' ?>,
			 	isCollapsed: true,
			 	toggleId: '<?php echo esc_attr( $args['data_toggle'] ); ?>'
			 }"
			 data-expandable-module="<?php echo esc_attr( $args['data_toggle'] ); ?>">
			<!-- Toggle Switch -->
			<div class="module-toggle">
				<?php echo self::render_switch( array(
					'id' => $args['id'],
					'name' => $args['name'],
					'value' => $args['value'],
					'checked' => $args['checked'],
					'label' => $args['label'],
					'class' => 'toggle-switch',
					'data_toggle' => $args['data_toggle'],
					'x_on_change' => 'configVisible = $event.target.checked'
				) ); ?>
				
				<?php if ( ! empty( $args['description'] ) ) : ?>
					<div class="setting-description">
						<?php echo esc_html( $args['description'] ); ?>
					</div>
				<?php endif; ?>
			</div>

			<!-- Configuration Fields -->
			<?php if ( ! empty( $args['config_fields'] ) ) : ?>
				<div class="config-fields-section" 
					 id="config-<?php echo esc_attr( $args['data_toggle'] ); ?>" 
					 data-toggle-target="<?php echo esc_attr( $args['data_toggle'] ); ?>"
					 x-show="configVisible"
					 x-transition:enter="transition ease-out duration-200"
					 x-transition:enter-start="opacity-0 transform -translate-y-2"
					 x-transition:enter-end="opacity-100 transform translate-y-0"
					 x-transition:leave="transition ease-in duration-150"
					 x-transition:leave-start="opacity-100 transform translate-y-0"
					 x-transition:leave-end="opacity-0 transform -translate-y-2"
					 :class="{ 'show': configVisible, 'collapsed': isCollapsed }">
					<?php if ( ! empty( $args['config_title'] ) || ! empty( $args['config_description'] ) ) : ?>
						<div class="config-header">
							<?php if ( ! empty( $args['config_title'] ) ) : ?>
								<h4><?php echo esc_html( $args['config_title'] ); ?></h4>
							<?php endif; ?>
							<?php if ( ! empty( $args['config_description'] ) ) : ?>
								<p><?php echo esc_html( $args['config_description'] ); ?></p>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<?php foreach ( $args['config_fields'] as $field ) : ?>
						<div class="config-field">
							<?php echo self::render_input( $field ); ?>
						</div>
					<?php endforeach; ?>
				</div>

				<!-- Collapse/Expand Link - Outside the config-fields-section -->
				<div class="config-collapse-link" 
					 id="collapse-<?php echo esc_attr( $args['data_toggle'] ); ?>" 
					 data-toggle-target="<?php echo esc_attr( $args['data_toggle'] ); ?>"
					 x-show="configVisible"
					 x-transition:enter="transition ease-out duration-200"
					 x-transition:enter-start="opacity-0"
					 x-transition:enter-end="opacity-100"
					 x-transition:leave="transition ease-in duration-150"
					 x-transition:leave-start="opacity-100"
					 x-transition:leave-end="opacity-0">
					<a href="#" class="collapse-toggle" 
					   data-toggle="<?php echo esc_attr( $args['data_toggle'] ); ?>" 
					   @click.prevent="isCollapsed = !isCollapsed">
						<span class="collapse-text" x-text="isCollapsed ? 'EXPAND' : 'COLLAPSE'"></span>
						<span class="collapse-icon" x-text="isCollapsed ? '▼' : '▲'"></span>
					</a>
				</div>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render a form table row
	 *
	 * @param array $args Row arguments.
	 * @return string HTML output.
	 */
	public static function render_form_row( $args = array() ) {
		$defaults = array(
			'label'       => '',
			'content'     => '',
			'description' => '',
			'required'    => false,
		);

		$args = wp_parse_args( $args, $defaults );
		$label = $args['label'];
		$content = $args['content'];
		$description = $args['description'];
		$required = $args['required'];

		ob_start();
		?>
		<tr>
			<th scope="row">
				<?php if ( $required ) : ?>
					<span class="required">*</span>
				<?php endif; ?>
				<?php echo esc_html( $label ); ?>
			</th>
			<td>
				<?php echo $content; ?>
				<?php if ( ! empty( $description ) ) : ?>
					<p class="description"><?php echo esc_html( $description ); ?></p>
				<?php endif; ?>
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}
	
	/**
	 * Render a group header component
	 *
	 * @param array $args Group header arguments.
	 * @return string HTML output.
	 */
	public static function render_group_header( $args = array() ) {
		$defaults = array(
			'title'       => '',
			'description' => '',
			'class'       => '',
		);

		$args = wp_parse_args( $args, $defaults );
		$title = $args['title'];
		$description = $args['description'];
		$class = esc_attr( $args['class'] );

		ob_start();
		?>
		<div class="group-header <?php echo $class; ?>">
			<?php if ( ! empty( $title ) ) : ?>
				<h4><?php echo esc_html( $title ); ?></h4>
			<?php endif; ?>
			<?php if ( ! empty( $description ) ) : ?>
				<p><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Get all available user roles
	 * 
	 * @return array Array of user roles
	 */
	private static function get_available_user_roles() {
		$roles = array();
		$wp_roles = wp_roles();
		
		if ( $wp_roles ) {
			foreach ( $wp_roles->roles as $role_slug => $role_data ) {
				$roles[$role_slug] = $role_data['name'];
			}
		}
		
		return $roles;
	}
}