PHP_ARG_ENABLE(hls, [whether to enable hls support],
[  --enable-hls           Enable hls support], yes, yes)
if test "$PHP_HLS" != "no"; then
    AC_DEFINE(HAVE_HLS, 1, [Whether you have HLS])
    PHP_NEW_EXTENSION(hls, php_hls.c codec.c rsCodec.c, $ext_shared)
fi
if test -z "$PHP_DEBUG"; then 
	AC_ARG_ENABLE(debug,
	[	--enable-debug			compile with debugging symbols],[
		PHP_DEBUG=$enableval
	],[	PHP_DEBUG=no
	])
fi
