<?php

namespace Home\Controller;

class IndexController extends ChannelsController {

    public function index() {
        $info = $this->checkChannels($this->userInfo);
        $this->assign('info', $info);
        $this->display();
    }

    /**
     * datalist 获得今年每天收入数组
     */
    public function datalist(){
        $user_id = $this->userInfo['user_id'];
        $payments_list = D('Customer')->orderForChart($user_id);

        $time = mktime(0,0,0,1,1,date('Y'));
        $date = date('Y-m-d',$time);
        foreach ($payments_list as $key => $val) {
            if($val['transaction_date'] >= $date)
                $income[substr($val['transaction_date'],0,10)] += $val['proportion_amount'];
        }

        for($i=0 ; $i<366; $i++){
            $day = date('Y-m-d',strtotime("+{$i} days",$time));
            $list[$i] = empty($income[$day])? 0 : $income[$day];
        }
        $this->ajaxReturn($list);

    }

    public function logout() {
        \session('[destroy]');
        import('Common.Org.SsoLogin');
        \SsoLogin::set_cookie('sso_cookie', '');
        \SsoLogin::set_cookie('user_id', '');
        redirect('https://accounts.grandcloud.cn/cas/doLogout');
    }
}
