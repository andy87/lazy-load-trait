<?php

namespace frontend\components\lazyLoadTest;

use yii\base\BaseObject;

/**
 * < Frontend > `ThirdComponent`
 *
 * @package yii2\frontend\components\test
 */
class ThirdComponent extends BaseObject
{
    public string $construct_argument_1;
    public string $construct_argument_2;


    public function __construct( string $construct_argument_1, string $construct_argument_2, array $config = [] )
    {
        $this->construct_argument_1 = $construct_argument_1;
        $this->construct_argument_2 = $construct_argument_2;

        parent::__construct($config);
    }

    public function method()
    {
        return __METHOD__;
    }
}