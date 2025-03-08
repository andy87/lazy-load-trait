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
* добавление объекта в `cache` с последующим переиспользованием закешированой версии
__добавление к имени своства префикса(нижнее подчеркивание `_`)__
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


    /** @var array  */
    public array $lazyLoadConfig = [
        'someComponent' => SomeComponent::class,
        'otherComponent' => [
            'class' => OtherComponent::class,
            'public_property' => 'some value'
        ],
        '_thirdComponent' => [
            'class' => [ ThirdComponent::class, ['construct_argument_1', 'construct_argument_2'] ],
        ],
        '_nextComponent' => [
            'class' => [ NextComponent::class, ['construct_argument_1', 'construct_argument_2'] ],
            'public_property' => 'some value'
        ],
    ];

    /**
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
            $text = $this->_thirdComponent->mextMethod();
        }
        
        if (Yii::$app->request->isDelete) {
            $text = $this->_nextComponent->mextMethod();
        }

        return $this->render('view', ['text' => $text]);
    }
}
```

### Для диномической сборки настроек можно перезаписать метод `findLazyLoadConfig()`
```php
<?php

namespace some\path;

use andy87\lazy_load\LazyLoadTrait;

/**
 * SomeClass
 *
 * @property-read SomeComponent $someComponent
 * @property-read DymanicConfigComponent $dynamicConfigCmponent
 * 
 * @package yii2\controllers
 */
class SomeClass
{
    use LazyLoadTrait;


    /** @var array  */
    public array $lazyLoadConfig = [
        'someComponent' => [
            'class' => SomeComponent::class,
            'public_property' => 'some value'
        ],
        'dynamicConfigCmponent' => DymanicConfigComponent::class,
    ];

    /**
     * @return Response|string
     */
    public function actionView(): Response|string
    {
        $message = $this->otherComponent->insideSomeComponent->test();
        return $this->dynamicConfigCmponent->insideSomeComponent->test();
    }
    
    
    protected function findCachedObject(string $name): ?object
    {
        return match ($name)
        {
            '_dynamicConfigCmponent' => [
                'class' => [SomeComponent::class, $this->getArguments() ],
            ],
            default => parent::findCachedObject($name),
        }
    }
}
```

Home: https://github.com/andy87/lazy-load-trait