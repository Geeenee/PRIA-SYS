<?php 
$role_code = "";
$role_name = "";
$disabled = "";
$header = "Create a new role";
$default_system = "";

if(ISSET($role)){
	$role_code = (!EMPTY($role["role_code"]))? $role["role_code"] : "";
	$role_name = (!EMPTY($role["role_name"]))? $role["role_name"] : "";
	$default_system = ( ISSET($role["default_system"]) AND !EMPTY($role["role_name"]))? $role["default_system"] : "";
	$disabled = "disabled";
	$header = "Update role";
}

$salt = gen_salt();
$token = in_salt($role_code, $salt);
?>	  

<input type="hidden" name="id" id="id_roles" value="<?php echo $role_code ?>">
<input type="hidden" name="salt" value="<?php echo $salt ?>">
<input type="hidden" name="token" value="<?php echo $token ?>">
<input type="hidden" id="system_json" value='<?php echo $system_json ?>'>
<div class="form-float-label">
  <div class="row m-n">
	<div class="col s4">
	  <div class="input-field">
		<input type="text" class="validate" required="" aria-required="true" name="role_code" id="role_code" value="<?php echo $role_code ?>" <?php echo $disabled ?>/>
		<label for="role_code" class="required">Code</label>
	  </div>
	</div>
	<div class="col s8">
	  <div class="input-field">
		<input type="text" class="validate" required="" aria-required="true" name="role_name" id="role_name" value="<?php echo $role_name ?>"/>
		<label for="role_name" class="required">Name</label>
	  </div>
	</div>
  </div>
  <div class="row m-n">
	<div class="col s12">
	  <div class="input-field">
		<label class="active required" for="system_role">System</label>
		<select name="system_role[]" required="" aria-required="true" id="system_role" class="" placeholder="Select System" multiple="multiple" >
		  <!-- <option value="">Select System</option> -->
		  <?php foreach($systems as $system): 
				$selected 		= ( !EMPTY( $sel_sys_roles ) AND in_array($system["system_code"], $sel_sys_roles) ) ? "selected" : "";
			?>
			<option value="<?php echo $system["system_code"] ?>" <?php echo $selected ?>><?php echo $system["system_name"] ?></option>				  
		  <?php endforeach; ?>
		</select>
	  </div>
	</div>
  </div>
  <div class="row m-n p-b-xs">
	<div class="col s6">
	  <div class="input-field">
		<label class="active" for="default_system">Default System to be loaded upon logging in</label>
		<select name="default_system" id="default_system" class="selectize-roles" placeholder="Plese select system role first"  >
		  <option value="">Plese select system role first</option>
		  <?php 
		  	if( !EMPTY( $def_sys_opt ) ) :
		  ?>
			<?php 
				foreach( $def_sys_opt as $sys_opt ) :

					$sel_def 	= ( !EMPTY( $default_system ) AND $default_system == $sys_opt['system_code'] ) ? 'selected' : '';
			?>
			<option value="<?php echo $sys_opt['system_code'] ?>" <?php echo $sel_def ?> ><?php echo $sys_opt['system_name'] ?></option>
			<?php 
				endforeach;
			?>

		  <?php 
		  	endif;
		  ?>
		</select>
	  </div>
	</div>
  </div>
</div>