<?php

namespace App\Providers;

use App\Library\Exports\Excel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Exporter;
use Maatwebsite\Excel\Mixins\StoreCollection;
use Maatwebsite\Excel\Mixins\DownloadCollection;
use Illuminate\Contracts\Routing\ResponseFactory;
use Maatwebsite\Excel\QueuedWriter;
use Maatwebsite\Excel\Writer;

class ExcelServiceProvider extends \Maatwebsite\Excel\ExcelServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->mergeConfigFrom(
            $this->getConfigFile(),
            'excel'
        );

        $this->app->bind('excel', function () {
            $writer = $this->app->make(Writer::class);
            $queued = $this->app->make(QueuedWriter::class);
            $response   = $this->app->make(ResponseFactory::class);
            $filesystem = $this->app->make('filesystem');
            return new Excel($writer, $queued, $response, $filesystem);
        });

        $this->app->alias('excel', Excel::class);
        $this->app->alias('excel', Exporter::class);

        Collection::mixin(new DownloadCollection);
        Collection::mixin(new StoreCollection);
    }
}