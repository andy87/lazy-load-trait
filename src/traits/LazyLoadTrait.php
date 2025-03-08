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
 * use some\path\Some;
 * use some\directory\Next;
 * use some\location\Third;
 *
 * @property-read Some $some (пример)
 * @property-read Next $next (пример)
 * @property-read Third $_third (пример)
 *
 * class ProfileController extends yii\web\Controller
 * {
 *     use LazyLoadTrait;
 *
 *     public array $lazyLoadConfig = [
 *         'some' => Some::class,
 *         'next' => ['class' => Next::class],
 *         '_third' => [['class' => Third::class], ['token']], // Singleton
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
    protected function constructLazyObject(array|string $config): object
    {
        if (is_array($config))
        {
            if (count($config) > 1 && !isset($config['class'])) {
                throw new Exception('Конфигурация должна содержать ключ "class".');
            }

            $object = new $config['class']();

            if (isset($config[0]) && is_array($config[0])) {
                foreach ($config[0] as $name => $value) {
                    $object->$name = $value;
                }
            }

        } else {

            $object = new $config();
        }

        return $object;
    }
}
