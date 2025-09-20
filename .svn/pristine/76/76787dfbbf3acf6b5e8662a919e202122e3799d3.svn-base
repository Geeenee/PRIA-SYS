<?php
if($view)
{
    foreach($boq_asset_codes as $bac)
    {
        echo <<<EOS
            <tr>
                <td>{$bac['asset_type_names']}</td>
                <td>{$bac['vendor_name']}</td>
            </tr>
EOS;
    }
}
else
{
    //Templates
    $cat_options   = '';
    $ven_options   = '';
    $tpl           = <<<EOS
        <tr id="trow-category">
            <td>
                <select id="boq_category_%s" class="selectize category" name="boq_category_%s[]" placeholder="Select category" data-parsley-required="true" multiple>
                    <option value=""></option>
                    %s
                </select>
            </td>

            <td>
                <select id="vendor_%s" class="selectize vendor" name="vendor_%s[]" placeholder="Select vendor" data-parsley-required="true">
                    <option value=""></option>
                    %s
                </select>
            </td>

        
            <td>
                <a id="delete_category_row" class="%s delete" style="cursor:pointer"><i class="material-icons" style="padding:5px 10px;">delete</i></a>
            </td>
        </tr>
EOS;


    if( ! EMPTY($boq_asset_codes))
    {
        $seq = 1;
        foreach($boq_asset_codes as $b)
        {
            
            $cat_options     = '';
            $ven_options     = '';

            foreach($categories as $c)
            {
                $assets       = explode(',', $b['asset_types']);
                $selected     = in_array($c['category_code'], $assets) ? 'selected' : '';
                $cat_options .= <<<EOS
                <option value="{$c['category_code']}" $selected>{$c['category_name']}</option>
EOS;
            }
        
            foreach($contractors as $c)
            {
                $selected     = ($c['vendor_code'] == $b['confirmed_contractor']) ? 'selected' : '';
                $ven_options .= <<<EOS
                <option value="{$c['vendor_code']}" $selected>{$c['vendor_name']}</option>
EOS;
            }

            $hide = ($seq > 1) ? '' : 'hide';

            echo sprintf($tpl, $seq, $seq, $cat_options, $seq, $seq, $ven_options, $hide);

            $seq++;
        }
    }
    else
    {
        
        foreach($categories as $c)
        {
            $cat_options .= <<<EOS
            <option value="{$c['category_code']}">{$c['category_name']}</option>
EOS;
        }

        foreach($contractors as $c)
        {
            $ven_options .= <<<EOS
            <option value="{$c['vendor_code']}">{$c['vendor_name']}</option>
EOS;
        }

        echo sprintf($tpl, 0, 0, $cat_options, 0, 0, $ven_options, 'hide');
    }
}
?>