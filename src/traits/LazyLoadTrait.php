<?php

namespace andy87\lazy_load;

use Exception;

/**
 * Trait LazyLoad for Yii2
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
 * @property-read ThirdComponent $_thirdComponent (пример - singletone)
 * @property-read NextComponent $_nextComponent (пример - singletone)
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
 *          '_thirdComponent' => [ //singletone
 *              'class' => [ ThirdComponent::class, ['construct_argument_1', 'construct_argument_2'] ],
 *          ],
 *          '_nextComponent' => [ //singletone
 *              'class' => [ NextComponent::class, ['construct_argument_1', 'construct_argument_2'] ],
 *              'public_property' => 'value'
 *          ],
 *     ];
 * }
 * ```
 */
trait LazyLoadTrait
{
    /** @var array Кэш объектов */
    protected array $_lazyLoadCache = [];


    /**
     * Переопределение метода __get
     *
     * @param string $name Имя запрашиваемого в `BaseObject` свойства
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function __get($name): mixed
    {
        if ($lazyLoadObject = $this->getLazyLoadObject($name)) {
            return $lazyLoadObject;
        }

        return parent::__get($name);
    }

    /**
     * Получение объекта LazyLoad
     *
     * @param string $name Имя свойства
     *
     * @return ?object
     *
     * @throws Exception
     */
    public function getLazyLoadObject(string $name): ?object
    {
        if ($object = $this->findCachedObject($name)) return $object;

        if ($config = $this->findLazyLoadConfig($name))
        {
            $object = $this->constructLazyObject($config);

            if (str_starts_with($name, '_')) $this->_lazyLoadCache[$name] = $object;

            return $object;
        }

        return null;
    }

    /**
     * Возвращает объект из кэша LazyLoad
     *
     * @param string $name
     *
     * @return ?object
     */
    protected function findCachedObject(string $name): ?object
    {
        return $this->_lazyLoadCache[$name] ?? null;
    }

    /**
     * Поиск/получение LazyLoad конфигурации
     *
     * @param string $name
     *
     * @return array|string|null
     */
    protected function findLazyLoadConfig(string $name): array|string|null
    {
        return $this->lazyLoadConfig[$name] ?? null;
    }

    /**
     * Создание LazyLoad объекта на основе конфигурации
     *
     * @param array|string $config Конфигурация объекта
     *
     * @return object
     *
     * @throws Exception
     */
    protected function constructLazyObject( array|string $config ): object
    {
        if ( is_string($config) ) {

            $object = $this->builder( $config );

        } elseif ( is_array($config) ) {

            $arguments = [];
            $property = [];

            if ( !isset($config['class']) ) {
                throw new Exception('Конфигурация должна содержать ключ "class".');
            }

            $className = $config['class'];

            if ( is_array( $className ) ) {
                $arguments = $config['class'];
                $className = array_shift($arguments );

                if(count($arguments)) $arguments = $arguments[0];

                if ( !is_string($className) ) {
                    throw new Exception('Ключ "class" должен содержать строку.');
                }
            }

            if ( count( $config ) > 1 )
            {
                $property = $config;
                unset($property['class']);
            }

            $object = $this->builder( $className, $arguments, $property );

        } else {

            throw new Exception('Конфигурация должна содержать ключ "class".');
        }

        return $object;
    }

    /**
     * @param string $className
     * @param array $arguments
     * @param array $property
     *
     * @return mixed
     */
    public function builder( string $className, array $arguments = [], array $property = [] ): object
    {
        $object = new $className( ...$arguments  );

        foreach ( $property as $name => $value ){
            $object->$name = $value;
        }

        return $object;
    }
}