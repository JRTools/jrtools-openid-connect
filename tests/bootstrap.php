<?php
/**
 * PHPUnit Test Bootstrap für den OIDC Client.
 *
 * Lädt Composer-Autoloader und definiert WP-Stubs, die vom Plugin-Code
 * benötigt werden, ohne eine echte WordPress-Instanz aufzusetzen.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Brain\Monkey;

// Mindest-WordPress-Konstanten
if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/../' );
}
if ( ! defined( 'AUTH_KEY' ) ) {
    define( 'AUTH_KEY', 'test-auth-key-for-unit-tests-only' );
}
if ( ! defined( 'SECURE_AUTH_KEY' ) ) {
    define( 'SECURE_AUTH_KEY', 'test-secure-auth-key-for-unit-tests-only' );
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

// WP_Error Stub
if ( ! class_exists( 'WP_Error' ) ) {
    class WP_Error {
        public $code;
        public $message;
        public $data;

        public function __construct( $code = '', $message = '', $data = '' ) {
            $this->code    = $code;
            $this->message = $message;
            $this->data    = $data;
        }

        public function get_error_message( $code = '' ) {
            return $this->message;
        }

        public function get_error_code() {
            return $this->code;
        }
    }
}

// WP_User Stub
if ( ! class_exists( 'WP_User' ) ) {
    class WP_User {
        public $ID   = 0;
        public $data;
        public $roles = array();
        private $set_role_calls  = array();
        private $add_role_calls  = array();

        public function set_role( $role ) {
            $this->set_role_calls[] = $role;
        }

        public function add_role( $role ) {
            $this->add_role_calls[] = $role;
        }

        public function get_set_role_calls() {
            return $this->set_role_calls;
        }

        public function get_add_role_calls() {
            return $this->add_role_calls;
        }
    }
}

// Plugin-Klassen laden (ohne ABSPATH-Check, da wir ihn oben definiert haben)
require_once __DIR__ . '/../includes/class-oidc-jwt-helper.php';
require_once __DIR__ . '/../includes/class-oidc-tokens.php';
require_once __DIR__ . '/../includes/class-oidc-roles.php';
