<?php

namespace Addons\LdhQguess\Controller\Mobile;
use Addons\LdhQguess\Controller\MobileController;

/**
 * QQ在线竞猜移动端控制器
 * @author 么么哒
 */
class queryController extends MobileController {
    public function queryLotteryRecord(){


        $mpid=get_mpid();
        $ldhqguess_opencode=M('ldhqguess_opencode');
        $w['mpid']=$mpid;
        $w['status']=3;
        $opens=$ldhqguess_opencode->where($w)->order('id DESC')->limit(15)->select();
        if(!$opens){
            $res['success']=true;
            $res['timeout']=false;
            $res['overdue']=false;
            $res['currentDate']=get_microtime();
            $this->ajaxReturn($res);
        }

        foreach($opens as $k=> $v){
            $historyList[$k]['id']=$v['id'];
            $historyList[$k]['createDate']=date('Y-m-d',$v['createDate']);
            $historyList[$k]['updateDate']=date('Y-m-d',$v['updateDate']);
            $historyList[$k]['issueNo']=$v['issueNo'];
            $historyList[$k]['openCode']=$v['openCode'];
            $historyList[$k]['status']=3;
            $historyList[$k]['startBetDate']=date('Y-m-d H:i:s',$v['startBetDate']);;
            $historyList[$k]['endBetDate']=date('Y-m-d H:i:s',$v['endBetDate']);;
            $historyList[$k]['openDate']=date('Y-m-d H:i:s',$v['openDate']);;
            $historyList[$k]['openFinishDate']=date('Y-m-d H:i:s',$v['openFinishDate']);
        }

        $opens=array_reverse($opens);
        foreach($opens as $k=> $v){
            $num1=substr($v['openCode'], -1)*1;
            $num2=substr($v['openCode'], -2,1)*1;
            //计算遗漏
            for($i=1;$i<14;$i++){
                $statisticsList[$i-1]['id']=$i-1;
                $statisticsList[$i-1]['createDate']=date('Y-m-d',$opens[0]['createDate']);
                $statisticsList[$i-1]['updateDate']=date('Y-m-d',$opens[0]['createDate']);
                $statisticsList[$i-1]['discType']=$i-1;
                $statisticsList[$i-1]['remark']=$opens[0]['issueNo'];
                if($i<11){
                    //计算数字
                    if($i==10){
                        $a=0;
                    }else{
                        $a=$i;
                    }
                    if($num1<>$a){
                        $statisticsList[$i-1]['noOpenCount']+=1;
                    }else{
                        $statisticsList[$i-1]['noOpenCount']=0;
                    }
                }

            }
            //计算10大11小12合
            if($num1<>$num2){
                if($num1>=5){
                    //开大
                    $statisticsList[10]['noOpenCount']=0;//出现大0
                    $statisticsList[11]['noOpenCount']+=1;//遗漏小+
                }else{
                    //开小
                    $statisticsList[11]['noOpenCount']=0;//出现小0
                    $statisticsList[10]['noOpenCount']+=1;//遗漏大+
                }
                $statisticsList[12]['noOpenCount']+=1;//遗漏合+
            }else{
                //开合
                $statisticsList[12]['noOpenCount']=0;//出现合0
            }

        }

        $data['historyList']=$historyList;
        //$data['statisticsList']=$statisticsList;
        $data['statisticsList']=array_values($statisticsList);
        $res['data']=$data;
        $res['success']=true;
        $res['timeout']=false;
        $res['overdue']=false;
        $res['currentDate']=get_microtime();

        $this->ajaxReturn($res);

    }
    public function queryWithDrawList(){
        $openid=get_openid();
        $mpid=get_mpid();
        $w['openid']=$openid;
        $w['mpid']=$mpid;
        $ldhqguess_duh=M('ldhqguess_duh');
        $aa=$ldhqguess_duh->where($w)->order("id DESC")->limit(20)->select();

        foreach($aa as &$v){
            $v['createTime']=date('m-d H:i:s',$v['createTime']);
            $aa['totalMoney']+=$v['money'];
        }

        $res['data']['withdrawList']=array_values($aa);
        $res['success']=true;
        $res['timeout']=false;
        $res['overdue']=false;
        $res['currentDate']=get_microtime();

        $this->ajaxReturn($res);
    }
    public function queryRankList(){
      //  exit('{"success":true,"data":{"dayList":[{"rankNo":1,"userName":"10106492","totalMoney":60021.0},{"rankNo":2,"userName":"10115667","totalMoney":46584.0},{"rankNo":3,"userName":"10049351","totalMoney":27873.0},{"rankNo":4,"userName":"10115242","totalMoney":24816.5},{"rankNo":5,"userName":"10030408","totalMoney":18363.5},{"rankNo":6,"userName":"10046554","totalMoney":17575.0},{"rankNo":7,"userName":"10116589","totalMoney":11485.5},{"rankNo":8,"userName":"10104591","totalMoney":9633.0},{"rankNo":9,"userName":"10095131","totalMoney":8483.5},{"rankNo":10,"userName":"10078078","totalMoney":8224.0}],"monthList":[{"rankNo":1,"userName":"10050155","totalMoney":97462.5},{"rankNo":2,"userName":"10052071","totalMoney":91387.0},{"rankNo":3,"userName":"10022376","totalMoney":77967.0},{"rankNo":4,"userName":"10066403","totalMoney":74605.5},{"rankNo":5,"userName":"10061699","totalMoney":72200.0},{"rankNo":6,"userName":"10011484","totalMoney":60002.0},{"rankNo":7,"userName":"10076079","totalMoney":54216.5},{"rankNo":8,"userName":"10042205","totalMoney":50160.0},{"rankNo":9,"userName":"10043194","totalMoney":48796.5},{"rankNo":10,"userName":"10038896","totalMoney":45638.0}],"myMonthData":{"userNo":"10031853","money":6950.0},"yesDayList":[{"rankNo":1,"userName":"10096284","totalMoney":28113.0},{"rankNo":2,"userName":"10087290","totalMoney":27367.0},{"rankNo":3,"userName":"10089402","totalMoney":25173.5},{"rankNo":4,"userName":"10095532","totalMoney":24463.5},{"rankNo":5,"userName":"10067442","totalMoney":23863.5},{"rankNo":6,"userName":"10089914","totalMoney":23140.0},{"rankNo":7,"userName":"10099996","totalMoney":23044.5},{"rankNo":8,"userName":"10107228","totalMoney":22961.5},{"rankNo":9,"userName":"10106492","totalMoney":19180.5},{"rankNo":10,"userName":"10081169","totalMoney":18764.5}],"yesMonthList":[{"rankNo":1,"userName":"10050155","totalMoney":97462.5},{"rankNo":2,"userName":"10052071","totalMoney":91387.0},{"rankNo":3,"userName":"10022376","totalMoney":77967.0},{"rankNo":4,"userName":"10061699","totalMoney":72200.0},{"rankNo":5,"userName":"10066403","totalMoney":62633.5},{"rankNo":6,"userName":"10011484","totalMoney":60002.0},{"rankNo":7,"userName":"10042205","totalMoney":50160.0},{"rankNo":8,"userName":"10076079","totalMoney":49048.5},{"rankNo":9,"userName":"10043194","totalMoney":48796.5},{"rankNo":10,"userName":"10038896","totalMoney":45638.0}]},"timeout":false,"overdue":false,"currentDate":1495719594511}');
    }
    public function getAgentCount(){
        $addon_name=get_addon();
        $uid=session($addon_name.'_uid');
        $data=count_child_class($uid);
        $res['data']=$data;
        $res['success']=true;
        $res['timeout']=false;
        $res['overdue']=false;
        $res['currentDate']=get_microtime();

        $this->ajaxReturn($res);

    }
}

?>