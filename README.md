Вот такая реализация пришла в голову.

Инициализация свойства класса только в момент вызова(обращения к ним).
P.S. Знаю что в PHP c версии 8.4 появилась поддержка lazyLoad из коробки, но это ещё не завезли в Yii2.

Установка.

Composer:
```bash
composer require andy87/yii2-lazy-load-trait
```

## Порядок дейсвий для использования:
### указать свойство в аннотации класса
```php
/**
 * SomeClass
 *
 * @property-read SomeComponent $someComponent // native
 * @property-read OtherComponent $_otherComponent // singleton
 * 
 * @package yii2\controllers
 */
class SomeClass
{
    //...
}
```

### подключить трейт

для подключения трейта в классе имеется 2 версии Trait'ов:
* `andy87\lazy_load\yii2\LazyLoadTrait` - для использования в фреймворке Yii2 с применением метода `Yii::createObject()`
* `andy87\lazy_load\LazyLoadTrait` - для использования вне фреймворка Yii2

### указать конфигурацию в свойстве $lazyLoadConfig

Структура конфигурации.
* для использования свойства как экземпляр класса (без настроек), доступно 2 варианта:
```php
    $lazyLoadConfig = [
        'someComponent' => SomeComponent::class, // быстрый способ ( меньше проверок )
        'otherComponent' => [
            'class' => OtherComponent::class,  // способ поедленней ( больше проверок )
        ],
    ]
```

* с назначением публичных свойств класса
```php
    $lazyLoadConfig = [
      'otherComponent' => [
            'class' => OtherComponent::class,
            'public_property_1' => 'value_1',
            'public_property_2' => 'value_2',
        ],
    ]
```

* с передачей параметров в аргументы функции `__construct()` 
```php
    $lazyLoadConfig = [
       'thirdComponent' => [
            'class' => [ ThirdComponent::class, ['construct_argument_1', 'construct_argument_2'] ],
        ],
    ]
```
* комбинирование назначения публичных свойств и передача параметров в аргументы функции `__construct()`
```php
    $lazyLoadConfig = [
         'nextComponent' => [
            'class' => [ NextComponent::class, ['construct_argument_1', 'construct_argument_2'] ],
            'public_property_1' => 'value_1',
            'public_property_2' => 'value_2',
        ],
    ]
```
* добавление экзщемпляра в `cache` с последующим использованием закешированой версии
добавление префиеа - нижнее подчеркивание `_`
```php
    $lazyLoadConfig = [
        '_nextComponent' => [ // данное своство при первом обращении будет закешировано, и при последующих обращениях будет использоваться закешированная версия
            'class' => [ NextComponent::class, ['construct_argument_1', 'construct_argument_2'] ],
            'public_property_1' => 'value_1',
            'public_property_2' => 'value_2',
        ],
    ]
```


### обращаться к свойствам как к обычным свойствам класса
```php
<?php

namespace some\path;

use andy87\lazy_load\LazyLoadTrait;

/**
 * SomeClass
 *
 * @property-read SomeComponent $someComponent
 * @property-read OtherComponent $otherComponent
 * @property-read ThirdComponent $thirdComponent
 * 
 * @package yii2\controllers
 */
class SomeClass
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
Home: https://github.com/andy87/lazy-load-trait