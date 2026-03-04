<?php

namespace Ns {
    function strlen(string $str): string {
        return "shadow:" . \strlen($str);
    }
}
