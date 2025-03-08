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
                throw new InvalidConfigException('Конфигурация должна содержать ключ "class".');
            }

            if (isset($config[0]) && is_array($config[0])) {
                return Yii::createObject( $config['class'], $config[0] );
            }
        }

        return Yii::createObject($config);
    }
}
