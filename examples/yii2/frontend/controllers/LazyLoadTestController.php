<?php

namespace frontend\controllers;

use Yii;
use andy87\lazy_load\yii2\LazyLoadTrait;
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
class LazyLoadTestController extends \yii\web\Controller
{
    use LazyLoadTrait;

    public array $lazyLoadConfig = [
        'someComponent' => SomeComponent::class,
        'otherComponent' => [
            'class' => OtherComponent::class,
            'public_property' => 'public_property'
        ],
        'thirdComponent' => [
            'class' => ThirdComponent::class,
            ['construct_argument_1','construct_argument_2']
        ],
    ];

    /**
     * @url http://127.0.0.1/lazy-load-test
     * @url http://127.0.0.1/lazy-load-test?a=a
     * @url http://127.0.0.1/lazy-load-test?a=b
     *
     * @return string
     */
    public function actionIndex(): string
    {
        $get = Yii::$app->request->get('a');

        switch ($get) {

            case 'a' :
                $text = $this->otherComponent->method();
                $text .= ( "<br> public_property = " . $this->otherComponent->public_property );
                break;

            case 'b' :
                $text = $this->thirdComponent->method();

                $text .= ( "<br> construct_argument_1 = " . $this->thirdComponent->construct_argument_1 );
                $text .= ( "<br> construct_argument_2 = " . $this->thirdComponent->construct_argument_2 );
                break;

            default :
                $text = $this->someComponent->method();
                break;
        }

        return $text;
    }
}