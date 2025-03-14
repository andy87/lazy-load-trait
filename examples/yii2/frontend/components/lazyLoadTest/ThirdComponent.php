<?php

namespace frontend\components\lazyLoadTest;
/**
 * < Frontend > `ThirdComponent`
 *
 * @package yii2\frontend\components\test
 */
class ThirdComponent
{
    public string $construct_argument_1;
    public string $construct_argument_2;


    public function __construct( string $construct_argument_1, string $construct_argument_2)
    {
        echo '<br><br> <b>construct</b> ' . __METHOD__;

        $this->construct_argument_1 = $construct_argument_1;
        $this->construct_argument_2 = $construct_argument_2;
    }

    public function method()
    {
        return "<br><br>" . __METHOD__
            . "<br> \$this->construct_argument_1 = $this->construct_argument_1"
            . "<br> \$this->construct_argument_2 = $this->construct_argument_2";
    }
}