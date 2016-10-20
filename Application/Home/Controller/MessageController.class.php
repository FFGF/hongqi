<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/1
 * Time: 16:06
 */
namespace Home\Controller;
class MessageController extends ChannelsController{
    //获取个人消息列表
    public function index(){
        $page = isset($_GET['p']) ? $_GET['p'] : 1;
        $page_size = C("PAGESIZE");
        $where['user_id']=$this->userInfo['user_id'];
        $title=I('searchMsg');
        $where['title']=array('LIKE',"%$title%");
        $order=I('order','desc');
        $msg = M('channels_user_msg')->join('channels_msg ON channels_user_msg.msg_id=channels_msg.msg_id')
                                     ->where($where)->order(array('channels_msg.create_time'=>$order))->field('channels_msg.*,channels_user_msg.status')->page($page,$page_size)->select();
        $msg1 = M('channels_msg')->where($where)->select();
        $count = count($msg1);
        $this->page($count);
        $this->assign('msg',$msg);
        $this->display();
    }
    //显示站内信
    public function showMsg(){
        $msg_id= I('msg_id');
        $msg = M('channels_msg')->where("msg_id=$msg_id")->find();
        $this->assign('msg',$msg);
        $this->display();
    }
   //点击阅读后标记为已读
    public function readMark(){
        $where['msg_id']=I('msg_id');
        $where['user_id']=$this->userInfo['user_id'];
        $data['status']=1;//标记为已读
        M('channels_user_msg')->where($where)->save($data);
        $where1['status']=0;
        $where1['user_id']=$this->userInfo['user_id'];
        $res=M('channels_user_msg')->where($where1)->select();
        $result['number']=count($res);
        exit(json_encode($result));
    }
    //点击“标记已读按钮”标记为已读
    public function readSign(){
        $msg_id = explode(',',I('msg_id'));
        $where['user_id']=$this->userInfo['user_id'];
        $data['status']=1;//标记为已读
        foreach($msg_id as $item){
            $where['msg_id']=$item;
            M('channels_user_msg')->where($where)->save($data);
        }
        $where1['status']=0;
        $where1['user_id']=$this->userInfo['user_id'];
        $res=M('channels_user_msg')->where($where1)->select();
        $result['number']=count($res);
        exit(json_encode($result));
    }
    //获得未读站内信数目
    public function notread(){
        $where['status']=0;
        $where['user_id']=$this->userInfo['user_id'];
        $res=M('channels_user_msg')->where($where)->select();
        $result['number']=count($res);
        exit(json_encode($result));
    }

}