<?php

use andy87\yii2\lazyLoadTrait\LazyLoadTrait;

/**
 * SomeController
 *
 * @property-read SomeComponent $someComponent
 * @property-read OtherComponent $otherComponent
 * @property-read ThirdComponent $thirdComponent
 *
 * @package yii2\controllers
 */
class SomeController extends \yii\web\Controller
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
            ['construct_argument_1','construct_argument_2']
        ],
    ];



    /**
     * @url http://domain.name/some/view
     *
     * @return Response|string
     */
    public function actionView(): Response|string
    {
        // Apply LazyLoad
        $text = $this->someComponent->insideSomeComponent->test();

        if (Yii::$app->request->isPost) {
            return $this->otherComponent->someMethod();
        }

        if (Yii::$app->request->isAjax) {
            $text = $this->thirdComponent->mextMethod();
        }

        return $this->render('view', ['text' => $text]);
    }
}