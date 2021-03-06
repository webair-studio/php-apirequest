<?php
namespace apirequest;

/**
 * Class ApiRequest
 * Wrapper for php curl to json requests
 * @package apirequest
 */
class ApiRequest
{
    private $url;
    private $curlCA;

    /**
     * ApiRequest constructor.
     * @param string $url api url in sprintf("%s") format
     * @param string $curlCA path to curl .pem file for https
     */
    public function __construct($url, $curlCA = '')
    {
        $this->url = $url;
        $this->curlCA = $curlCA;
    }

    /**
     * Do request
     * @param string $method method name
     * @param string $param param string
     * @param string $reqType type of request method
     * @param bool $decodeJSON json decode flag
     * @return bool|mixed
     */
    public function request($method, $param = '', $reqType = "POST", $decodeJSON = true)
    {
        $ans = false;
        $types = array("GET", "POST");
        $reqType = strtoupper($reqType);
        $enough = isset($method) && in_array($reqType, $types);
        if ($enough) {
            $url = sprintf($this->url, $method);

            if ($reqType == "GET" && $param) {
                $url = $url . "?" . $param;
            }

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            if ($reqType == "POST" && $param) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
            }

            if ($this->curlCA) {
                curl_setopt($ch, CURLOPT_CAINFO, $this->curlCA);
            }

            $result = curl_exec($ch);

            if ($result !== false) {
                if ($decodeJSON) {
                    $ans = json_decode($result, true);
                } else {
                    $ans = $result;
                }
            }

            curl_close($ch);
        }
        return $ans;
    }

    /**
     * Convert array to param string
     * @param $arr
     * @return bool|string
     */
    public function arrToParam($arr)
    {
        $ans = false;
        if (is_array($arr)) {
            $tmpArr = array();
            foreach ($arr as $k => $el) {
                $tmpArr[] = $k . "=" . $el;
            }
            $ans = implode("&", $tmpArr);
        }
        return $ans;
    }
}
