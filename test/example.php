<?php

require_once '../vendor/autoload.php';
use FoskyTech\YunmeiAuth;

$yunmei = new YunmeiAuth;

// 登录
$login = $yunmei->login('手机号', '密码');
# print_r($login);

$userId = $login['data']['userId'];
$token = $login['data']['token'];

// 使用登录返回的参数获取学校信息
$school_info = $yunmei->schoolInfo($userId, $token);
# print_r($school_info);

$schoolNo = $school_info['data']['schoolNo'];
$serverUrl = $school_info['data']['serverUrl'];
$token = $school_info['data']['token']; // 服务器返回了新的token

// 获取宿舍信息及门锁信息
$dorm_info = $yunmei->dormInfo($serverUrl, $userId, $token, $schoolNo);
# print_r($dorm_info);

$areaNo = $dorm_info['data']['areaNo'];
$buildNo = $dorm_info['data']['buildNo'];
$dormNo = $dorm_info['data']['dormNo'];

// 获取室友列表
$roommate = $yunmei->getRoommate($serverUrl, $userId, $token, $schoolNo, $areaNo, $buildNo, $dormNo);
print_r($roommate);