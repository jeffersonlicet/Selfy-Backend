# Authentication

Para el login del admin y proteger sus rutas de usuarios autenticados en el panel de administracion, pueden usar el middleware creado en App\Http\Middleware\AuthMiddleware usuandolo de la siguiente manera

```php 
    Route::group(['middleware' => 'App\Http\Middleware\AuthMiddleware'], function(){});
```

De esa manera protegemos un grupo de rutas 