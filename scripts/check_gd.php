<?php
if (function_exists('gd_info')) {
    print_r(gd_info());
} else {
    echo "no gd\n";
}
