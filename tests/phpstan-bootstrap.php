<?php
/**
 * PHPStan Bootstrap – WP-Stubs ohne Brain\Monkey.
 *
 * @package   OIDC_Client
 * @copyright 2026 Johannes Rösch
 * @license   GPL-2.0-or-later
 */

if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/../' );
}
if ( ! defined( 'AUTH_KEY' ) ) {
    define( 'AUTH_KEY', 'phpstan-stub' );
}
if ( ! defined( 'SECURE_AUTH_KEY' ) ) {
    define( 'SECURE_AUTH_KEY', 'phpstan-stub' );
}
if ( ! defined( 'HOUR_IN_SECONDS' ) ) {
    define( 'HOUR_IN_SECONDS', 3600 );
}
if ( ! defined( 'MINUTE_IN_SECONDS' ) ) {
    define( 'MINUTE_IN_SECONDS', 60 );
}
if ( ! defined( 'DAY_IN_SECONDS' ) ) {
    define( 'DAY_IN_SECONDS', 86400 );
}
if ( ! defined( 'OPENSSL_RAW_DATA' ) ) {
    define( 'OPENSSL_RAW_DATA', 1 );
}
if ( ! defined( 'OPENSSL_ALGO_SHA256' ) ) {
    define( 'OPENSSL_ALGO_SHA256', 7 );
}
if ( ! defined( 'OIDC_CLIENT_VERSION' ) ) {
    define( 'OIDC_CLIENT_VERSION', '1.0.0' );
}
if ( ! defined( 'OIDC_CLIENT_DIR' ) ) {
    define( 'OIDC_CLIENT_DIR', __DIR__ . '/../' );
}
if ( ! defined( 'OIDC_CLIENT_URL' ) ) {
    define( 'OIDC_CLIENT_URL', 'https://example.com/wp-content/plugins/oidc-client/' );
}

if ( ! class_exists( 'WP_Error' ) ) {
    class WP_Error {
        public string $code    = '';
        public string $message = '';

        public function __construct( string $code = '', string $message = '', mixed $_data = '' ) {
            $this->code    = $code;
            $this->message = $message;
        }

        public function get_error_message( string $_code = '' ): string {
            return $this->message;
        }

        public function get_error_code(): string {
            return $this->code;
        }
    }
}

if ( ! class_exists( 'WP_User' ) ) {
    class WP_User {
        public int    $ID    = 0;
        public array  $roles = array();
        public ?object $data  = null;

        public function set_role( string $_role ): void { /* Stub – keine Implementierung nötig */ }
        public function add_role( string $_role ): void { /* Stub – keine Implementierung nötig */ }
    }
}

if ( ! class_exists( 'WP_REST_Request' ) ) {
    class WP_REST_Request {
        public function get_param( string $_key ): mixed { return null; }
        public function get_params(): array { return array(); }
    }
}

if ( ! class_exists( 'WP_REST_Response' ) ) {
    class WP_REST_Response {
        public function __construct( mixed $_data = null, int $_status = 200 ) { /* Stub – keine Implementierung nötig */ }
    }
}
