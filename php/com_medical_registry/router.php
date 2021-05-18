<?php

// No direct access
defined( '_JEXEC' ) or die;

function Medical_RegistryBuildRoute( &$query )
{
	$segments = array();
	if ( isset( $query['view'] ) ) {
		$segments[] = $query['view'];
		unset( $query['view'] );
	}
	if ( isset( $query['task'] ) ) {
		$segments[] = $query['task'];
		unset( $query['task'] );
	}
	if ( isset( $query['id'] ) ) {
		$segments[] = $query['id'];
		unset( $query['id'] );
	}
	return $segments;
}

function Medical_RegistryParseRoute( $segments )
{
	$vars = array();
	$count = count( $segments );
	if ( $count == 1 ) {
		$vars['view'] = $segments[0];
	}
	if ( $count == 2 ) {
		$vars['view'] = $segments[0];
		if ( strpos( $segments[1], ':' ) !== false || is_numeric( $segments[1] ) ) {
			$vars['id'] = $segments[1];
		} else {
			$vars['task'] = $segments[1];
		}
	}
	if ( $count == 3 ) {
		$vars['view'] = $segments[0];
		$vars['task'] = $segments[1];
		$vars['id'] = $segments[2];
	}
	return $vars;
}