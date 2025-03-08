<?php

namespace examples\yii2;

use andy87\lazy_load\LazyLoadTrait;

use frontend\components\lazyLoadTest\OtherComponent;
use frontend\components\lazyLoadTest\SomeComponent;
use frontend\components\lazyLoadTest\ThirdComponent;
use frontend\components\lazyLoadTest\NextComponent;

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
            'public_property' => 'public_property OtherComponent'
        ],
        '_thirdComponent' => [
            'class' => [ ThirdComponent::class, ['construct_argument_1_ThirdComponent', 'construct_argument_2_ThirdComponent'] ],
        ],
        '_nextComponent' => [
            'class' => [ NextComponent::class, ['construct_argument_1_NextComponent', 'construct_argument_2_NextComponent'] ],
            'public_property' => 'public_property NextComponent'
        ],
    ];



    /**
     * @return string
     */
    public function view(): string
    {
        $message = '';

        $message .= $this->someComponent->method();
        $message .= $this->otherComponent->method();
        $message .= $this->_thirdComponent->method();
        $message .= $this->_nextComponent->method();

        if (isset($_GET['a']))
        {
            $message .= match ($_GET['a']) {
                'b' => $this->otherComponent->method(),
                'c' => $this->_thirdComponent->method(),
                'd' => $this->_nextComponent->method(),
                default => $this->someComponent->method(),
            };
        }

        return $message;
    }
}



$lazyLoadTest = new LazyLoadTest();

echo $lazyLoadTest->view();