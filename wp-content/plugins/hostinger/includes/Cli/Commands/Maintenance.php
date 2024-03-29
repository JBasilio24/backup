<?php

namespace Hostinger\Cli\Commands;

use WP_CLI;
use Hostinger\Settings;

defined( 'ABSPATH' ) || exit;

class Maintenance {

	public static function define_command(): void {
		if ( class_exists( '\WP_CLI' ) ) {
			WP_CLI::add_command( 'hostinger maintenance', self::class );
		}
	}

	/**
	 * Command allows enable/disable maintenance mode.
	 *
	 * ## EXAMPLES
	 *
	 *     # Enable maintenance mode
	 *     $ wp hostinger maintenance mode 1
	 *
	 *     # Disable maintenance mode
	 *     $ wp hostinger maintenance mode 0
	 *
	 * @param array $args WP-CLI positional arguments.
	 *
	 * @throws \Exception If pass bad argument.
	 */
	public function mode( array $args ): void {
		if ( empty( $args ) ) {
			WP_CLI::error( 'Arguments cannot be empty. Use 0 or 1' );
		}

		if ( has_action( 'litespeed_purge_all' ) ) {
			do_action( 'litespeed_purge_all' );
		}

		switch ( $args[0] ) {
			case '1':
				Settings::update_setting( 'maintenance_mode', 1 );
				WP_CLI::success( 'Maintenance mode ENABLED' );
				break;
			case '0':
				Settings::update_setting( 'maintenance_mode', 0 );
				WP_CLI::success( 'Maintenance mode DISABLED' );
				break;
			default:
				throw new \Exception( 'Invalid maintenance mode value' );
		}
	}

	/**
	 * Command return maintenance mode status.
	 *
	 * ## EXAMPLES
	 *
	 *     # Get maintenance mode status
	 *     $ wp hostinger maintenance status
	 */
	public function status(): bool {
		$status = get_option( 'hostinger_maintenance_mode', 0 );

		if ( $status ) {
			WP_CLI::success( 'Maintenance mode ENABLED' );
		} else {
			WP_CLI::success( 'Maintenance mode DISABLED' );
		}

		return (bool) $status;
	}
}
