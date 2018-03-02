<?php
$a;
echo 123;
echo $a->mobile;
echo 234;
exit;





ini_set('date.timezone','Asia/Shanghai');
$ini = parse_ini_file(dirname(dirname(__FILE__)).'/lib/config.ini');
$ini2 = parse_ini_file('lib/configcallback.ini');
//获取get参数中的openid
$shop = 0;
if(isset($_GET['shop'])){
    $shop = $_GET['shop'];
}
else{
    echo "非法操作";exit;
}



include 'lib/database.php';
$db = new DataHandle();

$appid = $ini['appid'];
$appsecret = $ini['secrit'];
$redirect_uri = urlencode($ini2['redirect_uri']);
$debug = $ini2['debug'];
$url = sprintf("http://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_userinfo&state=%s#wechat_redirect",$appid,$redirect_uri,$shop);

if($debug==0){
    require_once dirname(dirname(__FILE__))."/lib/jssdk.php";
    $jssdk = new JSSDK($appid, $appsecret);
    $signPackage = $jssdk->GetSignPackage();
}

@session_start();
if($debug==1){
    $_SESSION['sealy_openid'] = 'test1';
    $_SESSION['sealy_nickname'] = 'ssss';
    $_SESSION['sealy_headimgurl'] = 'img/adapter.jpg';
    $_SESSION['sealy_country'] = '中国';
    $_SESSION['sealy_area'] = '上海';
    $_SESSION['sealy_city'] = '上海市';
    $_SESSION['sealy_sex'] = 1;
}
// echo $_SESSION['sealy_nickname'];
if (!isset($_SESSION['sealy_openid'])) {
    header("Location:".$url);exit;
}

$id = 0;
$chk_re = $db->checkByOpenId($_SESSION['sealy_openid']);
$need_res = 1;//1=已绑定，0=未绑定
if ($chk_re){
    $id = $db->insertData($chk_re[0]['u_id'], $_SESSION['sealy_openid'], urlencode($_SESSION['sealy_nickname']),$_SESSION['sealy_headimgurl'],$_SESSION['sealy_country'],$_SESSION['sealy_area'],$_SESSION['sealy_city'],$_SESSION['sealy_sex']);
}
else{
    //首次
    $id = $db->insertData(null,$_SESSION['sealy_openid'], urlencode($_SESSION['sealy_nickname']),$_SESSION['sealy_headimgurl'],$_SESSION['sealy_country'],$_SESSION['sealy_area'],$_SESSION['sealy_city'],$_SESSION['sealy_sex']);
}
$_SESSION['sealy_id'] = $id;



//保存扫码绑定商店
$re_shops = $db->saveShop($shop, $_SESSION['sealy_id']);


$date = date("Y-m-d");
$x = 0;
if($date>='2016-11-01'&&$date<='2016-11-11'){
    $x = 1;
}
if($date>='2017-03-17'&&$date<='2017-04-09'){
    $x = 2;
}

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport"content="width=640,user-scalable=no"/>
<title>welcome</title>
<link rel="stylesheet" type="text/css" href="css/common.css" />

<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="js/index.js"></script>

<?php if($debug==0): ?>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script>
wx.config({
    //debug:true,
    appId: '<?php echo $signPackage["appId"];?>',
    timestamp: <?php echo $signPackage["timestamp"];?>,
    nonceStr: '<?php echo $signPackage["nonceStr"];?>',
    signature: '<?php echo $signPackage["signature"];?>',
    jsApiList: [
      // 所有要调用的 API 都要加到这个列表中
      'hideOptionMenu'
    ]
});
</script>
<script>
wx.ready(function () {  
	wx.hideOptionMenu();
});
</script>
<?php endif; ?>

</head>

<body style="<?php if($x==2): ?>background:url(img/bg_1703.jpg) no-repeat center bottom;<?php endif; ?>">
	<!-- <div class="sealy">
    	<div class="logo"><img src="images/logo.png"></div>
        <div class="txt"><img src="images/txt.png"></div>
        <div class="wechat"><img src="images/wechat.png"></div>
    </div> -->

<!-- top page -->
<div class="pages page01">
    <?php if($x==2): ?>
    <div class="global_img logo" style="width:91px;"><img src="img/logo_1703.png"></div>
    <?php else: ?>
    <div class="global_img logo"><img src="img/logo.png"></div>
    <?php endif; ?>


    <?php if($x==1): ?>
    <div class="global_img slogan"><img src="img/slogan.png"></div>
    <?php elseif($x==2): ?>
    <div class="global_img slogan event_2"><img src="img/slogan_1703.png"></div>
    <?php else: ?>
    <div class="global_img slogan"><img src="img/slogan_ori.png"></div>
    <?php endif; ?>

    <?php if($x!=2): ?>
    <div class="global_img event"><img src="img/event.png"></div>
    <?php endif; ?>

    <div class="tables">
    <table width="100%">
        <tr>
            <td width="20%">姓名:</td>
            <td><input type="text" id="u_name"></td>
        </tr>
        <tr>
            <td>手机:</td>
            <td><input type="text" id="u_mobile"></td>
        </tr>
        <tr>
            <td>住址:<br><span>(礼品寄送地址)</span></td>
            <td><input type="text" id="u_address"></td>
        </tr>
    </table>
    </div>

    <div class="global_img qr_btn"><img src="img/btn.png"></div>
</div>
<!-- /top page -->


<!-- Wechat QR -->
<div class="wechats dno">
<div class="wechat">
    <div class="wehat_td">
    <div class="wechat_con">
        <img src="img/qr.png" class="qr" style="display:none;">
        <img src="img/qr0_1703.png" class="qr0" style="display:none;">
        <img src="img/qr1.png" class="qr1" style="display:none;">
        <img src="img/qr2.png" class="qr2" style="display:none;">
        <img src="img/qr3.png" class="qr3" style="display:none;">
        <img src="img/qr4.png" class="qr4" style="display:none;">
        <img src="img/qr6.png" class="qr6" style="display:none;">
        <img src="img/qr7.png" class="qr7" style="display:none;">
        <div class="global_img close close_wechat"><img src="img/close.png"></div>
    </div>
    </div>
</div>
</div>
<!-- /Wechat QR -->


<div class="rules dno">
<div class="rule">
    <div class="rule_td">
    <div class="rule_con">
        <?php if($x==1): ?>
        <img src="img/alert.png">
        <?php elseif($x==2): ?>
        <img src="img/alert_1703.png">
        <?php else: ?>
        <img src="img/alert_ori.png">
        <?php endif; ?>
        <div class="global_img close close_rule"><img src="img/close.png"></div>
    </div>
    </div>
</div>
</div>

</body>

</html>