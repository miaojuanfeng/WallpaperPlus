<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Excel extends CI_Controller {

    private $vendor_cache = array();
    private $unit_cache = array();

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
        $this->load->model('product_model');
	}

	public function index()
	{

	}

	public function update()
	{
		// update here
	}

	public function delete()
	{
		// delete here
	}

	public function insert()
	{

	}

	public function select()
	{

	}

	public function  import()
    {
        $this->db->query("delete from vendor where 1");
        $this->db->query("alter table vendor AUTO_INCREMENT=1");
        $this->db->query("delete from product where 1");
        $this->db->query("alter table product AUTO_INCREMENT=1");
        $this->db->query("delete from unit where 1");
        $this->db->query("alter table unit AUTO_INCREMENT=1");

        $this->p1();
//        $this->p2();
//        $this->p3();
//        $this->p4();
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

    private function p1(){
        $data = $this->excel_get_value("p1.xls");
        echo "<pre>";
        var_dump($data);
        foreach ($data as $key => $value){
            /*
             * 获取vendor_id
             */
            $vendor_company = $value[0];
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
            /*
             * 获取unit
             */
            $product_unit = '';
            if( empty($product_unit) || !is_string($product_unit) ){
                $unit_id = 0;
            }else if( isset($this->unit_cache[$product_unit]) ){
                $unit_id = $this->unit_cache[$product_unit];
            }else{
                $unit = array(
                    'unit_name' => $product_unit
                );
                $unit_id = $this->unit_model->insert($unit);
                $this->unit_cache[$product_unit] = $unit_id;
            }
            /*
             * 获取product
             */
            //还未保存size
            $product = array(
                'product_vendor_id' => $vendor_id,
                'product_code' => $value[1],
                'product_name' => $value[2],
                'product_wpp_code' => '',
                'product_cost' => $value[4],
                'product_price_hkd' => $value[4],
                'product_unit_id' => $unit_id,
                'product_weight' => $value[11].'Kg',
                'product_detail' => $value[6]
            );
            $this->product_model->insert($product);
            break;
        }
    }

    private function p2(){
        $data = $this->excel_get_value("p2.xls");
        echo "<pre>";
//        var_dump($data);
        foreach ($data as $key => $value){
            /*
             * 获取vendor_id
             */
            $vendor_company = $value[0];
            $vendor_company_code = $value[0];
            $vendor_company_name = $value[1];
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
            /*
             * 获取unit
             */
            $product_unit = $value[12];
            if( empty($product_unit) || !is_string($product_unit) ){
                $unit_id = 0;
            }else if( isset($this->unit_cache[$product_unit]) ){
                $unit_id = $this->unit_cache[$product_unit];
            }else{
                $unit = array(
                    'unit_name' => $product_unit
                );
                $unit_id = $this->unit_model->insert($unit);
                $this->unit_cache[$product_unit] = $unit_id;
            }
            /*
             * 获取product
             */
            //还未保存size
            $product = array(
                'product_vendor_id' => $vendor_id,
                'product_code' => $value[2],
                'product_name' => $value[4],
                'product_wpp_code' => $value[13],
                'product_cost' => $value[11],
                'product_price_hkd' => $value[11],
                'product_unit_id' => $unit_id,
                'product_weight' => $value[10].'Kg',
                'product_detail' => $value[13]."\n\n".$value[14]
            );
            $this->product_model->insert($product);
            break;
        }
    }

    private function p3(){
        $data = $this->excel_get_value("p3.xls");
        echo "<pre>";
//        var_dump($data);
        foreach ($data as $key => $value){
            /*
             * 获取vendor_id
             */
            $vendor_company = $value[0];
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
            /*
             * 获取unit
             */
            $product_unit = $value[9];
            if( empty($product_unit) || !is_string($product_unit) ){
                $unit_id = 0;
            }else if( isset($this->unit_cache[$product_unit]) ){
                $unit_id = $this->unit_cache[$product_unit];
            }else{
                $unit = array(
                    'unit_name' => $product_unit
                );
                $unit_id = $this->unit_model->insert($unit);
                $this->unit_cache[$product_unit] = $unit_id;
            }
            /*
             * 获取product
             */
            //还未保存size
            $product = array(
                'product_vendor_id' => $vendor_id,
                'product_code' => $value[1],
                'product_name' => $value[2],
                'product_wpp_code' => $value[3],
                'product_cost' => $value[8],
                'product_price_hkd' => $value[8],
                'product_unit_id' => $unit_id,
                'product_weight' => $value[10].'Kg',
                'product_detail' => $value[12]."\n\n".$value[13]
            );
            $this->product_model->insert($product);
            break;
        }
    }

    private function p4(){
	    $data = $this->excel_get_value("p4.xls");
        echo "<pre>";
//        var_dump($data);
        foreach ($data as $key => $value){
            /*
             * 获取vendor_id
             */
            $vendor_company = $value[0];
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
            /*
             * 获取unit
             */
            $product_unit = $value[7];
            if( isset($this->unit_cache[$product_unit]) ){
                $unit_id = $this->unit_cache[$product_unit];
            }else{
                $unit = array(
                    'unit_name' => $product_unit
                );
                $unit_id = $this->unit_model->insert($unit);
                $this->unit_cache[$product_unit] = $unit_id;
            }
            /*
             * 获取product
             */
            //还未保存size
            $product = array(
                'product_vendor_id' => $vendor_id,
                'product_code' => $value[1],
                'product_name' => $value[2],
                'product_wpp_code' => $value[3],
                'product_cost' => $value[6],
                'product_price_hkd' => $value[6],
                'product_unit_id' => $unit_id,
                'product_weight' => $value[8].'Kg',
                'product_detail' => $value[9]."\n\n".$value[10]
            );
            $this->product_model->insert($product);
        }
    }

}
