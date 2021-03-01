<?php

namespace App\Models;


interface ModelExtInterface
{
    public function needLog(): bool;

    public function setLogRemark(string $remark);


    public function getLogRemark(): string;

    public function enableLog();

    public function disableLog();

    public function enableFormat();

    public function disableFormat();

    public function getEnableFormat() : bool;

    public function getFormat() : array;

}