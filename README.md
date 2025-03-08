Вот такая реализация пришла в голову.

Инициализация свойства класса только в момент вызова(обращения к ним).
P.S. Знаю что в PHP c версии 8.4 появилась поддержка lazyLoad из коробки, но это ещё не завезли в Yii2.

Установка.

Composer:
```bash
composer require andy87/yii2-lazy-load-trait
```

Пример использования:
1. указать свойство в аннотации класса
2. подключить трейт
3. указать конфигурацию в свойстве $lazyLoadConfig
4. обращаться к свойствам как к обычным свойствам класса



```php
<?php

use andy87\yii2\lazy_load\LazyLoadTrait;

/**
 * examples\yii2\frontend\controllers\SomeController
 *
 * @property-read SomeComponent $someComponent
 * @property-read OtherComponent $otherComponent
 * @property-read ThirdComponent $thirdComponent
 * 
 * @package yii2\controllers
 */
class examples\yii2\frontend\controllers\SomeController extends \yii\web\Controller
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
```


Дополнительные

Home: https://github.com/andy87/yii2-lazy-load-trait