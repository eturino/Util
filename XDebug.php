<?php

class EtuDev_Util_XDebug {

	function setVarDumpNormalDepth() {
		ini_set('xdebug.var_display_max_depth', 3);
	}

	function setVarDumpHighDepth() {
		ini_set('xdebug.var_display_max_depth', 5);
		ini_set('xdebug.var_display_max_data', 10240);
		ini_set('xdebug.var_display_max_children', 10240);
	}

	function setVarDumpHigherDepth() {
		ini_set('xdebug.var_display_max_depth', 9);
		ini_set('xdebug.var_display_max_data', 10240);
		ini_set('xdebug.var_display_max_children', 10240);
	}

	function setVarDumpHighestDepth() {
		ini_set('xdebug.var_display_max_depth', 15);
		ini_set('xdebug.var_display_max_data', 10240);
		ini_set('xdebug.var_display_max_children', 10240);
	}

	function setVarDumpDepth($depth = 3) {
		ini_set('xdebug.var_display_max_depth', $depth);
	}

}