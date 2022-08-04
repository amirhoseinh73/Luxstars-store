<?php

function random_verficiraion_code( $length = 8 ) {
	return substr( str_shuffle( "1234567890" ), 0, $length );
}