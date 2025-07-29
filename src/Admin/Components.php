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
			'value'       => '',
			'checked'     => false,
			'label'       => '',
			'description' => '',
			'disabled'    => false,
			'class'       => '',
		);

		$args = wp_parse_args( $args, $defaults );
		$id = esc_attr( $args['id'] );
		$name = esc_attr( $args['name'] );
		$value = esc_attr( $args['value'] );
		$checked = $args['checked'] ? 'checked' : '';
		$disabled = $args['disabled'] ? 'disabled' : '';
		$class = esc_attr( $args['class'] );

		ob_start();
		?>
		<div class="tiny-wp-modules-switch-wrapper <?php echo $class; ?>">
			<label class="tiny-wp-modules-switch">
				<input type="checkbox" 
					   id="<?php echo $id; ?>" 
					   name="<?php echo $name; ?>" 
					   value="<?php echo $value; ?>" 
					   <?php echo $checked; ?> 
					   <?php echo $disabled; ?> />
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
		);

		$args = wp_parse_args( $args, $defaults );
		$type = esc_attr( $args['type'] );
		$id = esc_attr( $args['id'] );
		$name = esc_attr( $args['name'] );
		$value = esc_attr( $args['value'] );
		$placeholder = esc_attr( $args['placeholder'] );
		$disabled = $args['disabled'] ? 'disabled' : '';
		$class = esc_attr( $args['class'] );
		$required = $args['required'] ? 'required' : '';

		ob_start();
		?>
		<?php if ( ! empty( $args['label'] ) ) : ?>
			<label for="<?php echo $id; ?>"><?php echo esc_html( $args['label'] ); ?></label>
		<?php endif; ?>
		<input type="<?php echo $type; ?>" 
			   id="<?php echo $id; ?>" 
			   name="<?php echo $name; ?>" 
			   value="<?php echo $value; ?>" 
			   placeholder="<?php echo $placeholder; ?>" 
			   class="<?php echo $class; ?>" 
			   <?php echo $disabled; ?> 
			   <?php echo $required; ?> />
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
			'class'       => 'button button-secondary',
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
}