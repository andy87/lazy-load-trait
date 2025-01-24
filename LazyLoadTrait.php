<?php

namespace common\components\traits;

use Yii;
use yii\base\{InvalidConfigException, UnknownPropertyException};

/**
 * Trait LazyLoad
 *
 * @property array $lazyLoadConfig
 *
 * ```php
 * *
 * * @property-read Foo $foo
 * * @property-read Bar $bar
 * * @property-read Zero $zero
 * *
 * class ProfileController extends yii/web/Controller
 * {
 *  use LazyLoadTrait;
 *
 *  public array $lazyLoadConfig = [
 *      'foo' => Foo::class,
 *      'bar' => [ 'class' => Bar::class ]
 *      'zero' => [ [ 'class' => Zero::class ], [ 'token' ] ]
 *  ];
 *
 *   public function actionIndex()
 *   {
 *      // ...
 *
 *      $this->foo->method();
 *
 *      // ...
 *   }
 * }
 *
 * ```
 *
 * @package yii2\common\components\traits
 */
trait LazyLoadTrait
{
    /**
     * Модифицированный метод __get для ленивой загрузки объектов
     *
     * @param $name
     *
     * @return mixed
     *
     * @throws UnknownPropertyException|InvalidConfigException
     */
    public function __get($name): mixed
    {
        if ( $lazyLoadObject = $this->checkLazyLoadObject($name) )
        {
            return $lazyLoadObject;
        }

        return parent::__get($name);
    }

    /**
     * Создание объекта по имеющимся данным конфига
     * в классе использующем трейт
     *
     * @param string $name
     *
     * @throws InvalidConfigException
     */
    public function checkLazyLoadObject(string $name): mixed
    {
        $config = $this->lazyLoadConfig[$name] ?? null;

        if ( $config ) {
            if ( is_string($config) ) {
                return Yii::createObject( $config );
            }

            if ( is_array($config)) {
                if( count($config) === 2 ) {
                    return Yii::createObject( $config[0], $config[1] );
                }

                return Yii::createObject( $config );
            }
        }

        return null;
    }
}
