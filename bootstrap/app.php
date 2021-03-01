<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/
use \Illuminate\Support\HtmlString;
use \Illuminate\Support\Str;

/**
 * rewrite mix
 * @param $path
 * @param string $manifestDirectory
 * @return HtmlString|string
 * @throws Exception
 * @throws \Illuminate\Container\EntryNotFoundException
 */
//function mix($path, $manifestDirectory = '')
//{
//    static $manifests = [];
//
//    if (! Str::startsWith($path, '/')) {
//        $path = "/{$path}";
//    }
//
//    if ($manifestDirectory && ! Str::startsWith($manifestDirectory, '/')) {
//        $manifestDirectory = "/{$manifestDirectory}";
//    }
//
//    if ( Str::startsWith($path, '/storage')) {
//        return new HtmlString(env('SFS_URL','//localhost:8080').$path);
//    }
//
//    if (file_exists(public_path($manifestDirectory.'/hot'))) {
//        $url = file_get_contents(public_path($manifestDirectory.'/hot'));
//
//        if (Str::startsWith($url, ['http://', 'https://'])) {
//            return new HtmlString(Str::after($url, ':').$path);
//        }
//
//        return new HtmlString("//localhost:8080{$path}");
//    }
//
//    $manifestPath = public_path($manifestDirectory.'/mix-manifest.json');
//
//    if (! isset($manifests[$manifestPath])) {
//        if (! file_exists($manifestPath)) {
//            throw new \Exception('The Mix manifest does not exist.');
//        }
//
//        $manifests[$manifestPath] = json_decode(file_get_contents($manifestPath), true);
//    }
//
//    $manifest = $manifests[$manifestPath];
//
//    if (! isset($manifest[$path])) {
//        report(new Exception("Unable to locate Mix file: {$path}."));
//
//        if (! app('config')->get('app.debug')) {
//            return $path;
//        }
//    }
//
//    return new HtmlString($manifestDirectory.$manifest[$path]);
//}

$app = new Illuminate\Foundation\Application(
    realpath(__DIR__.'/../')
);

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;
