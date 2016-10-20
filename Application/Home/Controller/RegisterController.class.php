<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/11/13
 * Time: 17:12
 */
namespace Home\Controller;

class RegisterController extends ChannelsController{
    protected $weblist=array(
        "www"=>"http://www.grandcloud.cn/",
        "accounts"=>"http://accounts.grandcloud.cn/",
    );
    public function index(){
        $where['user_id']=$this->userInfo['user_id'];
        //\print_r($this->userInfo);exit;
        $status=M('channels_user')->where($where)->field('status')->find();//检查是否已经申请过，根据status进行跳转'I'审核中，'P'通过，'F'不通过
        if($status){
            $this->assign('id',$status['status']);//用户复制网址后刷新可以知道是否通过
            $this->display("notice");
        }else{
            $user=M('cloud_member')->where($where)->field('user_name,user_company,user_website,user_phone,addr,user_email')->find();
            $this->assign('weblist',$this->weblist);
            $this->assign('user',$user);
            $this->display();
        }
    }
    public function againIndex(){//第一次审核没有通过，再一次提交
        $where['user_id']=$this->userInfo['user_id'];
        $user=M('channels_user')->where($where)->find();//第二次提交申请，从channels_user读取信息
        $company_address=json_decode($user['company_address'],true);//第二次申请，将JSON地址解析为数组地址
        $this->assign('weblist',$this->weblist);
        $this->assign('user',$user);
        $this->assign('company_address',$company_address);
        $this->display('Register:index');
    }
    public function register(){
        $company_address=array();
        $company_address['province']=I('province');
        $company_address['city']=I('city');
        $company_address['area']=I('area');
        $company_address['others']=I('company_address');
        foreach ( $company_address as $key => $value ) {
            $testJSON[$key] = urlencode ( $value );
        }
        $company_address=urldecode ( json_encode ( $testJSON ) );
        $user_phone=I('user_phone');
        $c_verify=I('verify');//客户端验证码
        $s_verify=S('sndaCloud_cap_' . $user_phone); //服务端验证码
        if($c_verify!=$s_verify){
            $this->error('手机验证码错误');
        }
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =    307200;// 设置附件上传大小300K
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =     './Uploads/'; // 设置附件上传根目录
        $upload->savePath  =     ''; // 设置附件上传（子）目录
        // 上传文件
        $info   =   $upload->upload();
        if(!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        }else{// 上传成功
            //$this->success('上传成功！');
        }
        //对文件进行重命名
        if(!changePhotoName($info,$this->userInfo['user_id'])){
            $this->error('图片重命名失败');
        }
        makeThumb($info);//生成缩略图
        $company_licence=$info["company_licence"]["savepath"].$info["company_licence"]["savename"];
        $user_idcardfront=$info["user_idcardfront"]["savepath"].$info["user_idcardfront"]["savename"];
        $user_idcardback=$info["user_idcardback"]["savepath"].$info["user_idcardback"]["savename"];
        if(I('others')){
            $company_business=implode(',',I('business')).','.I('others');//主营业务
        }else{
            $company_business=implode(',',I('business'));//主营业务
        }
        $user=M('channels_user');
        if (!$data=$user->field('user_name,user_phone,user_email,user_qq,user_address,user_remark,company_name,company_website,company_rc,company_size,company_sv,company_tel,company_email,company_agent')->create()){
            // 如果创建失败 表示验证没有通过 输出错误提示信息
            $this->error($user->getError());

        }else{
            // 验证通过 可以进行其他数据操作
            $data['user_id']=$this->userInfo['user_id'];
            $data['company_licence']=$company_licence;//营业执照保存路径
            $data['user_idcardfront']=$user_idcardfront;//用户身份证正面保存路径
            $data['user_idcardback']=$user_idcardback;//用户身份证背面保存路径
            $data['company_business']=$company_business;//主营业务
            $data['company_address']=$company_address;//公司地址
            $where['user_id']=$this->userInfo['user_id'];
            $exit=M('channels_user')->where($where)->find();
            if(empty($exit)){
                $res=M('channels_user')->add($data);
                if($res){
                    header("Location:/Register-index");//跳转到这个页面，用户复制后刷新可以知道自己是否通过审核
                }else{
                    $this->error('数据库插入出错');
                }
            }else{
                $data['status']='I';//用户资料审核未通过，需要再次提交，审核状态从未通过‘F’改为正在‘I’
                $res=M('channels_user')->where($where)->save($data);
                if($res){
                    header("Location:/Register-index");//跳转到这个页面，用户复制后刷新可以知道自己是否通过审核
                }else{
                    $this->error('数据库更新数据出错');
                }
            }
        }
    }
    //检测是否是第二次提交
    public function checkRegister(){
        $where['user_id']=$this->userInfo['user_id'];
        $where['status']=array('in',array('P','I'));
        $res=M('channels_user')->where($where)->find();
        if(empty($res)){
            $json['code']=0;//说明还没注册
        }else{
            $json['code']=1;//说明已经注册存在
        }
        exit(json_encode($json));
    }
    public function notice($id){
        $this->assign('id',$id);
        $this->display();
    }

}