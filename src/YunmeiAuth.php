<?php
/**
 * 云莓智能类
 * @author  Mouse123s <i@mouse123.cn>
 * @version 1.0
 */
namespace FoskyTech;

class YunmeiAuth
{
    public function login(string $username, string $password) {
        $data = $this->request('https://base.yunmeitech.com/login', [
            'userName'  =>  $username,
            'userPwd'   =>  md5($password)
        ]);

        if ($data['success'] === true) {
            return [
                'error' =>  0,
                'data'  =>  [
                    'userId'    =>  $data['o']['userId'],
                    'telephone'    =>  $data['o']['userTel'],
                    'realName' =>  $data['o']['realName'],
                    'token' =>  $data['o']['token']
                ]
            ];
        }

        return [
            'error' =>  1,
            'error_description'    =>  isset($data['msg']) ? $data['msg'] : '未知错误'
        ];
    }

    public function schoolInfo($userId, string $token) {
        $data = $this->request('https://base.yunmeitech.com/userschool/getbyuserid', [
            'userId'  =>  $userId
        ], [
            'tokenUserId: ' . $userId,
            'tokenData: ' . $token
        ]);

        if (count($data) > 0) {
            return [
                'error' =>  0,
                'data'  =>  [
                    'schoolNo'    =>  $data[0]['schoolNo'],
                    'schoolName'    =>  $data[0]['school']['schoolName'],
                    'serverUrl'    =>  $data[0]['school']['serverUrl'],
                    'token' =>  $data[0]['token']
                ]
            ];
        }

        return [
            'error' =>  1,
            'error_description'    =>  '未知错误'
        ];
    }

    public function dormInfo(string $server_url, $userId, string $token, string $schoolNo) {
        $data = $this->request($server_url . '/dormuser/getuserlock', [
            'schoolNo'  =>  $schoolNo
        ], [
            'tokenUserId: ' . $userId,
            'tokenData: ' . $token
        ]);

        if (count($data) > 0) {
            return [
                'error' =>  0,
                'data'  =>  $data[0]
            ];
        }

        return [
            'error' =>  1,
            'error_description'    =>  '未知错误'
        ];
    }

    public function getRoommate(string $server_url, $userId, string $token, string $schoolNo, string $areaNo, string $buildNo, string $dormNo) {
        $data = $this->request($server_url . '/student/getbydormloginuser', [
            'schoolNo'  =>  $schoolNo,
            'areaNo'    =>  $areaNo,
            'buildNo'   =>  $buildNo,
            'dormNo'    =>  $dormNo
        ], [
            'tokenUserId: ' . $userId,
            'tokenData: ' . $token
        ]);

        if (count($data) > 0) {
            foreach ($data as $k => $v) {
                unset($data[$k]['stuPwd']);
                unset($data[$k]['stuIdNum']);
                unset($data[$k]['stuFaceInfo']);
                unset($data[$k]['isDel']);
                unset($data[$k]['attr1']);
                unset($data[$k]['attr2']);
                unset($data[$k]['attr3']);
                unset($data[$k]['attr4']);
                unset($data[$k]['attr5']);
                unset($data[$k]['enable']);
            }
            return [
                'error' =>  0,
                'data'  =>  $data
            ];
        }

        return [
            'error' =>  1,
            'error_description'    =>  '未知错误'
        ];
    }
    private function request(string $url = '', array $param = [], array $headers = []) {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url . '?' . http_build_query($param),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTPHEADER => array_merge([
                'x-requested-with: XMLHttpRequest'
            ], $headers),
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
            return false;
        }

        $data = json_decode($response, true);

        return $data;
    }
}