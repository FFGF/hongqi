<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/10
 * Time: 17:11
 */
namespace Admin\Model;
use Think\Model;

class ChargeStandardModel extends Model{

    protected $tableName = 'charge_standard';

    //获得收费标准
    public function getChargeStandard($charge_standard_name){
        $maps['charge_standard_name'] = $charge_standard_name;
        return $this->where($maps)->select();
    }

}