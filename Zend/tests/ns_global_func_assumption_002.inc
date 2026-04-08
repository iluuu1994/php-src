<?php

namespace Ns {
    function test() {
        // Unqualified strlen() should be compiled as \strlen() initially
        return strlen("hello");
    }
}
