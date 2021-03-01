<?php

namespace App\Models;


trait ExtensionModelTrait
{
    protected $needLog = false;
    protected $logRemark = '';
    //It's like key => function()
    protected $resultFormat = [
//          'id'    =>  function_name
//          'key'   =>  ['class_name','method_name']
    ];

    protected $enableFormat = true;

    public function needLog(): bool
    {
        return $this->needLog;
    }

    public function setLogRemark(string $remark)
    {
        $this->logRemark = $remark;
    }

    public function getLogRemark(): string
    {
        return $this->logRemark;
    }

    public function enableLog()
    {
        $this->needLog = true;
        return $this;
    }

    public function disableLog()
    {
        $this->needLog = false;
        return $this;
    }

    public function enableFormat()
    {
        $this->enableFormat = true;
        return $this;
    }

    public function disableFormat()
    {
        $this->enableFormat = false;
        return $this;
    }

    public function getEnableFormat() : bool
    {
        return $this->enableFormat;
    }

    public function getFormat() : array
    {
        return $this->resultFormat;
    }
}