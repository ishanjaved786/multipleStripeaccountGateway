<h1>Switch Stripe Accounts Setting</h1>
<?php if(count($Ssettings) > 0 ){ ?>	
    <button class="newbtn" onClick="NewStripe()">Add New</button>
<?php  } ?>
	<?php 
			$idsArray = array();
			echo '<section>';

			if(count($Ssettings) > 0 ){
				$c = 0;
				foreach ($Ssettings as $item) {
					$c++;
					 $id = $item->ID; 
					 array_push($idsArray,$id);
					 $title = $item->post_title;
					 $options = get_post_meta($id, 'options', true);
					?>

					<h4  <?php if($c == 1){ echo 'class="active"'; } ?> ><i class="fa fa-cc-stripe"></i><?php echo $title; ?></h4>
					<ul>
						<div class="block_main"> 

						<form action="" method="post">
					<h2><?php echo ucfirst($title); ?> &nbsp;&nbsp;&nbsp; <span class="delete_setting" data-id="<?php  echo $id; ?>"><i class="fa fa-trash"></i>&nbsp;Delete Setting</span></h2>

					 <h3 class="ship"> Shipping  <span> <input type="checkbox" name="shipping" data-id="<?php  echo $id; ?>" class="shipcheck" <?php if($id == $shipping_check){ echo 'checked'; } ?> ></span></h3>
					<br>
					<p class="low_title">Stripe Test Keys</p>

					<input type="text" name="tpublic" class="text-80" value="<?php echo @$options['tpublic'] ?>" placeholder="Public key">
					<input type="text" name="tprivate" class="text-80" value="<?php echo @$options['tprivate'] ?>" placeholder="Private key">

					<p class="low_title">Stripe Live Keys</p>

					<input type="text" name="lpublic" class="text-80" value="<?php echo @$options['lpublic'] ?>" placeholder="Public key">
					<input type="text" name="lprivate" class="text-80" value="<?php echo @$options['lprivate'] ?>" placeholder="Private key">


					<p class="low_title">Category </p>
					<select class="text-80 select_<?php  echo $id; ?>" name="tselect[]" multiple="multiple">
						<option>Select categories</option>
						<?php foreach ($product_categories as $key => $value) { ?>
						
						<option value="<?php echo @$value->slug; ?>" <?php if(!empty($options['tselect']) && in_array($value->slug, $options['tselect'])){ echo 'selected'; } ?>><?php echo @$value->name; ?></option>
						
						<?php } ?>

					</select>


					<p class="low_title">Products Id</p>
					<p>Add product ids seperating by comas</p>

					<input type="text" name="tpi" class="text-80" value="<?php echo @$options['tpi'] ?>" placeholder="eg : 81,43">


					<p class="low_title">Variations Id</p>
					<p>Add variations ids seperating by comas</p>

					<input type="text" name="tvi" class="text-80" value="<?php echo @$options['tvi'] ?>" placeholder="eg : 189,211,289">


					<input type="submit" name="tiss" data-id="<?php  echo $id; ?>" class="sbtn" value="Submit">
					</form>

						</div>
					</ul>
					
				<?php } 
					$idsa =	implode(",",$idsArray);
				?>

					<script>
						let ssettings_ids = '<?php  echo $idsa; ?>';
					</script>
	
		<?php	}else{ ?>

				<div class="no_stripe">
				<h2>No Stripe settings available</h2>
				<br>
				<button class="newbtn" onClick="NewStripe()" style="float: none;">Add New</button>
				</div>

			<?php }
			echo '</section>';

    ?>
