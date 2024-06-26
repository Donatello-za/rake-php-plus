<?php

namespace DonatelloZa\RakePlus;

use RakePlusTest;

function extension_loaded($name): bool
{
    if ($name === 'mbstring') {
        return RakePlusTest::$mb_support;
    }
    return \extension_loaded($name);
}

