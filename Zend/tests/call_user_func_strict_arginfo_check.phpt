--TEST--
Arginfo / zpp consistency check for call_user_func() in strict context
--FILE--
<?php
declare(strict_types=1);

namespace Foo;

// strlen() will be called with strict_types=0, so this is legal.
var_dump(call_user_func('strlen', false));

?>
--EXPECTF--
Deprecated: Implicit bool to string coercion is deprecated in %s on line %d
int(0)
