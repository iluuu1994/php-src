--TEST--
GH-16649: array_splice UAF with multiple destructors
--FILE--
<?php
class MultiDestructor {
    public $id;

    function __construct($id) {
        $this->id = $id;
    }

    function __destruct() {
        global $arr;
        echo "Destructor {$this->id} called\n";
        if ($this->id == 2) {
            $arr = null;
        }
    }
}

$arr = ["start", new MultiDestructor(1), new MultiDestructor(2), "end"];

array_splice($arr, 1, 2);
?>
--EXPECT--
Destructor 1 called
Destructor 2 called
