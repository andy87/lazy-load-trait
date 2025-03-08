<?php

namespace examples\yii2;

use andy87\lazy_load\LazyLoadTrait;

use frontend\components\lazyLoadTest\OtherComponent;
use frontend\components\lazyLoadTest\SomeComponent;
use frontend\components\lazyLoadTest\ThirdComponent;

/**
 * SomeController
 *
 * @property-read SomeComponent $someComponent
 * @property-read OtherComponent $otherComponent
 * @property-read ThirdComponent $thirdComponent
 *
 * @package yii2\controllers
 */
class LazyLoadTest
{
    use LazyLoadTrait;


    public array $lazyLoadConfig = [
        'someComponent' => SomeComponent::class,
        'otherComponent' => [
            'class' => OtherComponent::class,
            'public_property' => 'value'
        ],
        'thirdComponent' => [
            'class' => ThirdComponent::class,
            ['construct_argument_1', 'construct_argument_2']
        ],
    ];



    /**
     * @url http://127.0.0.1/index.php
     * @url http://127.0.0.1/index.php?a=a
     * @url http://127.0.0.1/index.php?a=b
     *
     * @return string
     */
    public function view(): string
    {
        // Apply LazyLoad
        $message = $this->someComponent->method();

        if ($_GET['a'] = 'a') {
            return $this->otherComponent->method();
        }

        if ($_GET['b'] = 'b') {
            $message = $this->thirdComponent->method();
        }

        return $message;
    }
}



$lazyLoadTest = new LazyLoadTest();

echo $lazyLoadTest->view();