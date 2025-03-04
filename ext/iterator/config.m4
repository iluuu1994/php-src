PHP_NEW_EXTENSION([iterator], m4_normalize([
    iterator.c
  ]),
  [no],,
  [-DZEND_ENABLE_STATIC_TSRMLS_CACHE=1])
PHP_INSTALL_HEADERS([ext/iterator], m4_normalize([
  php_iterator.h
]))
