<?php

  if( ISSET($buttons) )
  {
      foreach($buttons as $b)
      {
        $icon       = (ISSET($b['icon']) &&  ! EMPTY($b['icon']))       ? $b['icon'] : 'add';
        $class      = (ISSET($b['class']) &&  ! EMPTY($b['class']))     ? $b['class'] : '';
        $target     = (ISSET($b['target']) &&  ! EMPTY($b['target']))   ? 'data-target="'.$b['target'].'"': '';
        $onclick    = (ISSET($b['onclick']) &&  ! EMPTY($b['onclick'])) ? 'onclick="'.$b['onclick'].'"'   : '';

        echo <<<EOS
            <button type="button" id="{$b['id']}" class="btn-extra-toggle $class" $target $onclick>
                <i class="material-icons">$icon</i> {$b['label']}
            </button>
EOS;
      }
  }    
?>

<?php if(!ISSET($hide_filter) OR EMPTY($hide_filter)): ?>
<button type="button" id="filter_task" class="btn-toggle <?php //echo $toggle_display ?>"><i class="material-icons">tune</i> Filter</button>
<?php endif; ?>

<?php

    if($imports)
    {
        $html =<<<EOS
        <a data-activates='transaction-actions' class="dropdown-trigger" data-tooltip="Actions" title="Actions">
            <i class="material-icons">more_vert</i>
        </a>

        <ul id='transaction-actions' class='dropdown-content'>
EOS;

        foreach($imports as $actions)
        {
            $icon       = (ISSET($actions['icon'])   &&  ! EMPTY($actions['icon']))       ? $actions['icon'] : 'add';
            $class      = (ISSET($actions['class'])  &&  ! EMPTY($actions['class']))      ? $actions['class'] : '';
            $target     = (ISSET($actions['target']) &&  ! EMPTY($actions['target']))     ? 'data-target="'.$actions['target'].'"': '';
            $onclick    = (ISSET($actions['onclick'])  &&  ! EMPTY($actions['onclick']))  ? 'onclick="'.$actions['onclick'].'"'   : '';

            $html.=<<<EOS
             <li>
                <a $target $onclick class="$class">
                    <i class="material-icons">$icon</i>{$actions['label']}
                </a>
            </li>
EOS;
        }

        $html .=<<<EOS
        </ul>
EOS;
        echo $html;
    }

?>
