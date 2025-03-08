<?php

namespace andy87\lazy_load\yii2;

use Yii;
use yii\base\{InvalidConfigException, UnknownPropertyException};

/**
 * Trait LazyLoad
 *
 * @property array $lazyLoadConfig Конфигурация для ленивой загрузки объектов
 *
 * Пример использования:
 * ```php
 *
 * use some\path\SomeComponent;
 * use some\directory\OtherComponent;
 * use some\location\ThirdComponent;
 * use some\target\NextComponent;
 *
 * @property-read SomeComponent $someComponent (пример)
 * @property-read OtherComponent $otherComponent (пример)
 * @property-read ThirdComponent $_thirdComponent (пример)
 * @property-read NextComponent $_nextComponent (пример)
 *
 * class ProfileController extends yii\web\Controller
 * {
 *     use LazyLoadTrait;
 *
 *     public array $lazyLoadConfig = [
 *          'someComponent' => SomeComponent::class,
 *          'otherComponent' => [
 *              'class' => OtherComponent::class,
 *              'public_property' => 'value'
 *          ],
 *          '_thirdComponent' => [
 *              'class' => [ ThirdComponent::class, ['construct_argument_1', 'construct_argument_2'] ],
 *          ],
 *          '_nextComponent' => [
 *              'class' => [ NextComponent::class, ['construct_argument_1', 'construct_argument_2'] ],
 *              'public_property' => 'value'
 *          ],
 *     ];
 * }
 * ```
 */
trait LazyLoadTrait
{
    use \andy87\lazy_load\LazyLoadTrait;

    /**
     * Переопределение метода __get
     *
     * @param string $name Имя запрашиваемого в `BaseObject` свойства
     *
     * @return mixed
     *
     * @throws UnknownPropertyException|InvalidConfigException
     */
    public function __get($name): mixed
    {
        if ($lazyLoadObject = $this->getLazyLoadObject($name)) {
            return $lazyLoadObject;
        }

        return parent::__get($name);
    }

    /**
     * @param string $className
     * @param array $arguments
     * @param array $property
     *
     * @return mixed
     *
     * @throws InvalidConfigException|UnknownPropertyException
     */
    public function builder( string $className, array $arguments = [], array $property = [] ): object
    {
        if (count($property))
        {
            $config = [
                'class' => $className,
                ...$property
            ];

        } else {

            $config = $className;
        }

        return (count($arguments) )
            ? Yii::createObject( $config, $arguments )
            : Yii::createObject( $config );
    }
}