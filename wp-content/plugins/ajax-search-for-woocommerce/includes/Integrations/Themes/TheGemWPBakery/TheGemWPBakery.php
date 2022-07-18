<?php

namespace DgoraWcas\Integrations\Themes\TheGemWPBakery;

use DgoraWcas\Abstracts\ThemeIntegration;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TheGemWPBakery extends ThemeIntegration {
	public function __construct() {
		$this->themeSlug = 'thegem-wpbakery';
		$this->themeName = 'TheGem (WPBakery)';

		parent::__construct();
	}

	/**
	 * Overwrite search
	 *
	 * @return void
	 */
	protected function maybeOverwriteSearch() {
		$partialPath = DGWT_WCAS_DIR . 'partials/themes/thegem-elementor.php';
		if ( $this->canReplaceSearch() && file_exists( $partialPath ) ) {
			require_once( $partialPath );
		}
	}
}
