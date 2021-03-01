<?php

namespace App\Models\RegisterUsers;

use App\Models\Collection;
use App\Models\ExtensionModelTrait;
use App\Models\Model;
use App\Models\ModelExtInterface;
use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Notifications\Notifiable;

class Users extends User implements ModelExtInterface
{
    public $table = 'users';


    public $timestamps = true;
    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';

    use Notifiable, ExtensionModelTrait;


//    public function newQuery()
//    {
//        $query = $this->newBaseQueryBuilder();
//        $builder = new class($query) extends Builder
//        {
//
//            /**
//             * Create a new Eloquent query builder instance.
//             *
//             * @param  \Illuminate\Database\Query\Builder $query
//             * @return void
//             */
//            public function __construct(QueryBuilder $query)
//            {
//
//                parent::__construct($query);
//            }
//
//            /**
//             * Execute the query and get the first result.
//             *
//             * @param  array $columns
//             * @return \Illuminate\Database\Eloquent\Model|object|static|null
//             */
//            public function first($columns = ['*'])
//            {
//                /** @var Model $user */
//                $user = $this->take(1)->get($columns)->first();
//
////                //從別的模型查數據  設置到這個模型裏面
////                if ($user != null) {
////                    $profile = UserProfile::query()
////                        ->where('user_id', '=', $user->getAttributeValue('id'))
////                        ->first();
////
////                    if (!empty($profile)) {
////                        $profile = $profile->toArray();
////                        $profile['profile_id'] = $profile['id'];
////                        unset($profile['id']);
////                        foreach ($profile as $key => $value) {
////                            $user->setAttribute($key, $value);
////                        }
////                    }
////
////                    $third = UserThird::query()->where('user_id', '=', $user->getAttributeValue('id'))->take(2)
////                        ->get();
////                    if (!empty($third)) {
////                        $third = $third->toArray();
////                        $user->setAttribute('third', $third);
////                    }
////                }
//                return $user;
//            }
//
//        };
//        $builder->setModel($this);
//        return $builder;
//    }

    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function __get($name)
    {
        $value = $this->getAttributeValue($name);
        if (is_null($value)) {
            return '';
        }
        if (!is_string($value)) {
            return json_encode($value);
        }
        return $value;
    }


    protected function newBaseQueryBuilder()
    {
        $connection = $this->getConnection();

        $builder = new \App\Models\QueryBuilder(
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

}