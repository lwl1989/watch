<?php


namespace App\Services;


use App\Library\Constant\Common;
use Illuminate\Support\Facades\Auth;

class ServiceBasic
{
    use ServiceTrait;
    protected $serviceName = '';
    public function __construct()
    {
        self::$instance = $this;
    }

    protected function _getRoleAuth() : int
    {
        /** @var \Illuminate\Database\Eloquent\Model $user */
        $user = Auth::user();

        if(empty($user)) {
            return false;
        }

        $role  = intval($user->getAttributeValue('role'));
        if($role == Common::ADMIN_ROLE_MANAGER) {

            return $role;

        }else if($role == Common::ADMIN_ROLE_EMPLOYEE){

            $perm = $user->getAttributeValue('permissions');
            if(is_string($perm)) {

                $perm = json_decode($perm, true);
                if(!is_array($perm)) {
                    return 0;
                }
                //
                //if(in_array($this->serviceName,$perm)) {
                // return $role;
                //}
                //


            }
            $need = $this->_getNeedPerm();

            if(in_array($need,$perm)) {
                return $role;
            }

        }else{
            return $role;
        }

        return 0;
    }

    protected function _getNeedPerm() : string
    {
        $mapping = array_values(Common::PERMISSION_MAPPING);
        foreach ($mapping as $value) {
            if(isset($value['actions'])) {
                $item = array_column($value['actions'], 'en');
                if(in_array($this->serviceName, $item)) {
                    return $this->serviceName;
                }
            }
        }
        return '';
    }
}