<?php

namespace DgoraWcas\Integrations\Themes\Kadence;

use DgoraWcas\Abstracts\ThemeIntegration;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kadence extends ThemeIntegration {

	public function __construct() {
		$this->themeSlug = 'kadence';
		$this->themeName = 'Kadence';

		parent::__construct();
	}
}
