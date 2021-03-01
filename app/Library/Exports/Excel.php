<?php

namespace App\Library\Exports;


use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Filesystem\FilesystemManager;
use Maatwebsite\Excel\QueuedWriter;
use Maatwebsite\Excel\Writer;

class Excel extends \Maatwebsite\Excel\Excel
{

    /**
     * @return Writer
     */
    public function getWriter(): Writer
    {
        return $this->writer;
    }

    /**
     * @return QueuedWriter
     */
    public function getQueuedWriter(): QueuedWriter
    {
        return $this->queuedWriter;
    }

    /**
     * @return ResponseFactory
     */
    public function getResponse(): ResponseFactory
    {
        return $this->response;
    }

    /**
     * @return FilesystemManager
     */
    public function getFilesystem(): FilesystemManager
    {
        return $this->filesystem;
    }


}