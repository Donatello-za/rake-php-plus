<?php

namespace DonatelloZa\RakePlus;

function extension_loaded($name)
{
    if ($name === 'mbstring') {
        return RakePlusTest::$mb_support;
    }
    return \extension_loaded($name);
}

