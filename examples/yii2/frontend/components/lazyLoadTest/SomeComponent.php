<?php

namespace frontend\components\lazyLoadTest;

/**
 * < Frontend > `SomeComponent`
 *
 * @package yii2\frontend\components\test
 */
class SomeComponent
{
    public function __construct()
    {
        echo '<br><br> <b>construct</b> ' . __METHOD__;
    }

    public function method()
    {
        return "<br><br>" . __METHOD__;
    }
}