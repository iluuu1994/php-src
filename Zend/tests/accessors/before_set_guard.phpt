--TEST--
A beforeSet hook may write to its own property.
--FILE--
<?php

class C {
    public $prop {
        beforeSet {
            $this->prop = 'assigned from beforeSet';
            throw new Exception('Stop');
        }
    }
}

$c = new C();
try {
    $c->prop = 'foo';
} catch (\Exception $e) {
    echo $e->getMessage(), "\n";
}
echo $c->prop, "\n";

?>
--EXPECT--
Stop
assigned from beforeSet
