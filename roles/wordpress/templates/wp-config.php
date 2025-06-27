<?php
/**
 * La configuration de base de WordPress
 *
 * Le script de création de wp-config.php utilise ce fichier pendant
 * l'installation. Vous n'avez pas besoin d'utiliser le site web, vous pouvez
 * copier ce fichier vers "wp-config.php" et renseigner les valeurs.
 *
 * Ce fichier contient les configurations suivantes :
 *
 * * Paramètres MySQL
 * * Clés secrètes
 * * Préfixe des tables de la base de données
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** Paramètres MySQL - Vous pouvez obtenir ces informations auprès de votre hébergeur web ** //
/** Nom de la base de données pour WordPress */
define( 'DB_NAME', '{{ wordpress_db_name }}' );

/** Nom d'utilisateur de la base de données MySQL */
define( 'DB_USER', '{{ wordpress_db_user }}' );

/** Mot de passe de la base de données MySQL */
define( 'DB_PASSWORD', '{{ wordpress_db_password }}' );

/** Nom d'hôte MySQL */
define( 'DB_HOST', '{{ wordpress_db_host }}' );

/** Jeu de caractères de la base de données à utiliser lors de la création des tables de la base de données. */
define( 'DB_CHARSET', 'utf8' );

/** Le type de collation de la base de données. Ne le modifiez pas en cas de doute. */
define( 'DB_COLLATE', '' );

{% set salts = ['AUTH_KEY', 'SECURE_AUTH_KEY', 'LOGGED_IN_KEY', 'NONCE_KEY', 'AUTH_SALT', 'SECURE_AUTH_SALT', 'LOGGED_IN_SALT', 'NONCE_SALT'] %}
{% for salt in salts %}
define('{{ salt }}', '{{ lookup('community.general.random_string', length=64, special=True) }}');
{% endfor %}

/**
 * Préfixe des tables de la base de données WordPress.
 *
 * Vous pouvez avoir plusieurs installations dans une base de données si vous donnez à chacune
 * un préfixe unique. N'utilisez que des chiffres, des lettres et des underscores !
 */
$table_prefix = 'wp_';

/**
 * Pour les développeurs : mode de débogage de WordPress.
 *
 * Changez ceci à true pour activer l'affichage des notifications pendant le développement.
 * Il est fortement recommandé que les développeurs de plugins et de thèmes utilisent WP_DEBUG
 * dans leurs environnements de développement.
 *
 * Pour des informations sur les autres constantes pouvant être utilisées pour le débogage,
 * visitez le Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* C'est tout, arrêtez d'éditer ! Bon blogging. */

/** Chemin absolu vers le répertoire WordPress. */
if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Configure les variables WordPress et les fichiers inclus. */
require_once( ABSPATH . 'wp-settings.php' );
