<?php

    $html 		= '';
    $cnt  		= ( ISSET($counter) ) ?  $counter : 0;
    $with_datatable = ISSET($with_datatable) ? $with_datatable : FALSE;
    $file_size 	= '';
    
   //$cnt = 0;
   //Remove in .task-append data-sec="$hashed_sec"

    if(!EMPTY($list)){
	foreach($list as $val)
	{
        $hashed_id  = ISSET($val['reference_id']) ? encrypt_id($val['reference_id']) : '';
        $hashed_ag  = ISSET($ag_code) ? encrypt_id($ag_code) : '';
        $hashed_pwi = ISSET($val['pria_workflow_id']) ? encrypt_id($val['pria_workflow_id']) : '';

		$document_id 			= base64_url_encode($val['document_id']);
		$reference 				= base64_url_encode($val['reference']);
        $document_type_code 	= base64_url_encode($val['document_type_code']);
		$version_id 			= $val['version'];
		$date_submitted  		= $val['created_date'];
		$file_name  			= $val['file_name'];
		$sys_file_name 			= $val['sys_file_name'];
		$created_date 			= (ISSET($val['modified_date'])) ? std_db_datetime_format($val['modified_date']) : std_db_datetime_format($val['created_date']);
		$created_by 			= $val['created_by'];
		

		$file_ext 				= (explode('.',$val['sys_file_name']));
		$file_ext 				= end($file_ext);
		
		$path = PATH_UPLOADED_FILES.$sys_file_name;
		$path = str_replace(array('\\','/'), array(DS,DS), $path);

	    if( file_exists( $path ) )
	        {
	            $file_size         = file_size_convert( filesize( $path ) );
	            $file_size_num     = filesize( $path );
	    }

		$path 					= PATH_UPLOADED_FILES;
        $base_url 				= base_url();
        

        $dl_path 		= base_url().'pria_file/download?file='.$sys_file_name;
        $vw_path 		= base_url().'pria_file/view?file='.$sys_file_name;

		//<button type="button" id="delete" onclick="content_delete('attachment','$document_id')" class='tooltipped' data-tooltip='Delete' data-position='top' data-delay='50'><i class="material-icons valign-middle">delete</i> </button>
		
        $html      .=<<<EOS
        <li class="" data-id="$hashed_id" data-pwi="$hashed_pwi">
            <div class="">
                <div class="row m-b-n">
                    <div class="row">
						<div class="col l12 m12 s12 valign-middle">
							<div class="file-wrapper $file_ext ">
								<div class="type" data-file-type=" $file_ext"></div>
								<div class="contents valign-top">
									<div class="filename truncate"><span class="red-text"><b>v$version_id</b></span> | $file_name</div>
									<div>uploaded $created_date by $created_by $file_size</div>

                                    <div class="action-links">
                                        <a target="_blank" href="$vw_path" >View</a>
                                        <a target="_blank" href="$dl_path">Download</a>
                                    </div>
								</div>
								
								<div class="actions valign-middle right-align">
				        			
				        			<a target="_blank" href="$vw_path" >
										<button type="button" id="view" class="tooltipped" data-position="top" data-tooltip="View"><i class="material-icons valign-middle ">search</i></button>
									</a>
				        			<a target="_blank" class="tooltipped" data-position="top" data-tooltip="Download" href="$dl_path" download>
				        				<button type="button" id="download"><i class="material-icons valign-middle gray-text">file_download</i></button>
				        			</a>
				        		</div>
							</div>
						</div>
					</div>
                    
                </div>
            </div>
            <div class="collapsible-body">
                <div class="row p-b-n">
                    <div class="col s4 offset-s4 center">
                        <div class="progress">
                            <div class="indeterminate"></div>
                        </div>   
                    </div>
                </div>
            </div>
        </li>
EOS;

        $cnt++;
    }	
      

    	if( ISSET($last_page) && $last_page === FALSE)
        	$html .= '<span id="scroll-next-page"></span>';

        echo $html;  
    }else{

        IF($with_datatable == FALSE){
        $html = '
            <div id="notfound">
                <div class="notfound">
                    <div class="notfound-404">
                        <h1>No data available</h1>
                    </div>
                    <h2>Oops! Nothing was found</h2>
                </div>
            </div>';
        echo $html;
        }
    }   
?>	