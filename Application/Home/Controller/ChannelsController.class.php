<?php

namespace Home\Controller;

use Think\Controller;

class ChannelsController extends Controller {

    public $userInfo;

    public function _initialize() {
        header("Content-type:text/html;charset=utf-8");
        $this->getGrandcoudUid();

    }

    /* 空操作，用于输出404页面 */
    public function _empty() {
        $this->display('Public:404');
    }

    //检测用户是否登录
    private function getGrandcoudUid() {
        $user_info['user_id'] = '999990000017206';//123
        $this->checkChannels($user_info);//123
        $this->userInfo=session('user');
        if (empty($this->userInfo)) {
            import('Common.Org.SsoLogin');
            $user_info = \SsoLogin::get_cookie("sso_cookie");
            if (!empty($user_info)) {
                if(CONTROLLER_NAME != 'Register' && ACTION_NAME != 'sendmsg'){
                    $user_info=$this->checkChannels($user_info);
                }
                $this->userInfo=$user_info;
            } else {
                $this->error('您还未登录，请先登录。', 'https://accounts.grandcloud.cn/new_user/login?forward=http://channels.grandcloud.cn');
            }
        }
    }

    //检测用户是否为渠道代理商，并返回代理商个人信息
    protected function checkChannels($user_info) {

        $result = M('channels_info')->where(array('user_id' => $user_info['user_id']))
                                    ->field('deduct_amount,available_amount,freezes_amount')->find();
        if (empty($result)) {
            $this->error('您还不是渠道代理商，请先申请。', 'http://www.grandcloud.cn/channels');
        } else {
            $available_amount=$this->getAmount($result['available_amount']);//获取当前用户总金额
            $level_result=$this->getLevel();//获取代理商当前的等级
            $user_info['level'] = $level_result['effective_time']<date('Y-m-d H:i:s')?$level_result['to_level']:$level_result['from_level'];
            //$proportion=C('level');//获取代理商对应级的返佣比例
            //$user_info['proportion'] = $proportion[$user_info['level']];
            $user_info['total'] = \bcsub(\bcsub($available_amount, $result['freezes_amount'], 4), $result['deduct_amount'], 2);
            $where['user_id'] = array('eq',$user_info['user_id']);
            $row= M('cloud_user_login')->where($where)->order('last_date desc')->find();//获得当前用户最后一次登录日期
            !empty($row) && $user_info['last_login'] = date('Y-m-d',$row['last_date']);
            session('user', $user_info);
            return $user_info;
        }
    }

    //获取渠道商当前最新的总金额
    private function getAmount($available_amount) {
        $my_mount = S('my_mount_'.$this->userInfo['user_id']);
        if (empty($my_mount)) {
            $row = D('Customer')->orderList($this->userInfo['user_id']);
            $proportion_amount = array(0);
            foreach ($row as $v) {
                $proportion_amount[] = $v['proportion_amount'];
            }
            $my_mount = \formatAmount(\array_sum($proportion_amount)); //用户当前实时总金额
            \S('my_mount_'.$this->userInfo['user_id'],$my_mount,300);
            if (\strcasecmp($available_amount, $my_mount) !=0 ) {
                M('channels_info')->where(array('user_id' => $this->userInfo['user_id']))->save(array('available_amount' => $my_mount));
            }
        }
        return $my_mount;
    }

    //获取代理商等级
    private function getLevel() {
        $my_level = D('Customer')->returnLevel($this->userInfo['user_id']);
        if(is_array($my_level)){
            return end($my_level);
        }else{
            return false;
        }
    }

    /**
     * 通用分页列表数据集获取方法
     *
     *  可以通过url参数传递where条件,例如:  index.html?name=asdfasdfasdfddds
     *  可以通过url空值排序字段和方式,例如: index.html?_field=id&_order=asc
     *  可以通过url参数r指定每页数据条数,例如: index.html?r=5
     *
     * @param sting|Model  $model   模型名或模型实例
     * @param array        $where   where查询条件(优先级: $where>$_REQUEST>模型设定)
     * @param array|string $order   排序条件,传入null时使用sql默认排序或模型属性(优先级最高);
     *                              请求参数中如果指定了_order和_field则据此排序(优先级第二);
     *                              否则使用$order参数(如果$order参数,且模型也没有设定过order,则取主键降序);
     *
     * @param array        $base    基本的查询条件
     * @param boolean      $field   单表模型用不到该参数,要用在多表join时为field()方法指定参数
     * @author 朱亚杰 <xcoolcc@gmail.com>
     *
     * @return array|false
     * 返回数据集
     */
    protected function simpleList($model, $where = array(), $order = '', $base = array('status' => array('egt', 0)), $field = true) {
        $options = array();
        $REQUEST = (array) I('request.');
        if (is_string($model)) {
            $model = M($model);
        }

        $OPT = new \ReflectionProperty($model, 'options');
        $OPT->setAccessible(true);

        $pk = $model->getPk();
        if ($order === null) {
            //order置空
        } else if (isset($REQUEST['_order']) && isset($REQUEST['_field']) && in_array(strtolower($REQUEST['_order']), array('desc', 'asc'))) {
            $options['order'] = '`' . $REQUEST['_field'] . '` ' . $REQUEST['_order'];
        } elseif ($order === '' && empty($options['order']) && !empty($pk)) {
            $options['order'] = $pk . ' desc';
        } elseif ($order) {
            $options['order'] = $order;
        }
        unset($REQUEST['_order'], $REQUEST['_field']);

        $options['where'] = array_filter(array_merge((array) $base, /* $REQUEST, */ (array) $where), function($val) {
            if ($val === '' || $val === null) {
                return false;
            } else {
                return true;
            }
        });
        if (empty($options['where'])) {
            unset($options['where']);
        }
        $options = array_merge((array) $OPT->getValue($model), $options);
        $total = $model->where($options['where'])->count();

        if (isset($REQUEST['r'])) {
            $listRows = (int) $REQUEST['r'];
        } else {
            $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : C('PAGESIZE');
        }
        $page = new \Think\Page($total, $listRows, $REQUEST);
        if ($total > $listRows) {
            $page->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        }
        $p = $page->show();
        $this->assign('_page', $p ? $p : '');
        $this->assign('_total', $total);
        $options['limit'] = $page->firstRow . ',' . $page->listRows;
        $model->setProperty('options', $options);
        return $model->field($field)->cache(true, 5)->select();
        //echo $model->_sql();exit;
    }

    //分页
    protected function page($total) {
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : C('PAGESIZE');
        $page = new \Think\Page($total, $listRows);
        if ($total > $listRows) {
            $page->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        }
        $this->assign('_page', $page->show());
        return $page;
    }

}
