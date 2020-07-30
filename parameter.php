<?php


class parameter
{
    const pay_api = "https://api.mch.weixin.qq.com/pay/unifiedorder";
    public function parameters(){
        $data = [
            'appid'=>APPID,
            'mch_id'=>MCH_ID,
            'nonce_str'=>uniqid(),
            'body'=>'测试',
            'out_trade_no'=>time().uniqid(),
            'total_fee'=>0.1,
            'spbill_create_ip'=>$_SERVER['REMOTE_ADDR'],
            'notify_url'=>'http://192.168.0.106/index.php',
            'trade_type'=>'MWEB',
        ];
        //scene_info  场景信息
        /*
         * {"h5_info": //h5支付固定传"h5_info"
   {"type": "",  //场景类型
    "wap_url": "",//WAP网站URL地址
    "wap_name": ""  //WAP 网站名
    }
}
         * */
        $data['scene_info'] = ['h5_info'=>['type'=>'Wap','wap_url'=>'http://127.0.0.1/pay.php','wap_name'=>'测试']];
        ksort($data);
        $str =urldecode(http_build_query($data).'&key='.KEY) ;
        $data['sign'] = strtoupper(md5($str));
        $this->pay($data);
    }

    public function pay($arr){
        header("content-type:text/html;charset=utf-8");
        $xml = "
        <xml>
        <appid>{$arr['appid']}</appid>
        <body>{$arr['body']}</body>
        <mch_id>{$arr['mch_id']}</mch_id>
        <nonce_str>{$arr['nonce_str']}</nonce_str>
        <notify_url>{$arr['notify_url']}</notify_url>
        <out_trade_no>{$arr['out_trade_no']}</out_trade_no>
        <spbill_create_ip>{$arr['spbill_create_ip']}</spbill_create_ip>
        <total_fee>{$arr['total_fee']}</total_fee>
        <trade_type>{$arr['trade_type']}</trade_type>
        <scene_info>{$arr['scene_info']}</scene_info>
        <sign>{$arr['sign']}</sign>
        </xml>
        ";
        $ret = $this->curl_post(self::pay_api,$xml);
        $data =  $this->xmlToArray($ret);
        if ($data['return_code'] == 'SUCCESS'){
            var_dump($data['return_msg']) ;die;
        }else{
            var_dump($data['return_msg']) ;die;
        }

    }
    public  function curl_post($url , $data){
        $header[] = "Content-type: text/xml";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        // POST数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // 把post的变量加上
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
        curl_close($ch);

        return $output;
    }
 public  function xmlToArray($xml){
        $array = (array)simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        return $array;
    }
}