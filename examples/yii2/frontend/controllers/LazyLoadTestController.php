<?php

namespace yii2\frontend\controllers;

use Yii;
use andy87\lazy_load\yii2\LazyLoadTrait;
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
 * @property-read NextComponent $_nextComponent
 *
 * @package yii2\controllers
 */
class LazyLoadTestController extends \yii\web\Controller
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
     * @url http://127.0.0.1/lazy-load-test?a=a
     * @url http://127.0.0.1/lazy-load-test?a=b
     * @url http://127.0.0.1/lazy-load-test?a=c
     * @url http://127.0.0.1/lazy-load-test?a=d
     *
     * @return string
     */
    public function actionIndex(): string
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

        return  $message;
    }
}