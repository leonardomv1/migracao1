<?php
/** 
 * As configurações básicas do WordPress.
 *
 * Esse arquivo contém as seguintes configurações: configurações de MySQL, Prefixo de Tabelas,
 * Chaves secretas, Idioma do WordPress, e ABSPATH. Você pode encontrar mais informações
 * visitando {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. Você pode obter as configurações de MySQL de seu servidor de hospedagem.
 *
 * Esse arquivo é usado pelo script ed criação wp-config.php durante a
 * instalação. Você não precisa usar o site, você pode apenas salvar esse arquivo
 * como "wp-config.php" e preencher os valores.
 *
 * @package WordPress
 */

// ** Configurações do MySQL - Você pode pegar essas informações com o serviço de hospedagem ** //
/** O nome do banco de dados do WordPress */
define('DB_NAME', 'wordpresscm');

/** Usuário do banco de dados MySQL */
define('DB_USER', 'usuariocm');

/** Senha do banco de dados MySQL */
define('DB_PASSWORD', 'q1w2e3r4t5');

/** nome do host do MySQL */
define('DB_HOST', 'mysql796.umbler.com');

/** Conjunto de caracteres do banco de dados a ser usado na criação das tabelas. */
define('DB_CHARSET', 'utf8mb4');

/** O tipo de collate do banco de dados. Não altere isso se tiver dúvidas. */
define('DB_COLLATE', '');

/**#@+
 * Chaves únicas de autenticação e salts.
 *
 * Altere cada chave para um frase única!
 * Você pode gerá-las usando o {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * Você pode alterá-las a qualquer momento para desvalidar quaisquer cookies existentes. Isto irá forçar todos os usuários a fazerem login novamente.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '1{rV:Vl<5G,!cFe%O:DL>`4t}bi*T(<z;(6QZd>HG/FQYQmp-hUQ^mZy]jzQz8Ts');
define('SECURE_AUTH_KEY',  'g *b.s@y#FAQ<+r95:$j0@zf5iay]]P2B?pX@l?)>O;VGKD>,87!iePVW-}-rfF-');
define('LOGGED_IN_KEY',    ')`=V~$~=6` uQILNW{2qtc1aqP&SX[F.TF`WB`%<>Byg8z<O4yGN!yspC4?WK`:A');
define('NONCE_KEY',        '_2ee1-cB[)?!E4frzi6vJQ:[Z>XnPaR2iA:{g0t!fjzr%:Nrx~e>X;)|34.LIeyY');
define('AUTH_SALT',        'iJ;8+y(1=c43L,1-+oQM1s,!y3X LaeU/2PGZMv#wGNTu],t_&Sx#jB-30uQmDM~');
define('SECURE_AUTH_SALT', '9Ye~ ED|AYR]Ix|p*%^CJ%0ApTCP&U=dO_~Qus$Hm@1A],-O9u>+ c&g>t)@Mcl@');
define('LOGGED_IN_SALT',   'YMp7/8MxPYGu#K8QvOr)jfoy@sbI4)IusCWVr9eygWDWh([B+4|G.>T$T(VbX|hl');
define('NONCE_SALT',       'I2/}]Mxbri-U#Lj@b?d*~Z!!9Xj0yh;:I&vqIBDR5=-_4;)O9NiYcZEe@|fXXtf*');

/**#@-*/

/**
 * Prefixo da tabela do banco de dados do WordPress.
 *
 * Você pode ter várias instalações em um único banco de dados se você der para cada um um único
 * prefixo. Somente números, letras e sublinhados!
 */
$table_prefix  = 'wp_';


/**
 * Para desenvolvedores: Modo debugging WordPress.
 *
 * altere isto para true para ativar a exibição de avisos durante o desenvolvimento.
 * é altamente recomendável que os desenvolvedores de plugins e temas usem o WP_DEBUG
 * em seus ambientes de desenvolvimento.
 */
define('WP_DEBUG', false);

/* Isto é tudo, pode parar de editar! :) */

/** Caminho absoluto para o diretório WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');
	
/** Configura as variáveis do WordPress e arquivos inclusos. */
require_once(ABSPATH . 'wp-settings.php');
