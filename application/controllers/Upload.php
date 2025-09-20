<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Upload extends SYSAD_Controller
{
	protected $allowed_types 	= '';
	protected $file_type 		= '';
	
	public function __construct()
	{
		parent::__construct();
		
		$mimes 	= get_mimes();
		
		$this->set_allowed_types($mimes);
		
		$this->load->model('Common_validate_model', 'cvm');
		
	}
	
	protected function set_allowed_types($types)
	{
		$this->allowed_types = (is_array($types) OR $types === '*')
			? $types
			: explode('|', $types);
	}
	
	public function get_extension($filename, $file_ext_tolower = FALSE)
	{
		$x = explode('.', $filename);
		
		if (count($x) === 1)
		{
		    return '';
		}
		
		$ext = ($file_ext_tolower) ? strtolower(end($x)) : end($x);
		return '.'.$ext;
	}
	
	public function is_allowed_filetype(array $params, $fileName, $tmp_name,  $ignore_mime = FALSE)
	{
		$extension 	= $this->get_extension($fileName);
		
		$this->_file_mime_type($params['file']);
		
		if ($this->allowed_types === '*')
		{
			return TRUE;
		}
		
		if( EMPTY($this->allowed_types) OR !is_array($this->allowed_types) )
		{
			// $this->set_error('upload_no_file_types');
			return FALSE;
		}
		
		$ext = strtolower(ltrim($extension, '.'));
		
		if ( ! in_array($ext, array_keys( $this->allowed_types ), TRUE))
		{
			return FALSE;
		}
		
		// Images get some additional checks
		
		
		if (in_array($ext, array('gif', 'jpg', 'jpeg', 'jpe', 'png'), TRUE) && @getimagesize($tmp_name) === FALSE)
		{
			return FALSE;
		}
		
		if ($ignore_mime === TRUE)
		{
			return TRUE;
		}
		
		$mimes 	= get_mimes();
		
		if (ISSET($mimes[$ext]))
		{
			return is_array($mimes[$ext])
				? in_array($this->file_type, $mimes[$ext], TRUE)
				: ($mimes[$ext] === $this->file_type);
		}
		
		return FALSE;
	}
	
	protected function _file_mime_type($file)
	{
		$regexp = '/^([a-z\-]+\/[a-z0-9\-\.\+]+)(;\s.+)?$/';
		
		if (function_exists('finfo_file'))
		{
			$finfo = @finfo_open(FILEINFO_MIME);
			if (is_resource($finfo)) 
			{
				$mime = @finfo_file($finfo, $file['tmp_name']);
				finfo_close($finfo);
				
				if (is_string($mime) && preg_match($regexp, $mime, $matches))
				{
					$this->file_type = $matches[1];
					return;
				}
			}
		}
		
		if (DIRECTORY_SEPARATOR !== '\\')
		{
			$cmd = function_exists('escapeshellarg')
				? 'file --brief --mime '.escapeshellarg($file['tmp_name']).' 2>&1'
				: 'file --brief --mime '.$file['tmp_name'].' 2>&1';
					
			if (function_usable('exec'))
					{
						$mime = @exec($cmd, $mime, $return_status);
						if ($return_status === 0 && is_string($mime) && preg_match($regexp, $mime, $matches))
						{
							$this->file_type = $matches[1];
							return;
						}
					}
					
					if ( ! ini_get('safe_mode') && function_usable('shell_exec'))
					{
						$mime = @shell_exec($cmd);
						if (strlen($mime) > 0)
						{
							$mime = explode("\n", trim($mime));
							if (preg_match($regexp, $mime[(count($mime) - 1)], $matches))
							{
								$this->file_type = $matches[1];
								return;
							}
						}
					}
					
					if (function_usable('popen'))
					{
						$proc = @popen($cmd, 'r');
						if (is_resource($proc))
						{
							$mime = @fread($proc, 512);
							@pclose($proc);
							if ($mime !== FALSE)
							{
								$mime = explode("\n", trim($mime));
								if (preg_match($regexp, $mime[(count($mime) - 1)], $matches))
								{
									$this->file_type = $matches[1];
									return;
								}
							}
						}
					}
		}
		
		if (function_exists('mime_content_type'))
		{
			$this->file_type = @mime_content_type($file['tmp_name']);
			if (strlen($this->file_type) > 0) 
			{
				return;
			}
		}
		
		$this->file_type = $file['type'];
	}
	
	private function _filter( array $orig_params, array $par_keys )
	{
		$par 			= $this->set_filter( $orig_params );
		
		foreach( $par_keys as $key )
		{
			$par->filter_string( $key, TRUE );
		}
		
		$params 		= $par->filter();
		
		return $params;
	}
	
	
	public function delete_multi_dt()
	{
		$prev_detail 			= array();
		$curr_detail 			= array();
		$audit_table			= array();
		$audit_schema 			= array();
		$audit_action 			= array();
		
		$orig_params 			= get_params();
		
		$delete_per 			= $this->delete_per;
		
		$msg 					= '';
		$flag 					= 0;
		$status 				= ERROR;
		
		$main_where 			= array();
		
		try
		{
			$orig_params 			= get_params();
			
			$par_keys 				= array_keys( $orig_params );
			
			$tables 				= $orig_params['tables'];
			$extra_data 			= $orig_params['extra_data'];
			unset( $orig_params['tables'] );
			unset( $orig_params['extra_data'] );
			
			$params 				= $this->_filter( $orig_params, $par_keys );
			
			$real_par 				= $params;
			
			foreach( $params as $columns => $val )
			{
				$main_where[$columns] 	= array( 'IN', $val );
			}
			
			ADACFMS_Model::beginTransaction();
			
			foreach( $tables as $table )
			{
				$audit_schema[] 	= $extra_data['schema'];
				$audit_table[] 	 	= $table;
				$audit_action[] 	= AUDIT_DELETE;
				$prev_detail[] 		= $this->cvm->get_details_for_audit( $table,
						$main_where
						);
				
				$this->cvm->delete_helper( $table, $main_where );
				
				$curr_detail[] 		= array();
			}
			
			$audit_name 		= $extra_data['module'].'.';
			
			$audit_activity 			= sprintf( $this->lang->line('audit_trail_delete'), $audit_name);
			
			$this->audit_trail->log_audit_trail( $audit_activity, $extra_data['module'], $prev_detail, $curr_detail, $audit_action, $audit_table, $audit_schema );
			
			ADACFMS_Model::commit();
			
			$status 				= SUCCESS;
			$flag 					= 1;
			$msg 					= $this->lang->line( 'data_deleted' );
		}
		catch( PDOException $e )
		{
			
			ADACFMS_Model::rollback();
			
			$this->rlog_error( $e );
			
			$msg 					= $this->get_user_message( $e );
		}
		catch (Exception $e)
		{
			
			ADACFMS_Model::rollback();
			
			$this->rlog_error( $e );
			
			$msg 					= $e->getMessage();
		}
		
		$response 					= array(
				'msg' 					=> $msg,
				'flag' 					=> $flag,
				'status'				=> $status
		);
		
		echo json_encode( $response );
	}
	
	public function index()
	{
		try
		{ 
			$msg 		= '';
			$params		= get_params();
			$output_dir = $params['dir'];
			
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
					$allowed 	= $this->is_allowed_filetype($params, $fileName, $params["file"]["tmp_name"] );
					
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
						$allowed 		= $this->is_allowed_filetype($params, $fileName, $params["file"]["tmp_name"][$i] );
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
						// exec($cmd);
						
						$ret[] 	= $newfilename;
					}
				}
				
				echo json_encode($ret);
			}
		}
		catch( PDOException $e )
		{
			
			RLog::error( $e->getMessage() . "\n" . $e->getTraceAsString() );
			
			$msg 					= $e->getMessage();
			
			// echo json_encode($msg);
		}
		catch (Exception $e)
		{
			
			RLog::error( $e->getMessage() . "\n" . $e->getTraceAsString() );
			
			$msg 					= $e->getMessage();
			
			// echo json_encode($msg);
		}
		
		if( ! EMPTY($msg))
			echo json_encode(['jquery-upload-file-error' => $msg]);
	}
	
	public function existing_files()
	{
		try
		{
			$params		= get_params();
			$output_dir = $params['dir'];
			$root_path 	= $this->get_root_path();
			
			$output_dir = $root_path.$output_dir;
			$output_dir = str_replace(array('/', '\\'), array(DS, DS), $output_dir);
			
			$files 		= scandir($output_dir);
			
			$db_file 	= ( ISSET( $params['file'] ) ) ? $params['file'] : NULL;
			
			$ret 		= array();
			
			$multi_file = FALSE;
			
			if( ISSET( $params['max_file'] ) AND intval( $params['max_file'] ) > 1 )
			{
				$multi_file = TRUE;
				
				foreach( $params['file'] as $f )
				{
					$db_file_arr = explode('|', $f);
					
					if( !EMPTY( $db_file_arr ) )
					{
						$db_file = array();
						
						foreach( $db_file_arr as $db_file_e )
						{
							$db_file_d 	= explode('=', $db_file_e);
							
							$db_file[]  = $db_file_d[0];
						}
					}
				}
			}
			
			if(ISSET($db_file))
			{
				foreach($files as $file)
				{
					if($file == "." || $file == "..")
						continue;
						
						if( is_array( $db_file ) )
						{
							$multi_file = TRUE;
							
							if( in_array( $file , $db_file ) )
							{
								$key 	= array_search($file, $db_file);
								
								$key 	= ( int )$key;
								
								$ret[$key]=$file;
							}
						}
						else
						{
							if($file == $db_file)
								$ret[]=$file;
						}
				}
			}
			
			if( $multi_file )
			{
				ksort( $ret );
				
				$ret 	= flattened_array( $ret );
			}
			
			echo json_encode($ret);
		}
		catch(Exception $e)
		{
			echo $this->rlog_error($e, TRUE);
		}
	}
	
	public function delete($params = array())
	{
		$msg 				= '';
		


		try
		{
			$params			= (!EMPTY($params))? $params : get_params();
			$output_dir 	= $params['dir'];
			
			$root_path 	= $this->get_root_path();
			$output_dir = $root_path.$output_dir;
			$output_dir = str_replace(array('/', '\\'), array(DS, DS), $output_dir);
			
			$module_schema 	= DB_CORE;
			$method 		= 'delete_attachments';
			$audit_method 	= ACTION_DELETE;
			
			if(isset($params["op"]) && $params["op"] == "delete" && isset($params['name']))
			{
				
				if(ISSET($params['delete_path']) AND !EMPTY( $params['delete_path'] ))
				{
					$type_obj 		= Modules::load( $params['delete_path'] );
					
					if( !EMPTY( $type_obj ) AND ISSET( $params['delete_path_method'] ) )
					{
						call_user_func_array( array( $type_obj, $params['delete_path_method'] ), array( $params ) );
						
					}
				}
				else
				{
					if( ISSET( $params['module_table'] ) AND !EMPTY( $params['module_table'] ) )
					{
						
					}
				}
				
				$fileName = $params['name'];
				$fileName = str_replace("..",".",$fileName); //required. if somebody is trying parent folder files
				$filePath = $output_dir. $fileName;
				if (file_exists($filePath))
					unlink($filePath);
					
					if(!ISSET($params["no_echo"]))
						echo "Deleted File ".$fileName."<br>";
			}
		}
		catch( PDOException $e )
		{
			$this->upload->rollback();
			
			RLog::error( $e->getMessage() . "\n" . $e->getTraceAsString() );
			
			$msg 					= $e->getMessage();
		}
		catch (Exception $e)
		{
			$this->upload->rollback();
			
			RLog::error( $e->getMessage() . "\n" . $e->getTraceAsString() );
			
			$msg 					= $e->getMessage();
		}
		
		echo $msg;
	}
	
	public function download()
	{
		
		$flag 				= 0;
		
		try
		{
			$params 		= get_params(TRUE, TRUE);
			
			if( ISSET( $params['file'] ) )
			{
				$root_path 	= $this->get_root_path();
				
				$path_dir 	= NULL;
				$path_fold 	= NULL;
				
				if( ISSET( $params['dir'] ) )
				{
					$dir_arr 	= explode( '|', $params['dir'] );
					
					$dir_path 	= implode( '/', $dir_arr);
					
					$path_dir 	= $root_path.PATH_UPLOADS.$dir_path.'/';
					$path_fold 	= PATH_UPLOADS.$dir_path.'/';
				}
				else if( $params['path'] )
				{
					$path_dir 	= $root_path.$params['path'];
					$path_fold 	= $params['path'];
				}
				
				
				$path_dir 	= str_replace(array('\\','/'), array(DS,DS), $path_dir);
				
				$path 		= $path_dir.$params['file'];
				$path 		= str_replace(array('\\','/'), array(DS,DS), $path);
				
				if( file_exists( $path ) )
				{
					$ext 		= pathinfo( $path, PATHINFO_EXTENSION );
					
					$img_ext_arr 	= array('gif','png','jpeg','jpg');
					
					if( strtolower( $ext ) == 'pdf'
							AND ( !ISSET( $params['pdf_no'] ) AND EMPTY( $params['pdf_no'] ) )
							)
					{
						$pdf 	= file_get_contents( $path );
						
						header("Content-type: application/pdf");
						header("Content-Disposition: inline; filename=".$params['file']."");
						
						readfile( $path );
						
					}
					else if( in_array( strtolower( $ext ), $img_ext_arr ) )
					{
						$img_path 	= base_url().$path_fold.$params['file'];
						// $imginfo 	= getimagesize($img_path);
						
						$this->load->view('imageviewer', array('img_path' => $img_path));
						
					}
					else
					{
						$this->load->helper('download');
						
						force_download( $path, NULL );
					}
					
					$flag 		= 1;
				}
				else
				{
					throw new Exception('File not found.');
				}
			}
		}
		catch( PDOException $e )
		{
			$msg 	= $this->get_user_message($e);
			
			$this->error_index( $msg );
		}
		catch(Exception $e)
		{
			$msg  	= $this->rlog_error($e, TRUE);
			
			$this->error_index( $msg );
		}
		
		if( !$flag )
		{
			// redirect(base_url().'Errors/index/402/');
		}
		
	}
	
	public function force_download()
	{
		
		$flag 				= 0;

		try
		{
			$params 		= get_params(TRUE, TRUE);
		
			if( ISSET( $params['file'] ) )
			{		
				$root_path 	= $this->get_root_path();
				
				$path_dir 	= NULL;
				$path_fold 	= NULL;
				
				if( ISSET( $params['dir'] ) )
				{
					$dir_arr 	= explode( '|', $params['dir'] );
					
					$dir_path 	= implode( '/', $dir_arr);
					
					$path_dir 	= $root_path.PATH_UPLOADS.$dir_path.'/';
					$path_fold 	= PATH_UPLOADS.$dir_path.'/';
				}
				else if( $params['path'] )
				{
					$path_dir 	= $root_path.$params['path'];
					$path_fold 	= $params['path'];
				}
				
				
				$path_dir 	= str_replace(array('\\','/'), array(DS,DS), $path_dir);
				
				$path 		= $path_dir.$params['file'];
				$path 		= str_replace(array('\\','/'), array(DS,DS), $path);
				
				if( file_exists( $path ) )
				{
					$ext 		= pathinfo( $path, PATHINFO_EXTENSION );
					
					$img_ext_arr 	= array('gif','png','jpeg','jpg');
					
					$this->load->helper('download');
					
					force_download( $path, NULL );
					
					$flag 		= 1;
				}
				else
				{
					throw new Exception('File not found.');
				}
			}
		}
		catch( PDOException $e )
		{
			$msg 	= $this->get_user_message($e);
			
			$this->error_index( $msg );
		}
		catch(Exception $e)
		{
			$msg  	= $this->rlog_error($e, TRUE);
			
			$this->error_index( $msg );
		}
		
		if( !$flag )
		{
			// redirect(base_url().'Errors/index/402/');
		}
		
	}
	
	public function upload_ckeditor()
	{
		
		$params				= get_params(TRUE, TRUE);
		$output_dir 		= PATH_CKEDITOR_UPLOADS;
		$ret 				= array();
		$ret_filename 		= "";
		$url 				= "";
		$error 				= "";
		$flag 				= 0;
		//$cmd = 'icacls '.SERVER_UPLOAD_FOLDER.' /grant "Everyone":(OI)(CI)F';
		
		try
		{
			
			$uploadimgerrors1 = $this->lang->line('err_ck_upload1');
			$uploadimgerrors2 = $this->lang->line('err_ck_upload2');
			$uploadimgerrors3 = $this->lang->line('err_ck_upload3');
			$uploadimgerrors4 = $this->lang->line('err_ck_upload4');
			$uploadimgerrors5 = $this->lang->line('err_ck_upload5');
			$uploadimgerrors6 = $this->lang->line('err_ck_upload6');
			$uploadimgerrors7 = $this->lang->line('err_ck_upload7');
			$uploadimgerrors8 = $this->lang->line('err_ck_upload8');
			
			if( !EMPTY( $output_dir ) )
			{
				$root_path 		= $this->get_root_path();
				
				$output_dir 	= $root_path.PATH_CKEDITOR_UPLOADS;
				$output_dir 	= str_replace( array('/', '\\'), array( DS, DS ), $output_dir );
			}
			
			if( !is_dir( $output_dir ) )
			{
				mkdir( $output_dir, 0777, TRUE );
			}
			
			
			if( ISSET( $params["upload"] ) )
			{
				
				$file_type 		= pathinfo( $params['upload']['name'], PATHINFO_EXTENSION );
				
				$valid_file_type= explode(',', IMAGE_EXTENSIONS);
				
				if( !in_array( strtolower( $file_type ), $valid_file_type ) )
				{
					echo '
					 	<script src="'.base_url().PATH_JS.'jquery-2.1.1.min.js"></script>
						<script src="'.base_url().PATH_JS.'ckeditor/plugins/imageuploader/dist/sweetalert.min.js"></script>
    					<link rel="stylesheet" type="text/css" href="'.base_url().PATH_JS.'ckeditor/plugins/imageuploader/dist/sweetalert.css">
			            <script>
			            $( function () {
							 swal({
				              title: "Error!",
				              text: "'.$uploadimgerrors8.'",
				              type: "error",
				              closeOnConfirm: false
				            },
				            function(){
				              history.back();
				            });
			            });
			            </script>
			        ';
					exit();
				}
				
				$check 			= getimagesize( $params["upload"]["tmp_name"] );
				
				if($check !== false)
				{
					$flag 		= 1;
				}
				else
				{
					echo "<script>alert('".$uploadimgerrors1."');</script>";
					$flag 		= 0;
				}
				
				$error 			= ( ISSET( $params["upload"]["error"] ) AND !EMPTY( $params["upload"]["error"] ) ) ? $params["upload"]["error"] :  "";
				
				
				//You need to handle both cases
				//If any browser does not support serializing of multiple files using FormData()
				
				if( !is_array( $params["upload"]["name"] ) ) //single file
				{
					$fileName 	= str_replace(" ","",$params["upload"]["name"]);
					$extension 	= pathinfo($fileName, PATHINFO_EXTENSION);
					$extension 	= strtolower( $extension );
					$fileName 	= pathinfo($fileName, PATHINFO_FILENAME);
					$fileName 	= preg_replace('/[^A-Za-z0-9]/u','', strip_tags($fileName));
					
					$upload_id 	= 0;
					
					/*$upload_id 	= $this->upload->insert_attachments( $this->upload->tbl_uploads,
					 array(
					 'filename'	=> $fileName,
					 'extension'	=> $extension,
					 'dir'		=> $output_dir,
					 'created_by'=> $this->session->user_id,
					 'created_date' => date( 'Y-m-d H:i:s' )
					 )
					 );*/
					
					$unique_id 	= uniqid();
					
					if( !EMPTY( $upload_id ) )
					{
						$unique_id 		= str_pad($upload_id, 4, '0', STR_PAD_LEFT);
					}
					
					if( !EMPTY( $params['asset_type'] ) )
					{
						$newfilename 	= strtoupper( $params['asset_type'] ).'_'.date('Ymd').'_'.$unique_id.'.'.$extension;
					}
					else
					{
						$newfilename 	= $fileName.'_'.date('Ymd').'_'.$unique_id.'.'.$extension;
					}
					
					$ret[] 				= $newfilename;
					$ret_filename 		= $newfilename;
					$url 				= base_url().PATH_FILE_UPLOADS.$newfilename;
					
					move_uploaded_file( $params["upload"]["tmp_name"], $output_dir.$newfilename );
					$flag 				= 1;
					//exec($cmd);
				}
				else  //Multiple files, file[]
				{
					$fileCount 			= count( $params["upload"]["name"] );
					
					for( $i=0; $i < $fileCount; $i++ )
					{
						$fileName 		= str_replace(" ","_",$params["upload"]["name"][$i]);
						$newfilename 	= preg_replace('/[^A-Za-z0-9 _.]/u','', strip_tags($fileName));
						
						move_uploaded_file( $params["upload"]["tmp_name"][$i], $output_dir.$newfilename );
						
						$flag 				= 1;
						
						if( ISSET( $cmd ) )
						{
							exec($cmd);
						}
						
						$ret[] 			= $newfilename;
					}
					
					$ret_filename 		= implode(",", $ret);
				}
			}
		}
		catch( PDOException $e )
		{
			RLog::error( $e->getMessage() . "\n" . $e->getTraceAsString() );
			
			$msg 					= $e->getMessage();
		}
		catch (Exception $e)
		{
			RLog::error( $e->getMessage() . "\n" . $e->getTraceAsString() );
			
			$msg 					= $e->getMessage();
		}
		
		$response 					= array(
				'uploaded'				=> $flag,
				'fileName'				=> $ret_filename,
				'url'					=> $url
		);
		
		if( !EMPTY( $error ) )
		{
			$response['error']		= array(
					'message'			=> $error
			);
		}
		
		if( $flag == 1 )
		{
			if( ISSET( $params['CKEditorFuncNum'] ) )
			{
				$CKEditorFuncNum 	= $params['CKEditorFuncNum'];
				
				echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$ret_filename', '');</script>";
			}
		}
		else
		{
			echo "<script>alert('".$uploadimgerrors6." ".$ret_filename." ".$uploadimgerrors7."');</script>";
		}
		
		if( !ISSET( $params['CKEditorFuncNum'] ) )
		{
			echo '<script>history.back();</script>';
		}
		
		// echo json_encode( $response );
	}
	
	public function delete_ckeditor()
	{
		$params				= get_params(TRUE, TRUE);
		$output_dir 		= PATH_CKEDITOR_UPLOADS;
		$ret 				= array();
		$ret_filename 		= "";
		$url 				= "";
		$error 				= "";
		$flag 				= 0;
		
		try
		{
			
			if( !EMPTY( $output_dir ) )
			{
				$root_path 		= $this->get_root_path();
				
				$output_dir 	= $root_path.PATH_CKEDITOR_UPLOADS;
				$output_dir 	= str_replace( array('/', '\\'), array( DS, DS ), $output_dir );
			}
			
			$filename 			= filter_var( $params['img'], FILTER_SANITIZE_STRING);
			
			$file_path 			= $output_dir.$filename;
			
			if( file_exists( $file_path ) )
			{
				if( !unlink( $file_path ) )
				{
					throw new Exception('Cannot delete file');
				}
				else
				{
					header('Location: ' . $_SERVER['HTTP_REFERER']);
					$flag 		= 1;
				}
			}
			
		}
		catch( PDOException $e )
		{
			RLog::error( $e->getMessage() . "\n" . $e->getTraceAsString() );
			
			$msg 					= $e->getMessage();
		}
		catch (Exception $e)
		{
			RLog::error( $e->getMessage() . "\n" . $e->getTraceAsString() );
			
			$msg 					= $e->getMessage();
		}
		
		if( $flag == 0 )
		{
			echo '
	            <script>
	            swal({
	              title: "Error",
	              text: "'.$msg.'",
	              type: "error",
	              closeOnConfirm: false
	            },
	            function(){
	              history.back();
	            });
	            </script>
	        ';
		}
		
	}
}