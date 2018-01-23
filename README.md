# silex-sessions

![Beta Status](https://img.shields.io/badge/status-beta-blue.svg?style=flat-square)

[cookie icon by Rashida Luqman Kheriwala from the Noun Project](https://thenounproject.com/search/?q=cookie&i=119497)

This package provides client side, cookie based sessions for Silex. It is a standalone implementation and does not use Symfony's session mechanism. Session data is stored in a cookie allowing excellent horizontal scalability. The serialized data is encryption using [defuse/php-encryption](https://github.com/defuse/php-encryption) to ensure it is safe in the wild.

## Installation

Installation is easy via composer:

```php
composer require ronanchilvers/silex-sessions
```

## Configuration

Firstly you need to add the service provider. There are various configuration options (detailed below) but you must at least provide an encryption key. See the [key generation section below](#encryption-key-generation) for details on creating a secure key.

```php
$app->register(new Ronanchilvers\Silex\Sessions\SessionProvider(), [
    'encryption.key' => $secureKey
]);

```

### Configuration options

There are various configuration options you can use, mostly to tweak the cookie storage the session uses. These are:

 - cookie.name - The name of the session cookie
 - cookie.expire - Expiry time for the cookie - by default when the browser closes
 - cookie.path - The cookie path - '/' by default
 - cookie.domain - The cookie domain - empty by default
 - cookie.secure - Whether the session cookie should only be valid for HTTPS connections
 - cookie.http.only - Whether the cookie should only be available via HTTP
 - encryption.key - The secure encryption key to use for encrypting and decrypting the cookie payload

## Usage

```SessionProvider``` registers a couple of services on the container:

 - A ```session``` service, the standard session interface. This service has the usual ```get()```, ```set()```, ```addFlash()``` and ```getFlash()``` methods available.
 - If you're using [```knplabs/console-service-provider```](https://github.com/KnpLabs/ConsoleServiceProvider) you'll also get a ```session:key:generate``` command to use for key generation. **NB:** Make sure that you register the console provider *before* the session provider.

```php
// Set session variables
$app['session']->set('name', 'Fred Bloggs');
$app['session']->set('stuff', ['data' => 123]);
$app['session']->addFlash('notice', 'Yeehaa!');

// Get them out again
$name = $app['session']->get('name');
// With a default
$stuff = $app['session']->get('stuff', []);

// Get the flashes for a particular type
foreach ($app['session']->getFlashes('notice') as $message) {
    echo "<p>Notice : {$message}</p>";
}
```

## Symfony Web Profiler Support

A data collector for the Symfony Web Profiler is included. To use it you need to first add the ```silex/web-profiler``` package to your project. Then enable the web profiler providers like so:

```php
$app->register(new Silex\Provider\WebProfilerServiceProvider(), [
    'profiler.cache_dir' => 'a directory somewhere',
]);
$app->register(new Ronanchilvers\Silex\Sessions\SessionWebProfilerProvider());
```

You should then start seeing a new button on the profiler toolbar and a new panel showing the session contents.

## Encryption Key Generation

Generating a decently secure encryption key is important to maintain the security of the session data. This package provides a [Symfony console](https://github.com/symfony/console) command to generate and output an ASCII safe key which you can store in a config file. The console command is added automatically if you're using [knplabs/console-service-provider](https://github.com/KnpLabs/ConsoleServiceProvider).

```bash
$ php bin/console session:key:generate
Generating new random encryption key
Key : aaf2234228005e7766c2e5075d9d229a4ff9fd0788a8c1d4dde08b1aa3a3d0e413c7694174201e20989fcb9db8238a8b6bdb1277f3d0e413c766c2e5075d9d2197d4d5b
```
