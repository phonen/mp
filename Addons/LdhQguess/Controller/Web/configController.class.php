<?php

namespace Addons\LdhQguess\Controller\Web;
use Addons\LdhQguess\Controller\WebController;

/**
 * QQ在线竞猜后台管理控制器
 * @author 么么哒
 */
class configController extends WebController {
    
    public function index(){

        $where['mpid']=get_mpid();
        $lists=M('ldhqguess_config')->where($where)->select(); 
        foreach($lists as &$v){
             switch($v['type']){
                 case 1:
                     $v['typename']='大小';
                     break;
                 case 2:
                     $v['typename']='3位数';
                     break;
                 case 3:
                     $v['typename']='2位数';
                     break;
                 case 4:
                     $v['typename']='1位数';
                     break;
                 case 5:
                     $v['typename']='合';
                     break;

             }
            $v['createby']=unserialize($v['createby']);
            $v['updateby']=unserialize($v['updateby']);
        }

        $this->assign('lists',$lists);
        $this->display();
    }
 


    public function edit(){
        $where['mpid']=$this->mpid;
        $where['id']=I('get.id')?I('get.id'):die('666');
        if(IS_POST){
            $upinfo["id"] = session('user_id');
            $upinfo["loginFlag"] =1;
            $upinfo["roleNames"] ='';
            $upinfo["admin"] =session('superadmin');

            $data['updateDate']=time();
            $data['odds']=I('post.odds');
            $data['remark']=I('post.remark');
            $data['updateBy']=serialize($upinfo);

            $res=M('ldhqguess_config')->where($where)->save($data);
            if($res){
                $info['status']=1;
                $info['info']='操作成功!';
            }else{
                $info['status']=0;
                $info['info']='操作失败!';
            }
            $info['url']=U('addon/LdhQguess/web/config_index');
            $this->ajaxReturn($info);
            exit;
        }
        $res=M('ldhqguess_config')->where($where)->find();
        $this->assign('res',$res);
        $this->display();


    }

}

?>