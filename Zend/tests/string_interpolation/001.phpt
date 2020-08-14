--TEST--
Basic string interpolation
--FILE--
<?php

class Foo {
    public static function bar()
    {
        return 'Foo::bar';
    }
}

$foo = 'foo';

// Ignored in simple strings
echo '#{Foo::bar()}' . "\n";

// Ignored in simple heredoc
echo <<<'TEXT'
#{Foo::bar()}

TEXT;

// Double quoted strings
echo "#{Foo::bar()}\n";

// Heredoc
echo <<<TEXT
#{Foo::bar()}

TEXT;

// Nested strings
// FIXME: Direct embedding of strings doesn't work
echo "#{(string) "Nested string"}\n";

// Escaping
echo "\#\n";
echo "\#{}\n";
echo "\#{Foo::bar()}\n";
echo "#\{$foo}\n";

?>
--EXPECT--
#{Foo::bar()}
#{Foo::bar()}
Foo::bar
Foo::bar
Nested string
\#
#{}
#{Foo::bar()}
#\{foo}
