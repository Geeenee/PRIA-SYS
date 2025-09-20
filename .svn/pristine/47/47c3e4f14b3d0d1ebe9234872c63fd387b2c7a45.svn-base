<?php

if($view)
{
    //$categories = array_column($categories, 'category_name', 'category_code');

    foreach($boq_asset_codes as $bac)
    {
       // $cat = $categories[$bac['asset_type']];
        $io = (!EMPTY($bac['internal_order']))? $bac['internal_order']: "N/A";
        echo <<<EOS
            <tr>
                <td>{$bac['category_name']}</td>
                <td>{$bac['asset_code']}</td>
                <td>{$io}</td>
            </tr>
EOS;
    }
}
else
{
    $options   = '';
/*     $tpl       = <<<EOS
    <tr id="trow-category">
        <td>
            <select id="boq_category_%s" class="selectize" name="boq_category[]" placeholder="Select category" data-parsley-required="true">
                <option value=""></option>
                %s
            </select>
        </td>

        <td>
            <input type="text" name="asset_code[]" id="asset_code" placeholder="Enter Asset Code" data-parsley-required="true" value="%s" />
        </td>

        <td>
            <input type="text" name="internal_order[]" id="internal_order" placeholder="Enter Internal Code" data-parsley-required="true" value="%s" />
        </td>

        <td>
            <a id="delete_category_row" class="%s delete" style="cursor:pointer"><i class="material-icons" style="padding:5px 10px;">delete</i></a>
        </td>
    </tr>
EOS; */

$tpl       = <<<EOS
    <tr id="trow-category">
        <td>
            %s
            <input type="hidden" name="category[]" id="category"  value="%s" />
        </td>

        <td>
            <input type="text" name="asset_code[]" id="asset_code" placeholder="Enter Asset Code" data-parsley-type="number" data-parsley-required="true" value="%s" />
        </td>

        <td>
            <input type="text" name="internal_order[]" id="internal_order" placeholder="Enter Internal Code" data-parsley-required="false" value="%s" />
        </td>

    </tr>
EOS;

    if( ! EMPTY($boq_asset_codes))
    {
        $seq = 1;
        foreach($boq_asset_codes as $bac)
        {
            $asset_type  = base64_url_encode($bac['asset_type']);
            $options     = '';
        /*     foreach($categories as $c)
            {
                $selected = $bac['asset_type'] == $c['category_code'] ? 'selected' : '';
                $options .= <<<EOS
                    <option value="{$c['category_code']}" $selected>{$c['category_name']}</option>
EOS;
            } */

            echo sprintf($tpl, $bac['category_name'], $asset_type, $bac['asset_code'], $bac['internal_order']);

            $seq++;
        }
    }
    else
    {

        foreach($categories as $c)
        {
            $options .= <<<EOS
                <option value="{$c['category_code']}">{$c['category_name']}</option>
EOS;
        }

    
        echo sprintf($tpl, 1, $options, '', '', 'hide');
    } 
}
?>