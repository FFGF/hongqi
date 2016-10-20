<?php
namespace Home\Model;

class CcwuxiModel extends ChannelsModel{
    protected $connection = array(
        'db_type'  => 'mysql',
        'db_user'  => 'dbro',
        'db_pwd'   => 'dhq2xwpdvk28vft0hfwrgckd5ardn4wu',
        'db_host'  => '10.176.2.83',
        'db_port'  => '3306',
        'db_name'  => 'DB_CLOUDCORE',
        'db_charset'=>'utf8',
    );

    //返回用户在无锡的主机信息和带宽信息
    public function detail($data){
        return array_merge((array)$this->bandwidth($data['bandwidth']), (array)$this->vm($data['vm']));
    }

    //获取主机信息
    private function vm($data){
        $vm=$data['VM'];
        $vm_reserved=$data['VM_Reserved'];
        $where['IS_DELETED']=array('EQ', 0);
        //获取所有系统类型
        $os=$this->table('OS_TYPE')->getField('OS_TYPE_ID,OS_TYPE_NAME');
        //获取所有主机类型
        $vmtype=$this->table('VM_TYPE')->getField('TYPE_ID,TYPE_LABEL');
        //获取所有按需主机信息
        if(!empty($vm)){
            $where['VM_ID']=array('IN', \arrayColumn($vm, 'external_id'));
            $vm_result=$this->table('VM_INFO')->where($where)->field('VM_ID as id,CREATE_TIME AS start_time,OS_TYPE_ID,VM_TYPE')->select();
            //按需
            if(!empty($vm_result)){
                foreach($vm_result as $k=>$v){
                    $vm_result[$k]['name']=$os[$v['OS_TYPE_ID']].$vmtype[$v['VM_TYPE']];
                    $vm_result[$k]['end_time']='-';
                }
            }
            unset($where['VM_ID']);
        }
        if(!empty($vm_reserved)){
            //获取所有套餐主机信息
            $where['COUNT_TYPE']=array('IN', \arrayColumn($vm_reserved, 'external_id'));
            $vm_reserved_result=$this->table('VM_INFO')->where($where)->field('COUNT_TYPE as id,CREATE_TIME AS start_time,OS_TYPE_ID,VM_TYPE')->select();
            //获取所有套餐主机的时间
            $timewhere['RI_ID']=array('IN', \arrayColumn($vm_reserved_result, 'id'));
            $time=$this->table('RESERVED_INSTANCE')->where($timewhere)->getField('RI_ID,START_TIME,END_TIME');
            //包年包月
            if(!empty($vm_reserved_result)){
                foreach($vm_reserved_result as $k=>$v){
                    $vm_reserved_result[$k]['name']=$os[$v['OS_TYPE_ID']].$vmtype[$v['VM_TYPE']];
                    $vm_reserved_result[$k]['start_time']=$time[$v['id']]['START_TIME'];
                    $vm_reserved_result[$k]['end_time']=$time[$v['id']]['END_TIME'];
                }
            }
        }
        return array_merge((array)$vm_result, (array)$vm_reserved_result);
    }

    //获取带宽信息
    private function bandwidth($bandwidth){
        $where['STATUS']=array('NEQ', 'deleted');
        //获取所有带宽信息
        if(!empty($bandwidth)){
            $where['RI_GROUP_ID']=array('IN', \arrayColumn($bandwidth, 'external_id'));
            $temp=$this->table('BANDWIDTH_RI')->where($where)->field('RI_GROUP_ID,START_TIME,END_TIME,NET_TYPE,BANDWIDTH,STATUS')->order('START_TIME asc')->select();
            //根据RI_GROUP_ID对带宽信息进行重新分组
            if(!empty($temp)){
                foreach($temp as $k=>$v){
                    if(!empty($temp[$v['RI_GROUP_ID']])){
                        array_push($temp[$v['RI_GROUP_ID']], $v);
                    }else{
                        $temp[$v['RI_GROUP_ID']][0]=$v;
                    }
                    unset($temp[$k]);
                }
            }
            //所有带宽
            if(!empty($bandwidth)){
                foreach($bandwidth as $k=>$v){
                    if(!empty($temp[$v['external_id']])){
                        $first=reset($temp[$v['external_id']]);
                        $last=end($temp[$v['external_id']]);
                        $result[$v['external_id']]['start_time']=$first['START_TIME'];
                        $result[$v['external_id']]['end_time']=$last['END_TIME'] == NULL ? '-' : $last['END_TIME'];
                        if(strtotime($result[$v['external_id']]['end_time'])-strtotime($result[$v['external_id']]['start_time']) == 86400)
                            $result[$v['external_id']]['end_time']='-';
                        $last['NET_TYPE'] == 'telecom' && $result[$v['external_id']]['name'].='电信';
                        $last['NET_TYPE'] == 'unicom' && $result[$v['external_id']]['name'].='联通';
                        $last['NET_TYPE'] == 'smart' && $result[$v['external_id']]['name'].='电信联通';
                        $last['NET_TYPE'] == 'BGP' && $result[$v['external_id']]['name'].='BGP';
                        $result[$v['external_id']]['name'].=' '.$last['BANDWIDTH'].'M带宽';
                        $last['STATUS'] == 'unbind' && $result[$v['external_id']]['name'].='[未绑定]';
                    }
                }
            }
        }
        return $result;
    }
}