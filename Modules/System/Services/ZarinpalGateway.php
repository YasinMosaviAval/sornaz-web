<?php
namespace Modules\System\Services;

class ZarinpalGateway
{
    public function host(bool $checkout = false): string
    {
        return filter_var(env('ZARINPAL_SANDBOX',false),FILTER_VALIDATE_BOOL)?'https://sandbox.zarinpal.com':($checkout?'https://www.zarinpal.com':'https://payment.zarinpal.com');
    }
    public function request(string $action,array $payload): array
    {
        if(!in_array($action,['request','verify','inquiry'],true))throw new \RuntimeException('عملیات درگاه معتبر نیست.',422);
        $curl=curl_init($this->host().'/pg/v4/payment/'.$action.'.json');
        curl_setopt_array($curl,[CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>json_encode($payload,JSON_UNESCAPED_UNICODE),CURLOPT_RETURNTRANSFER=>true,CURLOPT_CONNECTTIMEOUT=>10,CURLOPT_TIMEOUT=>30,CURLOPT_HTTPHEADER=>['Content-Type: application/json','Accept: application/json']]);
        $raw=curl_exec($curl);$http=(int)curl_getinfo($curl,CURLINFO_HTTP_CODE);curl_close($curl);
        $result=is_string($raw)?json_decode($raw,true):null;
        if($http!==200||!is_array($result))throw new \RuntimeException('ارتباط با درگاه برقرار نشد. دوباره تلاش کنید.',502);
        return $result;
    }
}
