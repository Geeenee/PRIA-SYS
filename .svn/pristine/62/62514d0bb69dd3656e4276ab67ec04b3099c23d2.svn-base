<?php
    $html         = '';
    $text_date    = '';
    $date         = (ISSET($prev_date)) ? $prev_date : date(FORMAT_DB_DATE);
    $curr_tl_date = date('M d', strtotime($date)); 
    $with_datatable = ISSET($with_datatable) ? $with_datatable : FALSE;

    if(!EMPTY($overview_list)){
    foreach($overview_list as $ol)
    {
        $img_path  = PATH_USER_UPLOADS;
        
        $options   = ['class' => 'responsive-img circle ', 'style' => 'width:50; height:50;', 'name' => $ol['fname']];

        $image     = create_img_tag($ol['photo'], $img_path, $options, TRUE);

        $time      = strtotime($ol['created_date']);

        $tl_time   = date('h : i a', $time);

        $tl_date   = date('M d', $time);

        //Replace text to "today"
        if($tl_date == $curr_tl_date)
            $tl_date = ($initial) ? 'Today' : '';

        //If same date from the previous row, dont display anything
        if($text_date == $tl_date)
        {
            $tl_date    = '';
            $class      = 'none';
        }
        else{
            $text_date  = $tl_date;
            $class      = '';
        }

        $html     .=<<<EOS
        <li>
            <div class="tl-date $class">$tl_date</div>
            <div class="tl-avatar">$image</div>
            <div class="tl-contents">
               {$ol['overview_html']}
            </div>
            <div class="tl-timestamp">$tl_time</div>
        </li>
EOS;
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