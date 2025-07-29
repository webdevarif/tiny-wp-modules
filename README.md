# Tiny WP Modules

A modular WordPress plugin with OOP architecture and Composer autoloading.

## Features

- **PSR-4 Autoloading**: Uses Composer for efficient class autoloading
- **Modular Architecture**: Separated Admin, Public, and Core modules
- **OOP Design**: Object-oriented programming with proper separation of concerns
- **WordPress Standards**: Follows WordPress coding standards and best practices
- **Internationalization**: Ready for translations with text domain support
- **Hook Loader**: Centralized hook management system
- **Settings API**: WordPress Settings API integration
- **Modern UI**: Clean and responsive admin interface

## Requirements

- PHP 7.4 or higher
- WordPress 5.0 or higher
- Composer

## Installation

1. **Clone or download** the plugin to your WordPress plugins directory:
   ```bash
   cd wp-content/plugins/
   git clone https://github.com/your-username/tiny-wp-modules.git
   ```

2. **Install Composer dependencies**:
   ```bash
   cd tiny-wp-modules
   composer install
   ```

3. **Activate the plugin** through the WordPress admin panel or via WP-CLI:
   ```bash
   wp plugin activate tiny-wp-modules
   ```

## Development Setup

1. **Install development dependencies**:
   ```bash
   composer install --dev
   ```

2. **Run tests**:
   ```bash
   composer test
   ```

3. **Check coding standards**:
   ```bash
   composer phpcs
   ```

4. **Fix coding standards**:
   ```bash
   composer phpcbf
   ```

## Plugin Structure

```
tiny-wp-modules/
├── src/
│   ├── Core/
│   │   ├── Plugin.php          # Main plugin class
│   │   ├── Loader.php          # Hook loader
│   │   ├── I18n.php           # Internationalization
│   │   ├── Activator.php      # Plugin activation handler
│   │   ├── Deactivator.php    # Plugin deactivation handler
│   │   └── Updater.php        # GitHub update handler
│   ├── Admin/
│   │   ├── Admin.php          # Admin functionality
│   │   ├── Settings.php       # Settings management
│   │   └── Ajax_Handler.php   # AJAX functionality
│   └── Public/
│       └── Public_Handler.php  # Public functionality
├── templates/
│   └── admin/
│       ├── admin-page.php     # Main admin page
│       └── settings-page.php  # Settings page
├── assets/
│   ├── css/
│   │   ├── admin.css         # Admin styles
│   │   └── public.css        # Public styles
│   └── js/
│       ├── admin.js          # Admin scripts
│       └── public.js         # Public scripts
├── languages/                 # Translation files
├── tests/                    # PHPUnit tests
├── composer.json             # Composer configuration
├── tiny-wp-modules.php      # Main plugin file
├── uninstall.php            # Plugin uninstall handler
└── README.md                # This file
```

## Usage

### Admin Interface

The plugin adds a "Tiny Modules" menu item to the WordPress admin panel with:

- **Dashboard**: Overview of plugin status and information
- **Settings**: Configure plugin options

### Adding New Modules

1. **Create a new module class** in the appropriate directory:
   ```php
   namespace TinyWpModules\YourModule;
   
   class YourModule {
       public function __construct() {
           // Initialize your module
       }
   }
   ```

2. **Register the module** in the main Plugin class:
   ```php
   private function load_dependencies() {
       // ... existing code ...
       $this->your_module = new YourModule();
   }
   ```

3. **Add hooks** in the appropriate hook definition method:
   ```php
   private function define_your_module_hooks() {
       $this->loader->add_action('your_hook', $this->your_module, 'your_method');
   }
   ```

### Settings

The plugin includes a settings system with:

- **Enable Modules**: Toggle all modules on/off
- **Debug Mode**: Enable debug mode for development
- **GitHub Updates**: Configure GitHub Personal Access Token for automatic updates from private repositories

### Activation and Deactivation

The plugin includes proper activation and deactivation handlers:

#### Activation
- Sets default plugin options
- Creates necessary database tables
- Flushes rewrite rules
- Sets activation timestamps
- Clears existing caches

#### Deactivation
- Flushes rewrite rules
- Clears caches and transients
- Sets deactivation timestamps
- Preserves user settings and data

#### Uninstall
- Completely removes all plugin data when deleted
- Removes all options and transients
- Drops database tables
- Clears scheduled hooks

### Hooks and Filters

The plugin provides several hooks for customization:

#### Actions
- `tiny_wp_modules_init`: Fired when plugin initializes
- `tiny_wp_modules_admin_loaded`: Fired when admin is loaded
- `tiny_wp_modules_public_loaded`: Fired when public is loaded

#### Filters
- `tiny_wp_modules_settings`: Filter plugin settings
- `tiny_wp_modules_admin_menu`: Filter admin menu items

### GitHub Updates

The plugin includes automatic update functionality from GitHub:

#### Features
- **Private Repository Support**: Update from private GitHub repositories using Personal Access Tokens
- **Automatic Update Checks**: WordPress will automatically check for updates
- **Manual Update Checks**: Check for updates manually from the admin interface
- **Plugin Row Integration**: "Check for Updates" link appears in the WordPress plugins list page (like ACF Pro)
- **Real-time Feedback**: Loading states and status messages during update checks
- **Token Validation**: Validate GitHub tokens before saving
- **Secure Updates**: Uses WordPress update system for secure plugin updates

#### Plugin Row Updates
The plugin adds a "Check for Updates" link in the WordPress plugins list page that:
- Shows loading states with spinner during checks
- Displays real-time status messages (up to date, update available, error)
- Provides visual feedback with color-coded messages
- Integrates seamlessly with WordPress admin interface
- Requires proper GitHub token configuration for private repositories

#### Setup
1. **Generate GitHub Token**: Create a Personal Access Token at https://github.com/settings/tokens
2. **Configure Token**: Add your token in the plugin settings
3. **Validate Token**: Use the "Validate Token" button to test your token
4. **Check Updates**: Use the "Check for Updates" button to manually check for updates

#### Requirements
- GitHub Personal Access Token with `repo` scope for private repositories
- Valid GitHub repository with releases/tags
- WordPress update system enabled

## Development

### Adding New Features

1. **Follow the existing structure** and add your code to the appropriate module
2. **Use the Loader class** to register hooks and filters
3. **Follow WordPress coding standards** and use proper escaping
4. **Add tests** for new functionality
5. **Update documentation** as needed

### Testing

The plugin includes PHPUnit tests. To run them:

```bash
composer test
```

### Code Quality

The plugin uses PHP_CodeSniffer with WordPress coding standards:

```bash
# Check code quality
composer phpcs

# Auto-fix issues
composer phpcbf
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Ensure all tests pass
6. Submit a pull request

## License

This plugin is licensed under the GPL v2 or later.

## Support

For support and questions:

- Create an issue on GitHub
- Check the documentation
- Review the code examples

## Changelog

### 1.0.0
- Initial release
- PSR-4 autoloading with Composer
- Modular OOP architecture
- Admin and public interfaces
- Settings management
- Internationalization support
- GitHub update functionality
- Activation and deactivation hooks
- Plugin row "Check for Updates" integration (like ACF Pro)
- Real-time update checking with loading states 