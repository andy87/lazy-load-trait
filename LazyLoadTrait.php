<?php

#namespace components\traits;
namespace common\components\traits;

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
    /** @var array Кэш объектов */
    protected array $_lazyLoadCache = [];



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
     * Получение объекта LazyLoad
     *
     * @param string $name Имя свойства
     *
     * @return ?object
     *
     * @throws InvalidConfigException
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
     * @throws InvalidConfigException
     */
    protected function constructLazyObject(array|string $config): object
    {
        if (is_array($config))
        {
            if (isset($config[0]) && is_array($config[0]) && !isset($config[0]['class'])) {
                throw new InvalidConfigException('Конфигурация должна содержать ключ "class".');
            }

            if (isset($config[1]) && is_array($config[1])) {
                return Yii::createObject( $config[0], array_values($config[1]) );
            }
        }

        return Yii::createObject($config);
    }
}
