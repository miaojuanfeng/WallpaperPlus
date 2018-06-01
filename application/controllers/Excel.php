<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Excel extends CI_Controller {

    private $vendor_cache = array();
    private $unit_cache = array();
    private $size_cache = array();

	public function __construct()
	{
		parent::__construct();
		check_session_timeout();
		check_is_login();
		convert_get_slashes_pretty_link();
//		check_permission();

        $this->load->database('default');

		$this->load->library('PHPExcel');
		$this->load->model('vendor_model');
		$this->load->model('unit_model');
        $this->load->model('attribute_model');
        $this->load->model('product_model');
        $this->load->model('z_product_attribute_model');

        /* vendor */
        $thisSelect = array(
            'return' => 'result'
        );
        $data['vendor'] = $this->vendor_model->select($thisSelect);
        foreach ($data['vendor'] as $key => $value){
            $this->vendor_cache[$value->vendor_company_code.'-'.$value->vendor_company_name] = $value->vendor_id;
        }

        /* unit */
        $thisSelect = array(
            'return' => 'result'
        );
        $data['unit'] = $this->unit_model->select($thisSelect);
        foreach ($data['unit'] as $key => $value){
            $this->unit_cache[$value->unit_name] = $value->unit_id;
        }

        /* size */
        $thisSelect = array(
            'where' => array('attribute_type' => 'size'),
            'return' => 'result'
        );
        $data['size'] = $this->attribute_model->select($thisSelect);
        foreach ($data['size'] as $key => $value){
            $this->size_cache[$value->attribute_name] = $value->attribute_id;
        }
	}

    /*
     * 获取vendor_id
     * @param $vendor_company
     * @return $vendor_id
     */
	private function get_vendor_id($vendor_company){
        $vendor_company_code = explode('-', $vendor_company)[0];
        $vendor_company_name = explode('-', $vendor_company)[1];
        if( isset($this->vendor_cache[$vendor_company]) ){
            $vendor_id = $this->vendor_cache[$vendor_company];
        }else{
            $vendor = array(
                'vendor_company_code' => $vendor_company_code,
                'vendor_company_name' => $vendor_company_name,
                'vendor_currency_id' => 1
            );
            $vendor_id = $this->vendor_model->insert($vendor);
            $this->vendor_cache[$vendor_company] = $vendor_id;
        }
        return $vendor_id;
    }

    /*
     * 获取unit_id
     * @param $unit_name
     * @return $unit_id
     */
    private function get_unit_id($unit_name){
        if( isset($this->unit_cache[$unit_name]) ){
            $unit_id = $this->unit_cache[$unit_name];
        }else{
            $unit = array(
                'unit_name' => $unit_name
            );
            $unit_id = $this->unit_model->insert($unit);
            $this->unit_cache[$unit_name] = $unit_id;
        }
        return $unit_id;
    }

    /*
     * 获取size_id
     * @param $attribute_name
     * @return $attribute_id
     */
    private function get_size_id($attribute_name){
        if( isset($this->size_cache[$attribute_name]) ){
            $size_id = $this->size_cache[$attribute_name];
        }else{
            $size = array(
                'attribute_name' => $attribute_name,
                'attribute_type' => 'size'
            );
            $size_id = $this->attribute_model->insert($size);
            $this->size_cache[$attribute_name] = $size_id;
        }
        return $size_id;
    }

    private function excel_get_value($filename, $encode='utf-8') {

        ini_set('memory_limit', '5000M');
        ini_set('max_execution_time', '0');

        $objReader = PHPExcel_IOFactory::createReader('Excel5');

        $objReader->setReadDataOnly(true);

        $objPHPExcel = $objReader->load($filename);

        $objWorksheet = $objPHPExcel->getActiveSheet();

        $highestRow = $objWorksheet->getHighestRow();
        $highestColumn = $objWorksheet->getHighestColumn();
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
        $excelData = array();
        for ($row = 1; $row <= $highestRow; $row++) {
            for ($col = 0; $col < $highestColumnIndex; $col++) {
                $excelData[$row][] =(string)$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
            }
        }
        return $excelData;
    }

	public function clean()
    {
        $this->db->query("delete from vendor where 1");
        $this->db->query("alter table vendor AUTO_INCREMENT=1");
        $this->db->query("delete from product where 1");
        $this->db->query("alter table product AUTO_INCREMENT=1");
        $this->db->query("delete from unit where 1");
        $this->db->query("alter table unit AUTO_INCREMENT=1");
        $this->db->query("delete from attribute where attribute_type = 'size'");
        $this->db->query("delete from z_product_attribute where 1");
    }

	public function import()
    {
        $thisGET = $this->uri->uri_to_assoc(3);
        if( isset($thisGET['excel_name']) ){
            ob_end_clean();
            echo str_pad(" ", 256);
            $this->$thisGET['excel_name']();
        }else{
            echo "Require parameter 'excel_name'";
        }
    }

    private function Import_All(){
        $this->Edition_All();
        $this->Wallpaper_All();
    }

    private function Edition_All(){
        $this->Edition_WP();
        $this->Edition_FB();
        $this->Edition_Vinyl();
        $this->Edition_Leather();
    }

    private function Edition_WP(){
        echo "Edition_WP<br/>";
        $data = $this->excel_get_value("excel/Edition_WP.xls");
//        echo "<pre>";
//        var_dump($data);
        foreach ($data as $key => $value){
            /*
             * save product
             */
            $product = array(
                'product_category_id' => 12,
                'product_vendor_id' => $this->get_vendor_id($value[0]),
                'product_code' => $value[1],
                'product_name' => $value[2],
                'product_wpp_code' => $value[3],
                'product_repeat' => $value[5],
                'product_cost' => $value[6],
                'product_price_hkd' => $value[6],
                'product_unit_id' => $this->get_unit_id($value[7]),
                'product_weight' => $value[8],
                'product_remark' => $value[9],
                'product_surcharge' => $value[10]
            );
            $product_id = $this->product_model->insert($product);
            /*
             * save size
             */
            $size = array(
                'product_id' => $product_id,
                'z_product_attribute_attribute_id' => array($this->get_size_id($value[4]))
            );
            $this->z_product_attribute_model->delete($size);
            $this->z_product_attribute_model->insert($size);

            echo ($key)." ";
            flush();
        }
        echo "<br/>OK<br/>";
    }

    private function Edition_FB(){
        echo "Edition_FB<br/>";
        $data = $this->excel_get_value("excel/Edition_FB.xls");
//        echo "<pre>";
//        var_dump($data);
        foreach ($data as $key => $value){
            /*
             * save product
             */
            $product = array(
                'product_category_id' => 13,
                'product_vendor_id' => $this->get_vendor_id($value[0]),
                'product_code' => $value[1],
                'product_name' => $value[2],
                'product_wpp_code' => $value[3],
                'product_repeat' => $value[5],
                'product_cost' => $value[6],
                'product_price_hkd' => $value[6],
                'product_unit_id' => $this->get_unit_id($value[7]),
                'product_weight' => $value[8],
                'product_remark' => $value[9],
                'product_surcharge' => $value[10]
            );
            $product_id = $this->product_model->insert($product);
            /*
             * save size
             */
            $size = array(
                'product_id' => $product_id,
                'z_product_attribute_attribute_id' => array($this->get_size_id($value[4]))
            );
            $this->z_product_attribute_model->delete($size);
            $this->z_product_attribute_model->insert($size);

            echo ($key)." ";
            flush();
        }
        echo "<br/>OK<br/>";
    }

    private function Edition_Vinyl(){
        echo "Edition_Vinyl<br/>";
        $data = $this->excel_get_value("excel/Edition_Vinyl.xls");
//        echo "<pre>";
//        var_dump($data);
        foreach ($data as $key => $value){
            /*
             * save product
             */
            $product = array(
                'product_category_id' => 14,
                'product_vendor_id' => $this->get_vendor_id($value[0]),
                'product_code' => $value[1],
                'product_name' => $value[2],
                'product_wpp_code' => $value[3],
                'product_repeat' => $value[5],
                'product_cost' => $value[6],
                'product_price_hkd' => $value[6],
                'product_unit_id' => $this->get_unit_id($value[7]),
                'product_weight' => $value[8],
                'product_remark' => $value[9],
                'product_surcharge' => $value[10]
            );
            $product_id = $this->product_model->insert($product);
            /*
             * save size
             */
            $size = array(
                'product_id' => $product_id,
                'z_product_attribute_attribute_id' => array($this->get_size_id($value[4]))
            );
            $this->z_product_attribute_model->delete($size);
            $this->z_product_attribute_model->insert($size);

            echo ($key)." ";
            flush();
        }
        echo "<br/>OK<br/>";
    }

    private function Edition_Leather(){
        echo "Edition_Leather<br/>";
        $data = $this->excel_get_value("excel/Edition_Leather.xls");
//        echo "<pre>";
//        var_dump($data);
        foreach ($data as $key => $value){
            /*
             * save product
             */
            $product = array(
                'product_category_id' => 15,
                'product_vendor_id' => $this->get_vendor_id($value[0]),
                'product_code' => $value[1],
                'product_name' => $value[2],
                'product_wpp_code' => $value[3],
                'product_repeat' => $value[5],
                'product_cost' => $value[6],
                'product_price_hkd' => $value[6],
                'product_unit_id' => $this->get_unit_id($value[7]),
                'product_weight' => $value[8],
                'product_remark' => $value[9],
                'product_surcharge' => $value[10]
            );
            $product_id = $this->product_model->insert($product);
            /*
             * save size
             */
            $size = array(
                'product_id' => $product_id,
                'z_product_attribute_attribute_id' => array($this->get_size_id($value[4]))
            );
            $this->z_product_attribute_model->delete($size);
            $this->z_product_attribute_model->insert($size);

            echo ($key)." ";
            flush();
        }
        echo "<br/>OK<br/>";
    }

    //////////////////////////
    ///
    ///
    ///
    ///
    ///
    ///
    ///
    ///
    //////////////////////////

    private function Wallpaper_All(){
        $this->Vinyl();
        $this->Leather();
        $this->Carpet();
        $this->Cushion();
        $this->Fireproof();
        $this->Skin();
        $this->Other();
        $this->Fabric();
        $this->Wallpaper();
    }

    private function Wallpaper(){
        echo "Wallpaper<br/>";
        $data = $this->excel_get_value("excel/Wallpaper.xls");
//        echo "<pre>";
//        var_dump($data[1]);
        foreach ($data as $key => $value){
            /*
             * save product
             */
            $product = array(
                'product_category_id' => 3,
                'product_vendor_id' => $this->get_vendor_id($value[0]),
                'product_code' => $value[1],
                'product_name' => $value[2],
                'product_wpp_code' => $value[3],
                'product_repeat' => $value[5],
                'product_cost' => $value[6],
                'product_price_hkd' => $value[6],
                'product_unit_id' => $this->get_unit_id($value[7]),
                'product_weight' => $value[8],
                'product_remark' => $value[9],
                'product_surcharge' => $value[10]
            );
            $product_id = $this->product_model->insert($product);
            /*
             * save size
             */
            $size = array(
                'product_id' => $product_id,
                'z_product_attribute_attribute_id' => array($this->get_size_id($value[4]))
            );
            $this->z_product_attribute_model->delete($size);
            $this->z_product_attribute_model->insert($size);

            echo ($key)." ";
            flush();
        }
        echo "<br/>OK<br/>";
    }

    private function Fabric(){
        echo "Fabric<br/>";
        $data = $this->excel_get_value("excel/Fabric.xls");
//        echo "<pre>";
//        var_dump($data[1]);
        foreach ($data as $key => $value){
            /*
             * save product
             */
            $product = array(
                'product_category_id' => 4,
                'product_vendor_id' => $this->get_vendor_id($value[0]),
                'product_code' => $value[1],
                'product_name' => $value[2],
                'product_wpp_code' => $value[3],
                'product_repeat' => $value[5],
                'product_cost' => $value[6],
                'product_price_hkd' => $value[6],
                'product_unit_id' => $this->get_unit_id($value[7]),
                'product_weight' => $value[8],
                'product_remark' => $value[9],
                'product_surcharge' => $value[10]
            );
            $product_id = $this->product_model->insert($product);
            /*
             * save size
             */
            $size = array(
                'product_id' => $product_id,
                'z_product_attribute_attribute_id' => array($this->get_size_id($value[4]))
            );
            $this->z_product_attribute_model->delete($size);
            $this->z_product_attribute_model->insert($size);

            echo ($key)." ";
            flush();
        }
        echo "<br/>OK<br/>";
    }

    private function Vinyl(){
        echo "Vinyl<br/>";
        $data = $this->excel_get_value("excel/Vinyl.xls");
//        echo "<pre>";
//        var_dump($data[1]);
        foreach ($data as $key => $value){
            /*
             * save product
             */
            $product = array(
                'product_category_id' => 5,
                'product_vendor_id' => $this->get_vendor_id($value[0]),
                'product_code' => $value[1],
                'product_name' => $value[2],
                'product_wpp_code' => $value[3],
                'product_repeat' => $value[5],
                'product_cost' => $value[6],
                'product_price_hkd' => $value[6],
                'product_unit_id' => $this->get_unit_id($value[7]),
                'product_weight' => $value[8],
                'product_remark' => $value[9],
                'product_surcharge' => $value[10]
            );
            $product_id = $this->product_model->insert($product);
            /*
             * save size
             */
            $size = array(
                'product_id' => $product_id,
                'z_product_attribute_attribute_id' => array($this->get_size_id($value[4]))
            );
            $this->z_product_attribute_model->delete($size);
            $this->z_product_attribute_model->insert($size);

            echo ($key)." ";
            flush();
        }
        echo "<br/>OK<br/>";
    }

    private function Leather(){
        echo "Leather<br/>";
        $data = $this->excel_get_value("excel/Leather.xls");
//        echo "<pre>";
//        var_dump($data[1]);
        foreach ($data as $key => $value){
            /*
             * save product
             */
            $product = array(
                'product_category_id' => 6,
                'product_vendor_id' => $this->get_vendor_id($value[0]),
                'product_code' => $value[1],
                'product_name' => $value[2],
                'product_wpp_code' => $value[3],
                'product_repeat' => $value[5],
                'product_cost' => $value[6],
                'product_price_hkd' => $value[6],
                'product_unit_id' => $this->get_unit_id($value[7]),
                'product_weight' => $value[8],
                'product_remark' => $value[9],
                'product_surcharge' => $value[10]
            );
            $product_id = $this->product_model->insert($product);
            /*
             * save size
             */
            $size = array(
                'product_id' => $product_id,
                'z_product_attribute_attribute_id' => array($this->get_size_id($value[4]))
            );
            $this->z_product_attribute_model->delete($size);
            $this->z_product_attribute_model->insert($size);

            echo ($key)." ";
            flush();
        }
        echo "<br/>OK<br/>";
    }

    private function Carpet(){
        echo "Carpet<br/>";
        $data = $this->excel_get_value("excel/Carpet.xls");
//        echo "<pre>";
//        var_dump($data[1]);
        foreach ($data as $key => $value){
            /*
             * save product
             */
            $product = array(
                'product_category_id' => 7,
                'product_vendor_id' => $this->get_vendor_id($value[0]),
                'product_code' => $value[1],
                'product_name' => $value[2],
                'product_wpp_code' => $value[3],
                'product_repeat' => $value[5],
                'product_cost' => $value[6],
                'product_price_hkd' => $value[6],
                'product_unit_id' => $this->get_unit_id($value[7]),
                'product_weight' => $value[8],
                'product_remark' => $value[9],
                'product_surcharge' => $value[10]
            );
            $product_id = $this->product_model->insert($product);
            /*
             * save size
             */
            $size = array(
                'product_id' => $product_id,
                'z_product_attribute_attribute_id' => array($this->get_size_id($value[4]))
            );
            $this->z_product_attribute_model->delete($size);
            $this->z_product_attribute_model->insert($size);

            echo ($key)." ";
            flush();
        }
        echo "<br/>OK<br/>";
    }

    private function Cushion(){
        echo "Cushion<br/>";
        $data = $this->excel_get_value("excel/Cushion.xls");
//        echo "<pre>";
//        var_dump($data[1]);
        foreach ($data as $key => $value){
            /*
             * save product
             */
            $product = array(
                'product_category_id' => 8,
                'product_vendor_id' => $this->get_vendor_id($value[0]),
                'product_code' => $value[1],
                'product_name' => $value[2],
                'product_wpp_code' => $value[3],
                'product_repeat' => $value[5],
                'product_cost' => $value[6],
                'product_price_hkd' => $value[6],
                'product_unit_id' => $this->get_unit_id($value[7]),
                'product_weight' => $value[8],
                'product_remark' => $value[9],
                'product_surcharge' => $value[10]
            );
            $product_id = $this->product_model->insert($product);
            /*
             * save size
             */
            $size = array(
                'product_id' => $product_id,
                'z_product_attribute_attribute_id' => array($this->get_size_id($value[4]))
            );
            $this->z_product_attribute_model->delete($size);
            $this->z_product_attribute_model->insert($size);

            echo ($key)." ";
            flush();
        }
        echo "<br/>OK<br/>";
    }

    private function Fireproof(){
        echo "Fireproof<br/>";
        $data = $this->excel_get_value("excel/Fireproof.xls");
//        echo "<pre>";
//        var_dump($data[1]);
        foreach ($data as $key => $value){
            /*
             * save product
             */
            $product = array(
                'product_category_id' => 9,
                'product_vendor_id' => $this->get_vendor_id($value[0]),
                'product_code' => $value[1],
                'product_name' => $value[2],
                'product_wpp_code' => $value[3],
                'product_repeat' => $value[5],
                'product_cost' => $value[6],
                'product_price_hkd' => $value[6],
                'product_unit_id' => $this->get_unit_id($value[7]),
                'product_weight' => $value[8],
                'product_remark' => $value[9],
                'product_surcharge' => $value[10]
            );
            $product_id = $this->product_model->insert($product);
            /*
             * save size
             */
            $size = array(
                'product_id' => $product_id,
                'z_product_attribute_attribute_id' => array($this->get_size_id($value[4]))
            );
            $this->z_product_attribute_model->delete($size);
            $this->z_product_attribute_model->insert($size);

            echo ($key)." ";
            flush();
        }
        echo "<br/>OK<br/>";
    }

    private function Skin(){
        echo "Skin<br/>";
        $data = $this->excel_get_value("excel/Skin.xls");
//        echo "<pre>";
//        var_dump($data[1]);
        foreach ($data as $key => $value){
            /*
             * save product
             */
            $product = array(
                'product_category_id' => 10,
                'product_vendor_id' => $this->get_vendor_id($value[0]),
                'product_code' => $value[1],
                'product_name' => $value[2],
                'product_wpp_code' => $value[3],
                'product_repeat' => $value[5],
                'product_cost' => $value[6],
                'product_price_hkd' => $value[6],
                'product_unit_id' => $this->get_unit_id($value[7]),
                'product_weight' => $value[8],
                'product_remark' => $value[9],
                'product_surcharge' => $value[10]
            );
            $product_id = $this->product_model->insert($product);
            /*
             * save size
             */
            $size = array(
                'product_id' => $product_id,
                'z_product_attribute_attribute_id' => array($this->get_size_id($value[4]))
            );
            $this->z_product_attribute_model->delete($size);
            $this->z_product_attribute_model->insert($size);

            echo ($key)." ";
            flush();
        }
        echo "<br/>OK<br/>";
    }

    private function Other(){
        echo "Other<br/>";
        $data = $this->excel_get_value("excel/Other.xls");
//        echo "<pre>";
//        var_dump($data[1]);
        foreach ($data as $key => $value){
            /*
             * save product
             */
            $product = array(
                'product_category_id' => 11,
                'product_vendor_id' => $this->get_vendor_id($value[0]),
                'product_code' => $value[1],
                'product_name' => $value[2],
                'product_wpp_code' => $value[3],
                'product_repeat' => $value[5],
                'product_cost' => $value[6],
                'product_price_hkd' => $value[6],
                'product_unit_id' => $this->get_unit_id($value[7]),
                'product_weight' => $value[8],
                'product_remark' => $value[9],
                'product_surcharge' => $value[10]
            );
            $product_id = $this->product_model->insert($product);
            /*
             * save size
             */
            $size = array(
                'product_id' => $product_id,
                'z_product_attribute_attribute_id' => array($this->get_size_id($value[4]))
            );
            $this->z_product_attribute_model->delete($size);
            $this->z_product_attribute_model->insert($size);

            echo ($key)." ";
            flush();
        }
        echo "<br/>OK<br/>";
    }

}
