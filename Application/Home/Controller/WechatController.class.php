<?php
/**
 * User: Administrator
 */


// 缓存 utf-8
namespace Home\Controller;

use Think\Controller;
use Org\TPWechat;

class WechatController extends Controller
{


    //读取配置
    public function readOption()
    {
        $options = array(
            'token' => C("token"),
            'encodingaeskey' => C("encodingaeskey"),
            'appid' => C("appid"),
            'appsecret' => C("appsecret")
        );
        return $options;
    }

    //验证服务器
    public function valid()
    {

        $options = $this->readOption();
        $weObj = new TPWechat($options);
        $weObj->valid();
        $type = $weObj->getRev()->getRevType();
        $event = $weObj->getRev()->getRevEvent();

        switch ($type) {
            case TPWechat::MSGTYPE_TEXT:
                $weObj->text("欢迎来到公众号！！！")->reply();
                exit;
                break;
            case TPWechat::MSGTYPE_EVENT:
                switch ($event['event']) {
                    case TPWechat::EVENT_SUBSCRIBE:
                        $this->addSubscribe($weObj->getRev()->getRevFrom());
                        $weObj->text("欢迎来到公众号！！！")->reply();
                        exit;
                        break;
                    default:
                        exit;
                        break;

                }
            default:
                $weObj->text("欢迎使用！！！")->reply();
                break;
        }
    }

    //创建菜单
    public function creadMenu()
    {
        $options = $this->readOption();
        $weObj = new TPWechat($options);
        // echo "11";
        // var_dump(file_get_contents("php://input")) ;
        //echo json_encode(file_get_contents("php://input"));
        /* echo $_POST['button'];
         //菜单 son_decode($_POST['button'],true);
         echo json_decode($_POST['button'],true);*/

        $request= json_decode(file_get_contents("php://input"));
        $button= $this->objectToArray($request);
       // $button= $request->button;
        /*$newmenu = array(
            "button" =>$button

        );*/
       /* echo strlen(file_get_contents("php://input")-1);
        /*$button=substr(file_get_contents("php://input"),0,(strlen(file_get_contents("php://input")-1)))*/;

        /*echo $button;*/
       // var_dump(['button']);
        //var_dump($newmenu);
        $open=fopen("log.txt","a" );
        fwrite($open,file_get_contents("php://input"));
        fclose($open);

        $result = $weObj->createMenu($button);
        if($result){
            echo "success";
        }else{
            echo "fail";
        }
        //echo $result;
        //echo "success";
    }


    //获取菜单
    public function getMenu()
    {
        $options = $this->readOption();
        $weObj = new TPWechat($options);
        $menu = $weObj->getMenu();
        echo json_encode($menu);
    }

    //添加订单者到数据库
    public function  addSubscribe($openid)
    {
        $Subscribe = M("subscribe"); // 实例化活动对象
        $data['openid'] = $openid;
        $data['creat_time'] = date("y-m-d h:i:s", time());
        $data['states'] = true;
        $Subscribe->add($data);
    }

    public function objectToArray($e){

        $e=(array)$e;

        foreach($e as $k=>$v){

            if( gettype($v)=='resource' ) return false;

            if( gettype($v)=='object' || gettype($v)=='array' )

                $e[$k]=(array)$this->objectToArray($v);

        }

        return $e;

    }


}