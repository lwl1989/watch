<?php
namespace App\Services\Admin;

use App\Services\ServiceBasic;
use App\Models\Admin\AdminProfile;


class AdminProfileService extends ServiceBasic
{
	protected $model = AdminProfile::class;

	public function getDepartmentId($admin_id)
	{
		$model = self::getModelInstance();

		$obj = $model->newQuery()
			->where('admin_id',$admin_id)
			->first();

		if(empty($obj)) {
			return [];
		}

		$data = $obj->toArray();

		return $data['department_id'] ?? 0;
	}



}