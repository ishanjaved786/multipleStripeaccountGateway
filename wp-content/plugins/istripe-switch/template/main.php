<h1>Switch Stripe Accounts Setting</h1>
		<div class="tab_container">
			<input id="tab1" type="radio" name="tabs" checked>
			<label for="tab1"><i class="fa fa-cc-stripe"></i><span>Evolve Liverpool Street</span></label>

			<input id="tab2" type="radio" name="tabs">
			<label for="tab2"><i class="fa fa-cc-stripe"></i><span>Evolve Blackfriars</span></label>

			<section id="content1" class="tab-content">

				<form action="" method="post">
				<h3>Evolve Liverpool Street</h3>
				<br>

				<p class="low_title">Stripe Test Keys</p>

				<input type="text" name="t1public" class="text-80" value="<?php echo @$options1['t1public'] ?>" placeholder="Public key">
				<input type="text" name="t1private" class="text-80" value="<?php echo @$options1['t1private'] ?>" placeholder="Private key">

				<p class="low_title">Stripe Live Keys</p>

				<input type="text" name="l1public" class="text-80" value="<?php echo @$options1['l1public'] ?>" placeholder="Public key">
				<input type="text" name="l1private" class="text-80" value="<?php echo @$options1['l1private'] ?>" placeholder="Private key">


				<p class="low_title">Category </p>
				<select class="text-80 t1select" name="t1select[]" multiple="multiple">
					<option>Select categories</option>
					<?php foreach ($product_categories as $key => $value) { ?>
					
					<option value="<?php echo @$value->slug; ?>" <?php if(!empty($options1['t1select']) && in_array($value->slug, $options1['t1select'])){ echo 'selected'; } ?>><?php echo @$value->name; ?></option>
					
					<?php } ?>

				</select>


				<p class="low_title">Products Id</p>
				<p>Add product ids seperating by comas</p>

				<input type="text" name="t1pi" class="text-80" value="<?php echo @$options1['t1pi'] ?>" placeholder="eg : 81,43">


				<p class="low_title">Variations Id</p>
				<p>Add variations ids seperating by comas</p>

				<input type="text" name="t1vi" class="text-80" value="<?php echo @$options1['t1vi'] ?>" placeholder="eg : 189,211,289">


				<input type="submit" name="t1iss" class="sbtn" value="Submit">
				</form>
			</section>

			<section id="content2" class="tab-content">
				

				<form action="" method="post">
				<h3>Evolve Blackfriars Stripe</h3>
				<br>

				<p class="low_title">Stripe Test Keys</p>

				<input type="text" name="t2public" class="text-80" value="<?php echo @$options2['t2public'] ?>" placeholder="Public key">
				<input type="text" name="t2private" class="text-80" value="<?php echo @$options2['t2private'] ?>" placeholder="Private key">

				<p class="low_title">Stripe Live Keys</p>

				<input type="text" name="l2public" class="text-80" value="<?php echo @$options2['l2public'] ?>" placeholder="Public key">
				<input type="text" name="l2private" class="text-80" value="<?php echo @$options2['l2private'] ?>" placeholder="Private key">


				<p class="low_title">Category </p>
				<select class="text-80 t2select" name="t2select[]" multiple="multiple">
				
					<option>Select categories</option>
					<?php foreach ($product_categories as $key => $value) { ?>
					
					<option value="<?php echo @$value->slug; ?>" <?php if(!empty($options2['t2select']) && in_array($value->slug, $options2['t2select'])){ echo 'selected'; } ?>><?php echo @$value->name; ?></option>
					
					<?php } ?>

				</select>


				<p class="low_title">Products Id</p>
				<p>Add product ids seperating by comas</p>

				<input type="text" name="t2pi" class="text-80" value="<?php echo @$options2['t2pi'] ?>" placeholder="eg : 81,43">


				<p class="low_title">Variations Id</p>
				<p>Add variations ids seperating by comas</p>

				<input type="text" name="t2vi" class="text-80" value="<?php echo @$options2['t2vi'] ?>" placeholder="eg : 189,211,289">


				<input type="submit" name="t2iss" class="sbtn" value="Submit">
				</form>

			</section>
		</div>