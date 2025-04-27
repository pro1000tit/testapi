<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
class ControllerCatalogTmp extends Controller
{
    private $error = array();


    public function info(){
        $data = array();
        $url = '';


        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => 'Результат последнего обновления',
            'href' => $this->url->link('catalog/tmp', 'token=' . $this->session->data['token'] . $url, true)
        );

        $this->document->setTitle('Результат последнего обновления');

        $q = $this->db->query("SELECT * FROM j_status ORDER by id DESC LIMIT 1");
        $data['result11'] = unserialize($q->row['result']);

        $data['date'] = $q->row['date'];


        $data['token'] = $this->session->data['token'];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('catalog/work_result', $data));
    }
    public function index()
    {
        $data = array();
        $url = '';


        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => 'Связи категорий',
            'href' => $this->url->link('catalog/tmp', 'token=' . $this->session->data['token'] . $url, true)
        );

        $this->document->setTitle('Связи категорий');

        if (isset($this->request->get['cat_id'])) {
            $cat_id = $this->request->get['cat_id'];
        } else {
            $cat_id = 0;
        }

        $this->load->model('catalog/work');
        $this->load->model('catalog/category');
        $data['categories'] = array();

        $data['categories'] = $this->getcat($cat_id);

//print_r( $data['categories']);
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

//1eb96303397600eb82c1025c4fe3ea0b27cfa404

        $process = curl_init("https://b2bapi.onliner.by/sections?access_token=".$token);
        curl_setopt($process, CURLOPT_HTTPHEADER, array('Accept: application/json'));

        curl_setopt($process, CURLOPT_POST, 0);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
//curl_setopt($process, CURLOPT_POSTFIELDS, array('grant_type' => 'client_credentials'));
        $result = curl_exec($process);
        curl_close($process);

$data['text_list'] = 'Связи категорий';

        foreach (json_decode($result,true) AS $key=>$value){
            $data['onliner_cat'][] = array('id'=>$key, 'name'=>$value);
        }



        /*        foreach ( $result as $r ) {
                    $r_parent = $this->model_catalog_work->getCat( $r['id'] );
                    if ( count( $r_parent ) > 0 ) {
                        $href = $this->url->link( 'catalog/work', 'token=' . $this->session->data['token'] . '&cat_id=' . $r['id'], true );
                    } else {
                        $href = '';
                    }

                    $data['cats'][] = array( 'id'       => $r['id'],
                        'name'     => $r['name'],
                        'href'     => $href,
                        'real_cat' => $r['real_cat']
                    );
                }*/

        $data['token'] = $this->session->data['token'];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('catalog/work_list', $data));

    }

    public function manu(){

    }
    public function setmanu(){
        $cat_id = $this->request->post['cat_id'];
        $m_id = $this->request->post['m_id'];
        $add = $this->request->post['add'];

        if($add ==1){
            $this->db->query("INSERT INTO `j_cat_m` (`cat_id`, `m_id`)
VALUES ('".$cat_id."', '".$m_id."');");
        }
        if($add ==0){
            $this->db->query("DELETE FROM  `j_cat_m` WHERE cat_id = '".$cat_id."' AND m_id = '".$m_id."' LIMIT 1");
        }
    }

    public function getcat($parent_id)
    {
        $q = $this->db->query("SELECT * FROM `oc_category` as c
join oc_category_description as cd on cd.category_id = c.category_id and cd.language_id=1

 WHERE c.`parent_id` = '" . $parent_id . "' order by cd.name");
        $result = array();
        foreach ($q->rows as $c) {
            $qq = $this->db->query("SELECT * FROM `oc_category` WHERE parent_id = '".$c['category_id']."' LIMIT 1");
            if($qq->num_rows==0){
                $href='';
            }
            else{
                $href=$this->url->link( 'catalog/tmp', 'token=' . $this->session->data['token'] . '&cat_id=' . $c['category_id'], true );
            }

            $result[] = array('id' => $c['category_id'],
                'href'=>$href,
                'onliner_id' => isset($c['onliner_id']) ? $c['onliner_id'] : null,
                'manufs'=> $this->getMan($c['category_id']),
                'name' => $c['name'], 'parent'=>$this->getcat($c['category_id']));
        }
        return $result;
    }
    public function getMan($cat_id){
        $q = $this->db->query("SELECT m.* FROM `oc_product_to_category` as ptc 
join oc_product AS p ON p.product_id = ptc.product_id
join oc_manufacturer as m ON m.manufacturer_id = p.manufacturer_id
WHERE ptc.`category_id` = '".$cat_id."' and ptc.main_category=1 group by m.manufacturer_id
order by m.name");
        $result = array();
        foreach ($q->rows AS $r){

            $qq = $this->db->query("SELECT * FROM j_cat_m WHERE cat_id = '".$cat_id."' AND m_id = '".$r['manufacturer_id']."' LIMIT 1");
            if($qq->num_rows==0){
                $add=0;
            }
            else{
                $add=1;
            }

            $result[] = array('id'=>$r['manufacturer_id'], 'name'=>$r['name'], 'add'=>$add);
        }
        return $result;
    }

    public function config()
    {
        $data['breadcrumbs'] = array();
        $url = '';

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => 'Настройки onliner.by',
            'href' => $this->url->link('catalog/tmp/config', 'token=' . $this->session->data['token'] . $url, true)
        );
        $data['action'] = $this->url->link('catalog/tmp/config', 'token=' . $this->session->data['token'], true);


        if ($this->request->server['REQUEST_METHOD'] == 'POST') {

            foreach ($_POST as $key => $p) {
                $this->db->query("DELETE FROM oc_setting WHERE `key` = '" . $key . "' LIMIT 1");
                $this->db->query("INSERT INTO `oc_setting` (`store_id`, `code`, `key`, `value`, `serialized`)
VALUES ('0', 'config', '" . $key . "', '" . $p . "', '0');");

            }


            $this->session->data['success'] = 'Сохранили';

            $this->response->redirect($this->url->link('catalog/tmp/config', 'token=' . $this->session->data['token'], true));
        }


        if (isset($this->request->post['config_deliverycity'])) {
            $data['config_deliverycity'] = $this->request->post['config_deliverycity'];
        } else {
            $data['config_deliverycity'] = $this->config->get('config_deliverycity');
        }
        if (isset($this->request->post['config_deliverycountry'])) {
            $data['config_deliverycountry'] = $this->request->post['config_deliverycountry'];
        } else {
            $data['config_deliverycountry'] = $this->config->get('config_deliverycountry');
        }

        if (isset($this->request->post['config_comment'])) {
            $data['config_comment'] = $this->request->post['config_comment'];
        } else {
            $data['config_comment'] = $this->config->get('config_comment');
        }

        if (isset($this->request->post['config_clientid'])) {
            $data['config_clientid'] = $this->request->post['config_clientid'];
        } else {
            $data['config_clientid'] = $this->config->get('config_clientid');
        }
        if (isset($this->request->post['config_cliensecret'])) {
            $data['config_cliensecret'] = $this->request->post['config_cliensecret'];
        } else {
            $data['config_cliensecret'] = $this->config->get('config_cliensecret');
        }

        if (isset($this->request->post['config_importer'])) {
            $data['config_importer'] = $this->request->post['config_importer'];
        } else {
            $data['config_importer'] = $this->config->get('config_importer');
        }

        if (isset($this->request->post['config_gar'])) {
            $data['config_gar'] = $this->request->post['config_gar'];
        } else {
            $data['config_gar'] = $this->config->get('config_gar');
        }

        if (isset($this->request->post['config_servis'])) {
            $data['config_servis'] = $this->request->post['config_servis'];
        } else {
            $data['config_servis'] = $this->config->get('config_servis');
        }


        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('catalog/onliner_config', $data));
    }

    public function savecat()
    {
        $cat_id = $this->request->post['cat_id'];
        $real_cat = $this->request->post['real_cat'];

        //отправим запрос


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

//1eb96303397600eb82c1025c4fe3ea0b27cfa404

        $process = curl_init("https://b2bapi.onliner.by/sections/".$real_cat."?access_token=".$token."");
        curl_setopt($process, CURLOPT_HTTPHEADER, array('Accept: application/json'));

        curl_setopt($process, CURLOPT_POST, 1);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($process, CURLOPT_POSTFIELDS, array('shopId' => 7940));
        $result = curl_exec($process);
        curl_close($process);


        print_r(json_decode($result,true));


//echo "UPDATE j_cat SET real_cat ='".$real_cat."' WHERE id = ".$cat_id." LIMIT 1";
        $this->db->query("UPDATE oc_category SET onliner_id ='" . $real_cat . "' WHERE category_id = " . $cat_id . " LIMIT 1");
        exit();
    }

    private function getCategories($parent_id, $parent_path = '', $indent = '')
    {
        $category_id = 0;
        $output = array();
        static $href_category = null;
        static $href_action = null;
        if ($href_category === null) {
            $href_category = $this->url->link('catalog/category', 'token=' . $this->session->data['token'] . '&path=', true);
            $href_action = $this->url->link('catalog/category/update', 'token=' . $this->session->data['token'] . '&category_id=', true);
        }
        $results = $this->model_catalog_category->getCategoriesByParentId($parent_id);
        foreach ($results as $result) {
            $path = $parent_path . $result['category_id'];
            $href = ($result['children']) ? $href_category . $path : '';
            $name = $result['name'];
            if ($category_id == $result['category_id']) {
                $name = '<b>' . $name . '</b>';
                $data['breadcrumbs'][] = array(
                    'text' => $result['name'],
                    'href' => $href,
                    'separator' => ' :: '
                );
                $href = '';
            }
            $selected = isset($this->request->post['selected']) && in_array($result['category_id'], $this->request->post['selected']);
            $action = array();
            $action[] = array(
                'text' => $this->language->get('text_edit'),
                'href' => $href_action . $result['category_id']
            );
            $output[$result['category_id']] = array(
                'category_id' => $result['category_id'],
                'name' => $name,
                'sort_order' => $result['sort_order'],
                'noindex' => $result['noindex'],
                'edit' => $this->url->link('catalog/category/edit', 'token=' . $this->session->data['token'] . '&category_id=' . $result['category_id'], true),
                'selected' => $selected,
                'action' => $action,
                'href' => $href,
                'href_shop' => HTTP_CATALOG . 'index.php?route=product/category&path=' . ($result['category_id']),
                'indent' => $indent
            );
            if ($category_id == $result['category_id']) {
                $output += $this->getCategories($result['category_id'], $path . '_', $indent . str_repeat('&nbsp;', 8));
            }
        }
        return $output;
    }

    private function getAllCategories($categories, $parent_id = 0, $parent_name = '')
    {
        $output = array();
        if (array_key_exists($parent_id, $categories)) {
            if ($parent_name != '') {
                $parent_name .= $this->language->get('text_separator');
            }
            foreach ($categories[$parent_id] as $category) {
                $output[$category['category_id']] = array(
                    'category_id' => $category['category_id'],
                    'name' => $parent_name . $category['name']
                );
                $output += $this->getAllCategories($categories, $category['category_id'], $parent_name . $category['name']);
            }
        }
        return $output;
    }

    function sortByName($a, $b)
    {
        return strcmp($a['name'], $b['name']);
    }

}
