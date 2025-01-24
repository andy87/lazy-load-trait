Вот такая реализация пришла в голову.

Инициализация свойства класса только в момент вызова.

```php
<?php

/**
 * SomeController
 *
 * @property-read SomeComponent $someComponent
 */
class SomeController extends \yii\web\Controller
{
    use LazyLoadTrait;

    public array $lazyLoadConfig = [
        'someComponent' => [ 'class' => SomeComponent::class ],
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

        return $this->render('view', ['text' => $text]);
    }
}
```
