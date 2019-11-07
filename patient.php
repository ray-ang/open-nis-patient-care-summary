<?php

/*
|--------------------------------------------------------------------------
| Patient Custom Post Type
|--------------------------------------------------------------------------
*/

// Register Patient Custom Post Type and Configuration
add_action( 'init', 'rja_register_patient_cpt' );

function rja_register_patient_cpt()
{	
	$labels = array(
	    'name'				=> _x( 'Patients', 'Post type general name', 'open-nis' ),
	    'singular_name'		=> _x( 'Patient', 'Post type singular name', 'open-nis' ),
	    'menu_name'			=> _x( 'Patients', 'Admin Menu text', 'open-nis' ),
	    'name_admin_bar'	=> _x( 'Patient', 'Add New on Toolbar', 'open-nis' ),
	    'add_new'			=> __( 'Add New', 'open-nis' ),
	    'add_new_item'		=> __( 'Add New Patient', 'open-nis' ),
	    'new_item'			=> __( 'New Patient', 'open-nis' ),
	    'edit_item'			=> __( 'Edit Patient', 'open-nis' ),
	    'view_item'			=> __( 'View Patient', 'open-nis' ),
	    'all_items'			=> __( 'All Patients', 'open-nis' ),
	    'search_items'		=> __( 'Search Patients', 'open-nis' ),
	    'not_found'			=> __( 'No patients found.', 'open-nis' ),
	);

	$args = array(
	    'labels'             => $labels,
	    'public'             => true,
	    'publicly_queryable' => true,
	    'show_ui'            => true,
	    'show_in_menu'       => true,
	    'query_var'          => true,
	    'rewrite'            => array( 'slug' => 'patient' ),
	    'capability_type'    => 'post',
	    'has_archive'        => true,
	    'hierarchical'       => true,
	    'menu_position'      => null,
	    'supports'           => array( 'title', 'editor', 'author' ),
	);

	register_post_type( 'patient', $args );
}

// Single Patient Content Template - Before Header
add_action( 'template_redirect', 'rja_single_patient_content_header' );

function rja_single_patient_content_header()
{

	if (isset($_POST['edit-patient']) && wp_verify_nonce($_POST['token'], 'token')) {	

		$patient = array(
			'post_title' => $_POST['room'],
			'post_type' => 'patient',
			'post_status' => 'Publish'
			);	

		$pid = wp_update_post($patient);

		update_metadata( 'post', $pid, 'patient_name', rja_encrypt($_POST['patient-name']) );
		update_metadata( 'post', $pid, 'patient_age', rja_encrypt($_POST['age']) );
		update_metadata( 'post', $pid, 'patient_sex', rja_encrypt($_POST['sex']) );
		update_metadata( 'post', $pid, 'patient_date_admission', rja_encrypt($_POST['admission-date']) );
		update_metadata( 'post', $pid, 'patient_reason', rja_encrypt($_POST['reason']) );
		update_metadata( 'post', $pid, 'patient_allergy', rja_encrypt($_POST['allergy']) );
		update_metadata( 'post', $pid, 'patient_diet', rja_encrypt($_POST['diet']) );
		update_metadata( 'post', $pid, 'patient_iv_access', rja_encrypt($_POST['iv-access']) );
		update_metadata( 'post', $pid, 'patient_monitoring', rja_encrypt($_POST['monitoring']) );
		update_metadata( 'post', $pid, 'patient_urine', rja_encrypt($_POST['urine']) );
		update_metadata( 'post', $pid, 'patient_bowel', rja_encrypt($_POST['bowel']) );
		update_metadata( 'post', $pid, 'patient_history', rja_encrypt($_POST['history']) );
		update_metadata( 'post', $pid, 'patient_medical_notes', rja_encrypt($_POST['medical-notes']) );
		update_metadata( 'post', $pid, 'patient_nursing_plan', rja_encrypt($_POST['nursing-plan']) );

		$link = get_permalink($pid);
		wp_redirect($link);

	}

	if ( isset($_POST['delete-patient']) ) {

		wp_delete_post(get_the_ID());
		wp_redirect(home_url());

	}

}

// Include Javascript and CSS scripts
add_action( 'wp_head', 'rja_single_patient_header_script' );

function rja_single_patient_header_script()
{
	?>
	<script>
		function editPatientForm() {
		  var x = document.getElementById("editPatient");
		  if (x.style.display == "none") {
		    x.style.display = "block";
		  } else {
		    x.style.display = "none";
		  }
		}
	</script>
	<style type="text/css">
		@media print {
		    .site-header, .site-navigation, .main-navigation, .entry-header, .entry-title, #no-print, .widget-area, .site-footer {display: none;}
		}
	</style>
	<?php
}

// Single Patient Content Template
add_filter( 'the_content', 'rja_single_patient_content' );

function rja_single_patient_content()
{

	if ( get_post_type() == 'patient' ) {

		if ( current_user_can('administrator') || current_user_can('nurse') ) {

			$patient = get_metadata( 'post', get_the_ID() );

			$patient_name = rja_decrypt($patient['patient_name'][0]);
			$patient_age = rja_decrypt($patient['patient_age'][0]);
			if ( rja_decrypt($patient['patient_sex'][0]) == 'M' ) { $patient_sex = 'Male'; } else { $patient_sex = 'Female'; }
			$patient_date_admission = rja_decrypt($patient['patient_date_admission'][0]);
			$patient_reason = rja_decrypt($patient['patient_reason'][0]);
			$patient_allergy = rja_decrypt($patient['patient_allergy'][0]);
			$patient_diet = rja_decrypt($patient['patient_diet'][0]);
			$patient_iv_access = rja_decrypt($patient['patient_iv_access'][0]);
			$patient_monitoring = rja_decrypt($patient['patient_monitoring'][0]);
			$patient_urine = rja_decrypt($patient['patient_urine'][0]);
			$patient_bowel = rja_decrypt($patient['patient_bowel'][0]);
			$patient_history = rja_decrypt($patient['patient_history'][0]);
			$patient_medical_notes = rja_decrypt($patient['patient_medical_notes'][0]);
			$patient_nursing_plan = rja_decrypt($patient['patient_nursing_plan'][0]);

			?>
			<h3>Room: <?php esc_html(the_title()); ?></h3>
			<p>Name: <?= esc_html($patient_name); ?><br />
			Age: <?= esc_html($patient_age); ?><br />
			Sex: <?= esc_html($patient_sex); ?></p>
			<p>Admission Date: <?= esc_html($patient_date_admission); ?><br />
			Reason: <?= esc_html($patient_reason); ?><br />
			Allergy: <?= esc_html($patient_allergy); ?><br />
			Diet: <?= esc_html($patient_diet); ?></p>
			<p>IV Access: <?= esc_html($patient_iv_access); ?><br />
			Monitoring: <?= esc_html($patient_monitoring); ?><br />
			Urine: <?= esc_html($patient_urine); ?><br />
			Bowel Movement: <?= esc_html($patient_bowel); ?></p>
			<p><em>History:</em><br /><?= nl2br(esc_html($patient_history)); ?></p>
			<p><em>Medical Notes:</em><br /><?= nl2br(esc_html($patient_medical_notes)); ?></p>
			<p><em>Nursing Plan of Care:</em><br /><?= nl2br(esc_html($patient_nursing_plan)); ?></p>
			<div id="no-print">
				<h2>Edit Information</h2>
				<button onclick="editPatientForm()">Edit Form</button>
				<button onclick="window.print()">Print</button>
				<div id="editPatient" style="display: none;">
					<p></p>
					<form method="post">
				    	<p><label for="room">Room</label><br />
							<input type="text" id="room" name="room" value="<?php esc_html(the_title()); ?>" required />
				        </p>
				    	<p><label for="name">Name</label><br />
				   			<input type="text" id="patient-name" name="patient-name" value="<?= esc_html($patient_name); ?>" required />
				        </p>
				        <p><label for="age">Age</label><br />
				        	<input type="number" id="age" name="age" value="<?= esc_html($patient_age); ?>" required />
				        </p>
				        <p><label for="sex">Sex</label><br />
				            <select id="sex" name="sex" size="2" required />
								<option value="M" <?php if (rja_decrypt($patient['patient_sex'][0])=='M') echo 'selected="selected"'; ?>>Male</option>
								<option value="F" <?php if (rja_decrypt($patient['patient_sex'][0])=='F') echo 'selected="selected"'; ?>>Female</option>
				            </select>
				        </p>
				        <p><label for="admission-date">Date of Admission</label><br />
				    		<input type="date" id="admission-date" name="admission-date" value="<?= esc_html($patient_date_admission); ?>" required />
				        </p>
				        <p><label for="reason">Reason for Admission</label><br />
				            <input type="text" id="reason" name="reason" value="<?= esc_html($patient_reason);?>" required /><br />
				        </p>
						<p><label for="allergy">Allergy</label><br />
							<input type="text" id="allergy" name="allergy" value="<?= esc_html($patient_allergy);?>" required /><br />
						</p>
				        <p><label for="diet">Diet</label><br />
				            <input type="text" id="diet" name="diet" value="<?= esc_html($patient_diet);?>" required /><br />
				        </p>
						<p><label for="iv-access">IV Access</label><br />
							<input type="text" id="iv-access" name="iv-access" value="<?= esc_html($patient_iv_access);?>" required /><br />
						</p>
						<p><label for="monitoring">Monitoring</label><br />
							<input type="text" id="monitoring" name="monitoring" value="<?= esc_html($patient_monitoring);?>" required /><br />
						</p>
						<p><label for="urine">Urine</label><br />
							<input type="text" id="urine" name="urine" value="<?= esc_html($patient_urine);?>" required /><br />
						</p>
						<p><label for="bowel">Bowel Movement</label><br />
							<input type="text" id="bowel" name="bowel" value="<?= esc_html($patient_bowel);?>" required /><br />
						</p>
				        <p><label for="history">History</label><br />
				            <textarea id="history" name="history" required><?= esc_html($patient_history); ?></textarea>
				        </p>
						<p><label for="medical-notes">Medical Notes</label><br />
						    <textarea id="medical-notes" name="medical-notes" required><?= esc_html($patient_medical_notes); ?></textarea>
						</p>
						<p><label for="nursing-plan">Nursing Plan of Care</label><br />
						    <textarea id="nursing-plan" name="nursing-plan" required><?= esc_html($patient_nursing_plan); ?></textarea>
						</p>
				        <p><input type="submit" value="Edit Patient" id="edit-patient" name="edit-patient" /></p>
						<?php if (current_user_can('administrator')) : ?>
						<p><input type="submit" value="Delete Patient" id="delete-patient" name="delete-patient" onclick="return confirm('Are you sure you want to delete this patient?');" /></p>
						<?php endif; ?>
				        <button onclick="editPatientForm()">Close Form</button>
				        <?php wp_nonce_field( 'token', 'token' ); ?>
					</form>
				</div>
			</div>
			<?php			
		} else {
			?>
			<style>
				.entry-header {display: none;}
			</style>
			<p>You do not have permission to view patient.</p>
			<?php
		}

	} else {

		return get_the_content();

	}

}