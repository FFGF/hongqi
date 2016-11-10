<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/23
 * Time: 19:18
 */
namespace Admin\Controller;
use Think\Model;

class FinanceController extends ChannelsController{
    public function index(){
        $this->display();
    }
}