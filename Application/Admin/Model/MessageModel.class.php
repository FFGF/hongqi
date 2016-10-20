<?php

namespace Admin\Model;

use Think\Model;

/**
 * MessageModel
 */
class MessageModel extends Model{

    private $cache_time;

    /**
     * [getPubMsgList 获得公共消息列表]
     * @return [type] [description]
     */

    public function _initialize(){
        parent::_initialize();
        $data_cache_time = C('data_cache_time');
        $this->cache_time = $data_cache_time['message_model'];
    }

    public function getPubMsgList($map=null){
        $cache_name = 'getPubMsgList_'.md5(serialize($map));
        if(S($cache_name)){
            return S($cache_name);
        }else{
            $model = M('channels_pub_msg');
            $result = $model->order('status desc,create_time desc')->select();

            // TODO: 缓存
            // S($cache_name,$result,$this->cache_time['min_time']);

            return $result;
        }
    }

    /**
     * [getUserMsgList 获得用户消息列表]
     * @return [type] [description]
     */
    public function getUserMsgList($map){
        $cache_name = 'getPubMsgList_'.md5(serialize($map));
        if(S($cache_name)){
            return S($cache_name);
        }else{
            $model = M('channels_user_msg');
            $result = $model->order('status desc,create_time desc')->select();

            // TODO: 缓存
            // S($cache_name,$result,$this->cache_time['min_time']);

            return $result;
        }
    }

    /**
     * [getMsgListByUserId 获得某个用户消息]
     * @return [type] [description]
     */
    public function getMsgListByUserId($user_id){
        $pub_list = $this->getPubMsgList($map);
        $user_list = $this->getUserMsgListByUserId($user_id);
        if(empty($pub_list)){
            $pub_list = array();
        }
        $result = array_merge($pub_list,$user_list);
        return $result;
    }

    /**
     *	[getUserMsgListByUserId 获得某个用户个人消息]
     *	@return [type] [description]
     */
    public function getUserMsgListByUserId($user_id){
        $model = M('channels_user_msg');
        $where['user_id'] = array('eq',$user_id);
        $result = $model->where($where)->order('status desc,create_time desc')->select();
        return $result;
    }

    /**
     * [getPubMsgById 通过id获得公共消息]
     * @param  [type] $msg_id [消息id]
     * @return [type]         [消息]
     */
    public function getPubMsgById($msg_id){
        $model = M('channels_pub_msg');
        $where['id'] = array('eq',$msg_id);
        $result = $model->where($where)->find();
        return $result;
    }


    /**
     * [getUserMsgById 通过id获得用户消息]
     * @param  [type] $msg_id [消息id]
     * @return [type]         [消息]
     */
    public function getUserMsgById($msg_id){
        $model = M('channels_user_msg');
        $where['id'] = array('eq',$msg_id);
        $result = $model->where($where)->find();
        return $result;
    }


    /**
     * [getUserInfoList 获得所有已审核通过渠道商info]
     * @return [type] [description]
     */
    public function getUserInfoList(){
        $model = M('channels_user');
        $where['status'] = array('eq','P');
        $result = $model->field('user_id,user_name,user_phone,user_email,user_company')
            ->where($where)
            ->select();
        return $result;
    }

    /**
     * [delete 删除消息]
     * @param  [type] $msg_id   [消息id]
     * @param  [type] $msg_type [消息类型]
     * @return [bool]           [操作结果]
     */
    public function delete($msg_id,$msg_type){
        if($msg_type == 'pub_msg'){
            $model = M('channels_pub_msg');
        }else{
            $model = M('channels_user_msg');
        }

        $result = $model->delete($msg_id);

        if($result === false){
            $this->error = '删除失败!';
            return false;
        }else{
            return true;
        }
    }
    /**
     * [save 保存消息]
     * @param [array] $msg [消息]
     * @return [bool] [是否保存成功]
     */
    public function save($msg){
        if(empty($msg['user_id'])){
            $model = M('channels_pub_msg');
        }else{
            $model = M('channels_user_msg');
        }

        if(isset($msg['user_id'])){
            $where['user_id'] = array('eq',$msg['user_id']);
            $row = M('channels_user')->where($where)->find();
            if(empty($row)){
                $this->error = '保存失败：渠道商不存在!';
                return false;
            }
        }

        $result = $model->add($msg,array(),true) ;

        if($result === false){
            $this->error = '保存失败!';
            return false;
        }else{
            return true;
        }
    }

}
