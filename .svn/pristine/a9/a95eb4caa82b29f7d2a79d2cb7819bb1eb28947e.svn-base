<!-- <div class="input-field m-n">
	<div class="row m-b-n p-n">
        <div class="col l3 m5 s5   p-r-md">
            <label class="view">Recommended Vendor</label>
        </div>

        <div class="col l5 m6 s6 ">
            <div class="div-task-values"><?php echo $boq_details['recommended_contractor'] ?></div>
        </div>

        <div class="col l4 m1 s1 "></div>
    </div>
</div>
 -->
<!-- <div class="input-field m-n">
	<div class="row m-b-n p-n">
		<div class="col l3 m5 s5   p-r-md">			
			<label class="<?php echo $class_label ?>">Vendor</label>
		</div>

		<div class="col l5 m6 s6 ">
		
        <?php
       /*      if($view)
            {
                echo <<<EOS
                    <div class="div-task-values">{$boq_details['awarded_contractor_name']}</div>
EOS;
            }
            else
            {
                $vendor_code = $boq_details['awarded_contractor'];

                echo <<<EOS
                    <select id="vendor" class="selectize" name="vendor" placeholder="Select vendor" data-parsley-required="true">
                        <option value=""></option>
EOS;

                foreach($contractors as $c)
                {
                    $selected = $vendor_code == $c['vendor_code'] ? 'selected' : '';

                    echo <<<EOS
                        <option value="{$c['vendor_code']}" $selected>{$c['vendor_name']}</option>
EOS;
                }

                echo <<<EOS
                    </select>
EOS;
            }
 */
		?>

		</div>
		<div class="col l4 m1 s1 "></div>
	</div>
</div>
 -->

 
 <div class="input-field m-n">
    <div class="row m-b-n p-n">

         <div class="col l12 m12 s12 p-l-sm p-r-md">
            <label class="view red-text">Note : Please contact administrator if vendor does not appear in the list.</label>
        </div>

         
    </div>
</div>

 <div class="input-field m-n">
    <div class="row m-b-n p-n">


        <div class="col l2 m6 s12  label-col p-r-md">
            <label class="view">Recommended Vendor</label>
        </div>

        <div class="col l2 m6 s12  ">
            <div class="div-task-values"><?php echo $boq_details['recommended_contractor'] ?></div>
        </div>    

        <div class="col l2 m6 s12  label-col p-r-md">
            <label class="view">Business Center</label>
        </div>

        <div class="col l2 m6 s12  ">
            <div class="div-task-values"><?php echo $boq_details['name'] ?></div>
        </div>    


        <div class="col l2 m6 s12  label-col p-r-md">
            <label class="view">Inhouse</label>
        </div>

        <div class="col l2 m6 s12  ">
            <div class="div-task-values"><?php echo ($boq_details['inhouse'] == ENUM_YES) ? 'Yes' : 'No'; ?></div>
        </div>    

    </div>
</div>

<?php if ($started): ?>
<div class="input-field m-n">
    <div class="row m-b-n p-n">
    <div class="col l12 m12 s12 right-align p-r-md"> 
        <?php
            echo ( ! $view) 
                ? 
                '<button type="button" id="btn-add-contractor" class="btn"><i class="material-icons">add</i>Add</button>' 
                : 
                '';
        ?>
    </div>
</div>
<?php endif; ?>

<div class="input-field m-n m-b-lg">
	<div class="row m-b-n p-n">
        <div class="col l12 m12 s12 p-r-md" style="height:30%;width:100%;overflow:auto !important;">
            <table class="table table-default table-layout-auto stripped" id="tbl-contractor" >
                <thead class="text-shadow-dark red darken-3">
                    <tr>
                        <th width="45%">Category</th>
                        <th width="45%">Vendor</th>
                        <?php
                         echo ( ! $view) ? '<th width="10%">Action</th>' : '';
                        ?>
                    </tr>
                </thead>

                <tbody id="tbody-contractor">
                    <?php echo $contractor_view; ?> 
                </tbody>
            </table>
        </div>
    </div>
</div>



