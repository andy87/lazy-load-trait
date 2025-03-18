Вот такая реализация пришла в голову.

Инициализация свойства класса только в момент вызова(обращения к ним).
P.S. Знаю что в PHP c версии 8.4 появилась поддержка lazyLoad из коробки, но это ещё не завезли в Yii2.

Установка.

Composer:
```bash
composer require andy87/lazy-load-trait
```

## Использование. Порядок дейсвий.

### 1. Аннотации
Указать свойство в аннотации класса
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

### 2. Добавление use
Для подключения трейта в классе имеется 2 варианта Trait'ов:
* `andy87\lazy_load\yii2\LazyLoadTrait` - для использования в фреймворке Yii2 с применением метода `Yii::createObject()`
* `andy87\lazy_load\LazyLoadTrait` - для использования вне фреймворка Yii2

### 3. Конфигурация свойств
указать конфигурацию в свойстве `$lazyLoadConfig`

Структура конфигурации.
* для использования свойства как экземпляр класса (без настроек), доступно 2 варианта:
```php
    public array $lazyLoadConfig = [
        'someComponent' => SomeComponent::class, // быстрый способ ( меньше проверок )
        'otherComponent' => [
            'class' => OtherComponent::class,  // способ поедленней ( больше проверок )
        ],
    ]
```

* с назначением публичных свойств класса
```php
    public array $lazyLoadConfig = [
      'otherComponent' => [
            'class' => OtherComponent::class,
            'public_property_1' => 'value_1',
            'public_property_2' => 'value_2',
        ],
    ]
```

* с передачей параметров в аргументы функции `__construct()` 
```php
    public array $lazyLoadConfig = [
       'thirdComponent' => [
            'class' => [ ThirdComponent::class, ['construct_argument_1', 'construct_argument_2'] ],
        ],
    ]
```
* комбинирование назначения публичных свойств и передача параметров в аргументы функции `__construct()`
```php
    public array $lazyLoadConfig = [
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
    public array $lazyLoadConfig = [
        '_nextComponent' => [ // данное своство при первом обращении будет закешировано, и при последующих обращениях будет использоваться закешированная версия
            'class' => [ NextComponent::class, ['construct_argument_1', 'construct_argument_2'] ],
            'public_property_1' => 'value_1',
            'public_property_2' => 'value_2',
        ],
    ]
```


### 4.Использование
Обращаться к свойствам как к обычным свойствам класса
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
     * @return string
     */
    public function actionView(): string
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

### Для динамической настройки, с получением своств из метода, надо перезаписать метод `findLazyLoadConfig()`
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
     * @return string
     */
    public function actionView(): string
    {
        $message = $this->otherComponent->insideSomeComponent->test();
        return $this->dynamicConfigCmponent->insideSomeComponent->test();
    }
    
    protected function findLazyLoadConfig(string $name): ?object
    {
        return match ($name)
        {
            'dynamicConfigCmponent' => [
                'class' => [SomeComponent::class, $this->getArguments() ],
            ],
            default => parent::findCachedObject($name),
        }
    }
}
```


## P.S. в Yii2 имеется.
регистрация класса и вызов его через контейнер:
```
Yii::$container->set('someComponent', SomeComponent::class);

Yii::$container->get('someComponent')
```
Ленивая загрузка компоненов приложения:
```
// condig.php
'components' => [
    'someComponent' => [
        'class' => 'some\path\SomeComponent',
        'property' => 'value',
    ],
],


//use
$component = Yii::$app->someComponent; // Создается только при обращении
```

Home: https://github.com/andy87/lazy-load-trait
