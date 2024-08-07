--TEST--
strftime() and gmstrftime() tests
--SKIPIF--
<?php
if (substr(PHP_OS, 0, 3) == 'WIN') die('skip posix only test.');
?>
--FILE--
<?php
date_default_timezone_set('Asia/Jerusalem');

$t = mktime(0,0,0, 6, 27, 2006);

var_dump(strftime(""));
var_dump(strftime("%a %A %b %B %c %C %d %D %e %g %G %h %H %I %j %m %M %n %p %r %R %S %t %T %u %U %V %W %w %x %X %y %Y %Z %z %%", $t));
var_dump(strftime("%%q %%a", $t));
var_dump(strftime("blah", $t));

var_dump(gmstrftime(""));
var_dump(gmstrftime("%a %A %b %B %c %C %d %D %e %g %G %h %H %I %j %m %M %n %p %r %R %S %t %T %u %U %V %W %w %x %X %y %Y %Z %z %%", $t));
var_dump(gmstrftime("%%q %%a", $t));
var_dump(gmstrftime("blah", $t));

echo "Done\n";
?>
--EXPECTF--
Deprecated: Function strftime() is deprecated since 8.1, use IntlDateFormatter::format() instead in %s on line %d
bool(false)

Deprecated: Function strftime() is deprecated since 8.1, use IntlDateFormatter::format() instead in %s on line %d
string(%d) "Tue Tuesday Jun June Tue Jun 27 00:00:00 2006 %s
%s %"

Deprecated: Function strftime() is deprecated since 8.1, use IntlDateFormatter::format() instead in %s on line %d
string(5) "%q %a"

Deprecated: Function strftime() is deprecated since 8.1, use IntlDateFormatter::format() instead in %s on line %d
string(4) "blah"

Deprecated: Function gmstrftime() is deprecated since 8.1, use IntlDateFormatter::format() instead in %s on line %d
bool(false)

Deprecated: Function gmstrftime() is deprecated since 8.1, use IntlDateFormatter::format() instead in %s on line %d
string(%d) "Mon Monday Jun June Mon Jun 26 21:00:00 2006 %s
%s %"

Deprecated: Function gmstrftime() is deprecated since 8.1, use IntlDateFormatter::format() instead in %s on line %d
string(5) "%q %a"

Deprecated: Function gmstrftime() is deprecated since 8.1, use IntlDateFormatter::format() instead in %s on line %d
string(4) "blah"
Done
