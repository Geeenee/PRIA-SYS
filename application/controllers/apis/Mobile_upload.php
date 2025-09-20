<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mobile_upload extends SYSAD_Controller{
	public function __construct(){
		parent::__construct();
		$this->upload	= modules::load('../Upload');
	}
	
	public function upload_file_to_server(){
		try{
			$params		= get_params();
			$output_dir = PATH_UPLOADED_FILES;
			$root_path 	= $this->get_root_path();
			
			$output_dir = $root_path.$output_dir;
			$output_dir = str_replace(array('/', '\\'), array(DS, DS), $output_dir);
			
			if(!is_dir($output_dir))
			{
				mkdir($output_dir,0777,TRUE);
			}
			
			$salt 	= gen_salt();

			if(ISSET($params["file"]))
			{
				$ret 	= array();
				
				$error 	= $params["file"]["error"];
				//You need to handle both cases
				//If any browser does not support serializing of multiple files using FormData()
				
				$unique_id 	= uniqid();
				
				if(!is_array($params["file"]["name"])) //single file
				{
					$fileName 	= str_replace(" ","",$params["file"]["name"]);
					$allowed 	= $this->upload->is_allowed_filetype($params, $fileName, $params["file"]["tmp_name"] );
					
					$extension 	= pathinfo($fileName, PATHINFO_EXTENSION);
					$fileName 	= pathinfo($fileName, PATHINFO_FILENAME);
					$fileName 	= preg_replace('/[^A-Za-z0-9]/u','', strip_tags($fileName));
					$newfilename 	= str_replace(array("/", "\\", "."), array("","",""),crypt($fileName, $salt)).date('Ymd').$unique_id.'.'.$extension;
					// $newfilename	= crypt($newfilename, $salt);
					if( !$allowed )
					{
						throw new Exception('Invalid File');
					}
					
					move_uploaded_file($params["file"]["tmp_name"],$output_dir.$newfilename);
					$ret[] 			= $newfilename;
				}
				else  //Multiple files, file[]
				{	
					$fileCount 	= count($params["file"]["name"]);
					
					for($i=0; $i < $fileCount; $i++)
					{
						$fileName 		= str_replace(" ","_",$params["file"]["name"][$i]);
						$allowed 		= $this->upload->is_allowed_filetype($params, $fileName, $params["file"]["tmp_name"][$i] );
						$gen_file 		= strip_tags(crypt( $fileName, $salt ));
						$gen_file 		= str_replace(array("/", "\\", "."), array("","",""), $gen_file);
						$newfilename = preg_replace('/[^A-Za-z0-9 _.]/u','', $gen_file);
						/*$newfilename 	= preg_replace('/[^A-Za-z0-9 _.]/u','', strip_tags(crypt( $fileName, $salt ) ));*/
						// $newfilename 	= crypt($newfilename, $salt);
						
						
						if( !$allowed )
						{
							throw new Exception('Invalid File');
						}
						
						move_uploaded_file($params["file"]["tmp_name"][$i],$output_dir.$newfilename);		
						$ret[] 	= $newfilename;
					}
				}

				echo json_encode( array('sys_file_name' => $ret[0]) );
			}
		}catch( PDOException $e ){			
			$msg = $e->getMessage();	
			echo json_encode($msg);
		}catch (Exception $e){
			$msg = $e->getMessage();	
			echo json_encode($msg);
		}
	}
}