--TEST--
Exception during break 2 with multiple try/catch
--XFAIL--
FIXME / irrelevant?
--FILE--
<?php

class A {
    public $a = 1;
    public $b = 2;

    public function __destruct() {
        throw new Exception(__METHOD__);
    }
}

function foo() {
    foreach ([0] as $_) {
        try {
            foreach (new A as $value) {
                try {
                    break 2;
                } catch (Exception $e) {
                    echo "catch1\n";
                } finally {
                    echo "finally1\n";
                }
            }
            (function() {})();
        } catch (Exception $e) {
            echo "catch2\n";
        } finally {
            echo "finally2\n";
        }
    }
}

foo();
?>
--EXPECT--
finally1
catch2
finally2
