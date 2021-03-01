<?php

namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;

class Model extends \Illuminate\Database\Eloquent\Model implements ModelExtInterface
{
    use ExtensionModelTrait;
    //use SoftDeletes;

    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';

    protected function newBaseQueryBuilder()
    {
        $connection = $this->getConnection();

        $builder = new QueryBuilder(
            $connection, $connection->getQueryGrammar(), $connection->getPostProcessor()
        );

        $builder->setModel($this);
        return $builder;
    }

    public function newCollection(array $item = [])
    {
        $collection = new Collection($item);
        $collection->setModel($this);
        return $collection;
    }

    public function setTimestamp($bool = true)
    {
        $this->timestamps = $bool;

        return $this;
    }
}