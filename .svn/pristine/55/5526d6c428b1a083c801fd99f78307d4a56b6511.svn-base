<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Excel_parser
{
    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->library('Excel');
    }
    
    
    public function parse_file($file = NULL, $params = NULL, $sheet = NULL)
    {
        try
        {
            $objPHPExcel = new PHPExcel();

            $inputFileType  = PHPExcel_IOFactory::identify($file);
            $objReader      = PHPExcel_IOFactory::createReader($inputFileType);
            $objReader->setReadDataOnly(true);

            if(!EMPTY($sheet))
            {
                $objReader->setLoadSheetsOnly($sheet);
            }

            $objPHPExcel    = $objReader->load($file);

            $raw_data       = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);

            foreach ($raw_data as $no => $row) {
                foreach ($row as $key => $value) {
                    if (isset($value) && $value == '#VALUE!') {
                        $raw_data[$no][$key] = $objPHPExcel->getActiveSheet()->getCell($key.$no)->getOldCalculatedValue();
                    }
                }
            }
            
            $data           = array(1, $raw_data);
            $table_name     = $objPHPExcel->getSheetNames();
            
            $header         = array();
            $body           = array();

            if($data[0]==1){

                $ctr1 = -1;
                
                foreach($data[1] AS $key => $row){
                    
                    $ctr2 = 0;

                    foreach($row AS $key2 => $column){
                        
                        if($key == 1){
                            $header[] = $column;
                        }else{
                            $body[$ctr1][$ctr2] = trim($column);
                        }

                        $ctr2++;
                    }
                
                $ctr1++;
                }
            }

            return $response = array(
                'table'    => $table_name[0],
                'header'   => $header,
                'body'     => $body 
            );

        }
        catch (PDOException $e)
        {
            throw $e;
        }
        catch(Exception $e)
        {
            throw $e;
        }
    }    
}