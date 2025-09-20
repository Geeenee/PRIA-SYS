<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pria_file extends SYSAD_Controller
{
	protected $allowed_types 	= '';
	protected $file_type 		= '';
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model(PORTAL_TRANSACTIONS.'/documents_model', 'dm_model'); 
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
				
				$path_dir 	= $root_path.PATH_UPLOADED_FILES.$dir_path;

				$path_dir 	= str_replace(array('\\','/'), array(DS,DS), $path_dir);
				$path 		= $path_dir.$params['file'];
				$path 		= str_replace(array('\\','/'), array(DS,DS), $path);
				
				if( file_exists( $path ) )
				{
					$contents		= file_get_contents($path);

					$document_dets 	= $this->dm_model->get_document(['sys_file_name' => $params['file']], ['file_name']);
	
					$this->load->helper('download');
						
					force_download( $document_dets['file_name'], $contents );

					$flag 		= 1;
				}
				else
				{
					throw new Exception('File not found.');
				}
			}

			throw new Exception('File not found.');
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

	public function view()
	{
		
		$flag 				= 0;
		
		try
		{
			$params 		= get_params(TRUE, TRUE);
			
			if( ISSET( $params['file'] ) )
			{
				$root_path 	= $this->get_root_path();
				$path_dir 	= $root_path.PATH_UPLOADED_FILES.$dir_path;

				$path_dir 	= str_replace(array('\\','/'), array(DS,DS), $path_dir);
				$path 		= $path_dir.$params['file'];
				$path 		= str_replace(array('\\','/'), array(DS,DS), $path);
				
				
			
				if( file_exists( $path ) )
				{
					$ext 			= pathinfo( $path, PATHINFO_EXTENSION );
					
					$img_ext_arr 	= array('gif','png','jpeg','jpg');

					$contents		= file_get_contents($path);

					$document_dets 	= $this->dm_model->get_document(['sys_file_name' => $params['file']], ['file_name']);

					$filename 		= $document_dets['file_name'];
	
					if( strtolower( $ext ) == 'pdf'
							AND ( !ISSET( $params['pdf_no'] ) AND EMPTY( $params['pdf_no'] ) )
							)
					{	
						header("Content-type: application/pdf");
						header("Content-Disposition: inline; filename=".$filename);
						
						readfile( $path );
					}
					else if( in_array( strtolower( $ext ), $img_ext_arr ) )
					{
						$mime = mime_content_type($path);

						ob_clean();

						header("Content-Type: ".$mime);

						readfile($path);
					}
					else
					{
						$this->load->helper('download');
						
						force_download( $filename, $contents );
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
	
}