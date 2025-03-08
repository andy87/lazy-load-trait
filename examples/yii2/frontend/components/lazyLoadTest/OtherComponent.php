<?php

namespace frontend\components\lazyLoadTest;

use yii\base\BaseObject;

/**
 * < Frontend > `OtherComponent`
 *
 * @package yii2\frontend\components\test
 */
class OtherComponent extends BaseObject
{
    public string $public_property;

    public function method()
    {
        return __METHOD__;
    }
}