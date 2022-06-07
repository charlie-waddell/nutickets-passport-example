# Nutickets Laravel Passport Integration

This repository details the required changes to Laravel Passport in order to successfully integrate with the Nutickets
OAuth implementation.

Firstly, when the user is redirected back to Nutickets with their access token, there needs to be a way for Nutickets to
identify the user (the user's name and email address), which is a fundamental requirement for actions such as sending
order confirmation emails and so on.

This can be achieved by altering the returned access token (JWT), by adding two custom Claims (name and email) when it
is generated. To achieve this, you need to override the Access Token generation that ships with Laravel Passport.

1. Create a new `AccessToken` class (see [app/Passport/AccessToken.php](https://github.com/charlie-waddell/nutickets-passport-example/blob/main/app/Passport/AccessToken.php#L57-L58)). This is where you will add the custom name and email Claims.
2. Create a new `AccessTokenRepository` class, and override the `getNewToken` method to return an instance of your new`AccessToken` (see [app/Passport/AccessTokenRepository.php](https://github.com/charlie-waddell/nutickets-passport-example/blob/main/app/Passport/AccessTokenRepository.php)).
3. In your `AuthServiceProvider`, register your new `AccessTokenRepository` by binding it to `Laravel\Passport\Bridge\AccessTokenRepository::class` (see [app/Providers/AuthServiceProvider.php](https://github.com/charlie-waddell/nutickets-passport-example/blob/main/app/Providers/AuthServiceProvider.php)).
   ```php
   use App\Passport\AccessTokenRepository;
   use Laravel\Passport\Bridge;
   
   ...
   
   public function register()
   {
       parent::register();

       $this->app->bind(Bridge\AccessTokenRepository::class, function ($app) {
           return $app->make(AccessTokenRepository::class);
       });
   }
   ```
