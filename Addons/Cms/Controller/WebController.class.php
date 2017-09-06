<?php

namespace Addons\Cms\Controller;
use Mp\Controller\AddonsController;

/**
 * QQ在线竞猜后台管理控制器
 * @author 么么哒
 */
class WebController extends AddonsController {
    protected $mpid;
    public function _initialize()
    {
        parent::_initialize();
        $this->mpid=get_mpid();
        if(!$this->mpid){exit('谁');}

    }


    public  function bb (){
        $aax=1496690363;
        $aa=1496690364;
        echo date('Y-m-d H:i:s',$aax);
        echo date('Y-m-d H:i:s',$aa);

    }

    public function aaa(){
        exit;
        $M=M();
        $sql="SELECT * FROM `dc_ldhqguess_chongzhi` WHERE ex_openid<>'' and  orderNo IN('11496497368','11496500905','11496538381','11496538496','11496538504','11496540123','11496540291','11496541497','11496542803','11496544312','11496549248','11496552176','11496553149','11496553732','11496555371','11496555485','11496558537','11496558863','11496562832','11496562909','11496563561','11496563863','11496564307','11496564591','11496564720','11496564785','11496565572','11496565696','11496566109','11496566594','11496566595','11496566650','11496566862','11496566979','11496567013','11496567022','11496567023','11496567076','11496567146','11496567157','11496567262','11496567269','11496567445','11496567509','11496567523','11496567527','11496567590','11496567634','11496567638','11496567648','11496567656','11496567714','11496567757','11496567806','11496567828','11496567869','11496567893','11496567959','11496568068','11496568181','11496568185','11496568192','11496568275','11496568362','11496568386','11496568398','11496568430','11496568433','11496568462','11496568476','11496568480','11496568490','11496568493','11496568542','11496568579','11496568849','11496568884','11496568975','11496568981','11496568988','11496569002','11496569033','11496569065','11496569108','11496569154','11496569167','11496569176','11496569281','11496569427','11496569481','11496569605','11496569775','11496569777','11496569821','11496569831','11496569838','11496569888','11496569914','11496569920','11496569952','11496570076','11496570163','11496570204','11496570254','11496570274','11496570278','11496570283','11496570315','11496570320','11496570326','11496570341','11496570409','11496570439','11496570444','11496570487','11496570497','11496570508','11496570588','11496570591','11496570618','11496570637','11496570651','11496570681','11496570685','11496570700','11496570709','11496570746','11496570804','11496570815','11496570823','11496570864','11496570935','11496570955','11496571003','11496571036','11496571114','11496571126','11496571165','11496571300','11496571339','11496571343','11496571470','11496571918','11496571942','11496571997','11496572061','11496572153','11496572211')";
        $list=$M->query($sql);
        $ldhqguess_user=M('ldhqguess_user');
        $a=0;
        foreach($list as $v){
            $w['openid']=$v['openid'];
            $w['ex_openid']=array('neq',$v['ex_openid']);
            $aa=$ldhqguess_user->where($w)->select();
            if($aa){
                unset( $w['ex_openid']);
                $ex_openid=$ldhqguess_user->where($w)->getField('ex_openid');
                 $a['openid']=$v['openid'];echo $v['orderNo'].' ';echo $v['openid'].' ';echo date('m-d H:i:s',$v['payDate']).' ';echo $ex_openid.' '; echo $v['amount']."<br>";
                  //M('ldhqguess_user')->where($a)->setInc('moneyx',$v['amount']);
            }
        }




        
    }
    public function home(){

        $mpid=get_mpid();
        $ww['mpid']=$mpid;
        $M=M();
        $sql="SELECT SUM(betAmount) as betAmount,SUM(winAmount) as winAmount,(SUM(betAmount)-SUM(winAmount)) as y  FROM __PREFIX__ldhqguess_num ";
        $zong=$M->query($sql);

        $sql="SELECT count(issueNo)  FROM __PREFIX__ldhqguess_num GROUP BY issueNo";
        $total=$M->query($sql);
        $Page =page($total, 20, 0, array());
        $Page->Static=true;
        $sql="SELECT issueNo,buytime,SUM(betAmount) as betAmount,SUM(winAmount) as winAmount  FROM __PREFIX__ldhqguess_num ".
            " GROUP BY issueNo  ORDER BY issueNo DESC  LIMIT ".$Page->firstRow.','.$Page->listRows;
        $Page->SetPager('default', '<span class="all">共有{recordcount}条信息</span>{first}{prev}{liststart}{list}{listend}{next}{last}');
        $list=$M->query($sql);
        $this->assign("Page", $Page->show());
        foreach($list as &$v){
            $v['y']=$v['betAmount']-$v['winAmount'];

        } 
        $this->assign('lists',$list);
        $this->assign('zong',$zong);
        $this->display();
    }


    public function _empty(){ 
        $actions=ACTION_NAME  ;
        $model=explode('_',$actions);
        if(count($model)>1){
            $controller=$model[0]?$model[0]:die('谁1？');
            $action=$model[1]?$model[1]:die('谁2？');
            $class='\Addons\LdhQguess\Controller\Web\\'.$controller.'Controller';
            if(class_exists($class)) {
                $news             =   new $class();
                $news->$action();
            }else{
                exit('是谁3？');
            }
        }else{
            exit('是谁4？');
        }

    }


}

?>