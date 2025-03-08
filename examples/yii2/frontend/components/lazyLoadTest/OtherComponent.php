<?php

namespace frontend\components\lazyLoadTest;

/**
 * < Frontend > `OtherComponent`
 *
 * @package yii2\frontend\components\test
 */
class OtherComponent
{
    public string $public_property;

    public function __construct()
    {
        echo '<br><br> <b>construct</b> ' . __METHOD__;
    }

    public function method()
    {
        return "<br><br>" . __METHOD__;
    }
}