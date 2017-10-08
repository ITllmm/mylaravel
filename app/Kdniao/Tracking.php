<?php
    namespace App\Kdniao; // <?php 不能与 namespace 对齐

    use GuzzleHttp\Client;
    use GuzzleHttp\Exception\RequestException;

    class Tracking
    {
        protected $client;

        public function __construct(Client $client)
        {
            $this->client = $client;
        }

        public function track(string $shipping_method, string $tracking_number) //要求类型而不是强制转化
        {

            $request_data = [
                'OrderCode' => '',  //订单编号
                'ShipperCode' => $this->convertToShipperCode($shipping_method, $tracking_number), //快递公司编码
                'LogisticCode' => $tracking_number,//物流单号
            ];

            $request_data = json_encode($request_data); //JSON格式

            $data = array(
                'EBusinessID' => config("kdniao.ebusiness_id"), //电商ID
                'RequestType' => '1002', //请求指令类型：1002
                'RequestData' => urlencode($request_data) ,
                'DataType' => '2', //请求、返回数据类型： 2-json；
                'DataSign' => $this->encrypt($request_data,  config("kdniao.api_key")) //数据内容签名
            );

            try {
                $response = retry(3,  function () use ($data) { //重载三次
                   return $this->client->post(config("kdniao.endpoint"), ['form_params' => $data]);  //use GuzzleHttp POST
                });
            } catch (RequestException $e) {
                   throw new Exception($e->getMessage());
            }

           //根据公司业务处理返回的信息......
            $body = json_decode($response->getBody()->getContents(), 1); //getBody getContents -> GuzzleHttp

            if (!$body['Success']) { //订单查询失败
                throw new Exception($body['Reason']); //抛出的异常交个调用你这接口的前端来处理
            }

            return [
                'state' => $body['State'],
                'state_reason' => $this->getStateReason($body['State']),
                'tracking_number' => $tracking_number,
                'shipping_method' => $shipping_method,
                'traces' => $body['State']==0?0:$this->sortTracesByDate($body['Traces']),
            ];
     }

    //订单接受状态的时间排序，以最新的状态时间来排最前端('AcceptTime')
    private function sortTracesByDate(array $data)
    {
          return collect($data)->sortByDesc('AcceptTime')->toArray();
    }

    // DataDesign
    private function encrypt($data, $api_key)
    {
        return urlencode(base64_encode(md5($data.$api_key)));
    }

    //Shipper Code
    private function convertToShipperCode($method, $tracking_number)
    {
        if  ($method == 'EMS') {
            if (starts_with($tracking_number, 'R')) {
                return 'YZPY';
            }
            return 'EMS';
        }

        return $method;
    }

    // private function changeToShipperCode($method)
    // {
    //     if($method == 'DHL_EN') {
    //         return 'DHL';
    //     }

    //     if ($method == 'FEDEX_GJ') {
    //         return 'FEDEX';
    //     }

    //     return $method;
    // }

    //Get StateReason
    private function getStateReason($state)
    {
        $state_reason = [
             0 => '无轨迹',
             2 => '在途中',
             3 => '已签收',
             4 => '问题件',
            ];

        return  $state_reason[$state];
    }
}