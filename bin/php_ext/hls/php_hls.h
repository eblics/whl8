#ifndef _PHP_HLS_H
	#define _PHP_HLS_H 1
	#ifdef HAVE_CONFIG_H
	    #include "config.h"
	#endif

	#ifdef ZTS
	#include "TSRM.h"
	#endif
	#include <php.h>
	#include <php_ini.h>

	#define PHP_HLS_VERSION "0.1"

	PHP_FUNCTION(hls_encode);
	PHP_FUNCTION(hls_decode);
	PHP_FUNCTION(hls_batch);

	//PHP_MINIT_FUNCTION(hls);
	//PHP_MSHUTDOWN_FUNCTION(hls);

	//extern zend_module_entry hls_module_entry;
#endif
