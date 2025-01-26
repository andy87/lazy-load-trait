<?php

namespace yii2\common\components\traits;

use Yii;
use yii\base\{InvalidConfigException, UnknownPropertyException};

/**
 * Trait LazyLoad
 *
 * @property array $lazyLoadConfig Конфигурация для ленивой загрузки объектов
 *
 * Пример использования:
 * ```php
 * @property-read Foo $foo (пример)
 * @property-read Bar $bar (пример)
 * @property-read Zero $zero (пример)
 *
 * class ProfileController extends yii\web\Controller
 * {
 *     use LazyLoadTrait;
 *
 *     public array $lazyLoadConfig = [
 *         'foo' => Foo::class,
 *         'bar' => ['class' => Bar::class],
 *         '_zero' => [['class' => Zero::class], ['token']], // Singleton
 *     ];
 * }
 * ```
 */
trait LazyLoadTrait
{
    /** @var array Кэш для ленивых объектов */
    private array $_lazyLoadCache = [];



    /**
     * Переопределение магического метода __get для ленивой загрузки
     *
     * @param string $name Имя свойства
     * @return mixed
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
     * Получение ленивого объекта
     *
     * @param string $name Имя свойства
     * @return ?object
     * @throws InvalidConfigException
     */
    public function getLazyLoadObject(string $name): ?object
    {
        if ($object = $this->findCachedObject($name)) {
            return $object;
        }

        if ($config = $this->findLazyLoadConfig($name))
        {
            $object = $this->constructLazyObject($config);

            if (str_starts_with($name, '_')) {
                $this->_lazyLoadCache[$name] = $object;
            }

            return $object;
        }

        return null;
    }

    /**
     * @param string $name
     *
     * @return object|null
     */
    protected function findCachedObject(string $name): ?object
    {
        return $this->_lazyLoadCache[$name] ?? null;
    }

    /**
     * @param string $name
     *
     * @return array|null
     */
    protected function findLazyLoadConfig(string $name): ?array
    {
        return $this->lazyLoadConfig[$name] ?? null;
    }

    /**
     * Создание объекта на основе конфигурации
     *
     * @param array|string $config Конфигурация объекта
     * @return object
     * @throws InvalidConfigException
     */
    protected function constructLazyObject(array|string $config): object
    {
        if (is_array($config))
        {
            if (is_array($config[0]) && !isset($config[0]['class'])) {
                throw new InvalidConfigException('Конфигурация должна содержать ключ "class".');
            }

            if (isset($config[1]) && is_array($config[1])) 
            {
                $_constructParams = array_values($config[1]);

                return Yii::createObject( $config[0], $_constructParams );
            }
        }

        return Yii::createObject($config);
    }
}
