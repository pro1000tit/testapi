<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('memory_limit', '512M');

define('OPENCART_ADMIN_DIR', '/var/www/www-root/data/www/dev/admin/');
define('OPENCART_DIR', '/var/www/www-root/data/www/dev/');

if (file_exists(OPENCART_ADMIN_DIR . 'config.php')) {
    require_once(OPENCART_ADMIN_DIR . 'config.php');
} else {
    die("ERROR: cli cannot access to config.php");
}
// Check VERSION
$data = file_get_contents(OPENCART_ADMIN_DIR . 'index.php');
preg_match("/define\('VERSION', '([0-9]*\.[0-9]*)/i", $data, $matches);
if (isset($matches[1])) {
    $version = $matches[1];
} else {
    $version = '2.0';
}
// Startup
require_once(DIR_SYSTEM . 'startup.php');
// Registry
$registry = new Registry();
// Loader
$loader = new Loader($registry);
$registry->set('load', $loader);
// Config
$config = new Config();
$registry->set('config', $config);
// Database
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$registry->set('db', $db);




$process = curl_init("https://b2bapi.onliner.by/oauth/token");
curl_setopt($process, CURLOPT_HTTPHEADER, array('Accept: application/json'));
curl_setopt($process, CURLOPT_USERPWD, "1067cc125029b0a0b37e:45b31b8c87a7b42d787338c6b1ed05734e39f223");
curl_setopt($process, CURLOPT_POST, 1);
curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($process, CURLOPT_POSTFIELDS, array('grant_type' => 'client_credentials'));
$result = curl_exec($process);
curl_close($process);


//print_r(json_decode($result));

$token = json_decode($result)->access_token;



/*$process = curl_init("https://b2bapi.onliner.by/pricelists/6444fe5e3f4f06b1970bad62/report?access_token=".$token);
curl_setopt($process, CURLOPT_HTTPHEADER, array('Accept: application/json'));

curl_setopt($process, CURLOPT_POST, 0);
curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
//curl_setopt($process, CURLOPT_POSTFIELDS, array('grant_type' => 'client_credentials'));
$result = curl_exec($process);
curl_close($process);

print_r(json_decode($result,true));*/


$process = curl_init("https://b2bapi.onliner.by/sections?access_token=".$token);
curl_setopt($process, CURLOPT_HTTPHEADER, array('Accept: application/json'));

curl_setopt($process, CURLOPT_POST, 0);
curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
//curl_setopt($process, CURLOPT_POSTFIELDS, array('grant_type' => 'client_credentials'));
$result = curl_exec($process);
curl_close($process);


foreach (json_decode($result,true) AS $key=>$value){
    $o_cat[$key] =$value;
}

$q = $db->query("SELECT * FROM `oc_setting` WHERE `key` = 'config_deliverycity'");

$dcity = $q->row['value'];


$q = $db->query("SELECT * FROM `oc_setting` WHERE `key` = 'config_deliverycountry'");

$dcountry = $q->row['value'];


$q = $db->query("SELECT * FROM `oc_setting` WHERE `key` = 'config_comment'");

$comment = $q->row['value'];

$q = $db->query("SELECT * FROM `oc_setting` WHERE `key` = 'config_servis'");

$servis = $q->row['value'];

$q = $db->query("SELECT * FROM `oc_setting` WHERE `key` = 'config_importer'");

$importer = $q->row['value'];

$q = $db->query("SELECT * FROM `oc_setting` WHERE `key` = 'config_gar'");

$gar = $q->row['value'];


$q = $db->query("SELECT *, pd.name AS pname, m.name as mname,
(SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id 
AND ((ps.date_start = '0' OR ps.date_start < '" . $db->escape(date('Y-m-d')) . "') 
AND (ps.date_end = '0' OR ps.date_end > '" . $db->escape(date('Y-m-d')) . "')) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special       
       
       FROM oc_product AS p 
JOIN oc_product_to_category AS ptc ON ptc.product_id = p.product_id AND ptc.main_category=1
JOIN oc_category AS c ON c.category_id = ptc.category_id AND c.onliner_id !=0
join oc_product_description AS pd ON pd.product_id = p.product_id and pd.language_id=1
join oc_manufacturer AS m on m.manufacturer_id = p.manufacturer_id
         WHERE -- c.category_id = 68
 p.quantity!=0 --  and p.model ='Stage800BA'
  -- limit 4000
");


$result = array();
foreach ($q->rows as $r) {
    //проверим производителя


    $qq = $db->query("SELECT * FROM j_cat_m WHERE cat_id = '".$r['category_id']."' AND m_id = '".$r['manufacturer_id']."' LIMIT 1");
    if($qq->num_rows !=0){

        if(!is_null($r['special'])){
            $price = $r['special'];
        }
        else{
            $price=$r['price'];
        }

        
        $result[] = array("id"=> $r['product_id'],
        "category"=> $o_cat[$r['onliner_id']],
        "vendor"=> $r['mname'],
        "model"=> trim($r['upc']), //trim(str_replace($r['mname'], "",$r['model'])),
        /*"article"=> $r['product_id'],*/

        "price"=> $price,
        "currency"=> "BYN",
"producer"=>$r['mname'],
        "isCashless"=>"нет",
        "importer"=>$importer,
"productLifeTime"=> $gar,
        "comment"=>$comment,
        "serviceCenters"=> $servis,
        "warranty"=> $gar,
        "deliveryTownTime"=> $dcity,
        "deliveryCountryTime"=> $dcountry,

        "stockStatus"=> "in_stock",

        );
    }

}

//print_r($result);exit();

$result1 = json_encode($result);


$process = curl_init("https://b2bapi.onliner.by/pricelists?access_token=" . $token );
curl_setopt($process, CURLOPT_HTTPHEADER, array(   'Accept: application/json',
    'Content-Type: application/json'));



curl_setopt($process, CURLOPT_CUSTOMREQUEST, 'PUT');
curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($process, CURLOPT_POSTFIELDS, $result1);

$results = curl_exec($process);
curl_close($process);

sleep(5);
$info = json_decode($results,true);
print_r($info);
echo '<hr>';
echo $token;
$process = curl_init("https://b2bapi.onliner.by/pricelists/".$info['id']."/report?access_token=".$token);
curl_setopt($process, CURLOPT_HTTPHEADER, array('Accept: application/json'));

curl_setopt($process, CURLOPT_POST, 0);
curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
//curl_setopt($process, CURLOPT_POSTFIELDS, array('grant_type' => 'client_credentials'));
$result = curl_exec($process);
curl_close($process);

$str = json_decode($result,true);

//print_r($str);

$db->query("INSERT INTO `j_status` (`result`, `date`)
VALUES ('".$db->escape(serialize($str))."', now());");

echo 'Готово';
