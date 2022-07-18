<?php

namespace DgoraWcas\Integrations\Themes\TheGemElementor;

use DgoraWcas\Abstracts\ThemeIntegration;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TheGemElementor extends ThemeIntegration {
	public function __construct() {
		$this->themeSlug = 'thegem-elementor';
		$this->themeName = 'TheGem (Elementor)';

		parent::__construct();
	}
}
