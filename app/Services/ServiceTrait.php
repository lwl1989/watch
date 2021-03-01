<?php


namespace App\Services;


use App\Exceptions\ErrorConstant;
use App\Library\Constant\Common;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait ServiceTrait
{
    public static $instance;
    protected $model;
    protected $attr;
    protected $listField = ['*'];
    protected $detailField = ['*'];
    protected $searchField = ['name'];

    public function getOne($id) : array
    {
        $model = static::getModelInstance();

        $obj = $model->newQuery()->find($id, static::getSelfDetailField());
        if(empty($obj)) {
            return [];
        }

        $obj = $obj->toArray();
        return $obj;
    }
    /**
     * @param $id
     * @return int
     */
    public function delete($id) : int
    {
        $model = static::getModelInstance();
        if(is_array($id)) {
            return $model->newQuery()->whereIn('id', $id)->update(['deleted'=>Common::DELETED]);
        }
        return $model->newQuery()->where('id', $id)->update(['deleted'=>Common::DELETED]);
    }
    /**
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function setAttr(string $key, $value)
    {
        if(!is_string($value)) {
            $value = json_encode($value);
        }
        $this->attr[$key] = $value;
        return $this;
    }
    /**
     * @param $id
     * @return int
     */
    public function update($id) : int
    {
        $model = static::getModelInstance();
        return $model->newQuery()->where('id',$id)->update($this->attr);
    }

    /**
     * @return int
     */
    public function create() : int
    {
        $model = static::getModelInstance();


        foreach ($this->attr as $key => $value) {
            $model->setAttribute($key, $value);
        }

        $saved = $model->save();

        if($saved) {
            return intval($model->id);
        }else{
            return -1;
        }
    }

    /**
     * @return string
     */
    public function getModel() : string
    {
        return $this->model;
    }

    /**
     * @param string $model
     * @return $this
     */
    public function setModel(string $model)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * @return Model
     */
    public static function getModelInstance() : Model
    {
        $model = static::getSelfModel();

        if(!empty($model)) {
            if(class_exists($model)) {
                return new $model();
            }
        }
        throw new ModelNotFoundException("class {$model} not found", ErrorConstant::SYSTEM_ERR);
    }

    /**
     * @param array $conditions
     * @param bool $deleted
     * @param int $status
     * @param bool $exitsDelete
     * @return Builder
     */
    protected static function _getQuery(array $conditions, bool $deleted = false, int $status = -1, bool $exitsDelete = true)
    {
        $service = static::_getInstance();
        $model = $service->getModelInstance();

		if($exitsDelete){
			$conditions['deleted'] = ['=',$deleted ? 1 : 0];
		}

        if($status != -1) {
            $conditions['status'] = ['=',$status];
        }

        $query = $model::query();
        $table = $model->getTable();
        foreach ($conditions as $field=>$operate) {
            if ($field == 'order' and is_array($operate)) {
                $query->orderBy($operate['field'], $operate['sort'] ?? 'desc');
                continue;
            }

			if ($field == 'raw' and is_array($operate)) {
				$query->whereRaw($operate[0]. ' ' . $operate[1].' ?', [$operate[2]]);
				continue;
			}

			if ($field === 'rawBetween' and is_array($operate)) {
                $query->whereRaw($operate[0].' between ? and ?', [$operate[1], $operate[2]]);
                continue;
            }

            if($field == 'orWhereBetween' and is_array($operate)) {
            	$query->where(function ($query)use($operate){
            		$query->whereBetween($operate[0], $operate[1])
			            ->orWhereBetween($operate[2], $operate[3]);
	            });
            	continue;
            }

            if ($field == 'or' and is_array($operate)) {
                    /**@var $query Builder **/
                    foreach ($operate as $key=>$value) {
                        if(is_array($value)) {
                            $key = $table . '.' . $key;
                            $query->orWhere($key, $value[0], $value[1]);
                        }
                    }
                continue;
            }

			if ( strpos($field,'.') === false ) {
				$field = $table.'.'.$field;
			}

            if($field == $table.'.keyword') {
                if(is_array($service->searchField)) {
                    $query->where(function($query) use($service, $table, $operate){
                        foreach ($service->searchField as $key) {
                            $field = $table . '.' .$key;
                            $query->orWhere($field,'like', '%' . $operate . '%');
                        }
                    });
                }else {
                    $field = $table . '.' . $service->searchField;
                    if(is_array($operate) and isset($operate[1])) {
                        $query->where($field, $operate[0], '%' . $operate[1] . '%');
                    }else {
                        $query->where($field, 'like', '%' . $operate . '%');
                    }
                }
                continue;
            }

            if(is_array($operate)) {
                if(count($operate) == 2) {
					if( $operate[0] == 'between'){
					    if(is_array($operate[1])) {
                            $query->whereBetween($field, $operate[1]);
                        }else if(isset($operate[2])) {
                            $query->whereBetween($field, [$operate[1],$operate[2]]);
                        }
					}else if($operate[0] == 'in'){
						$query->whereIn($field, $operate[1]);
					}elseif($operate[0] == 'like'){
                        $query->where($field, 'like', '%'.$operate[1].'%');
                    }else{
						$query->where($field, $operate[0], $operate[1]);
					}
                }else{
                    $query->where($field, '=', $operate);
                }
            }else{
                $query->where($field, $operate);
            }
        }

        return $query;
    }

	/**
	 * @param array $conditions
	 * @param bool $deleted
	 * @param int $status
	 * @param bool $exitsDelete
	 * @return int
	 */
    public static function count(array $conditions, bool $deleted = false, int $status = -1, bool $exitsDelete=true) : int
    {
        $query = static::_getQuery($conditions, $deleted, $status, $exitsDelete);
        $count = $query->count();
        return intval($count);
    }


    /**
     * @param array $conditions
     * @param int $limit
     * @param int $page
     * @param bool $deleted
     * @param int $status
     * @param bool $exitsDelete
     * @return array
     */
    public static function limit(array $conditions, int $limit = 15, int $page = 1, bool $deleted = false, int $status = -1, bool $exitsDelete = true) : array
    {
        $query = static::_getQuery($conditions, $deleted, $status, $exitsDelete);

        $list = $query->skip(($page-1)*$limit)
            ->take($limit)
            ->get(static::getSelfListField())
            ->toArray();

        return $list;
    }

    /**
     * @return ServiceTrait
     */
    protected static function _getInstance()
    {
        if(static::$instance == null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * @return string
     */
    protected static function getSelfModel() : string
    {
        $service = static::_getInstance();
        return $service->getModel();
    }

    /**
     * @return array
     */
    public static function getSelfListField() : array
    {
        $service = static::_getInstance();
        $fields = $service->listField;

        if(empty($fields)) {
            return ['*'];
        }
        return $fields;
    }

    /**
     * @return array
     */
    public static function getSelfDetailField() : array
    {
        $service = static::_getInstance();
        $fields = $service->detailField;

        if(empty($fields)) {
            return ['*'];
        }
        return $fields;
    }


    /**
     * @param array $fields
     */
    public static function setSelfListField(array $fields)
    {
        $service = static::_getInstance();
        $service->listField = $fields;
    }

    /**
     * @param array $fields
     */
    public static function setSelfDetailField(array $fields)
    {
        $service = static::_getInstance();
        $service->detailField = $fields;
    }

}