/**
 * Adding a salary field for jobs
 */
add_filter( 'submit_job_form_fields', 'frontend_add_salary_field' );

function frontend_add_salary_field( $fields ) {
  $fields['job']['job_salary'] = array(
    'label'       => __( 'Зарплата (₽)', 'job_manager' ),
    'type'        => 'text',
    'required'    => false,
    'placeholder' => 'З/п не указана',
    'priority'    => 7
  );
  return $fields;
}

add_filter( 'job_manager_job_listing_data_fields', 'admin_add_salary_field' );

function admin_add_salary_field( $fields ) {
  $fields['_job_salary'] = array(
    'label'       => __( 'Зарплата (₽)', 'job_manager' ),
    'type'        => 'text',
    'placeholder' => 'Оставьте пустым если З/п по результатам собеседования',
    'description' => ''
  );
  return $fields;
}

add_action( 'single_job_listing_meta_after', 'display_job_salary_data' );

function display_job_salary_data() {
  global $post;

  $salary = get_post_meta( $post->ID, '_job_salary', true );

  if ( $salary ) {
    echo '<section class="post-section post-sidebar-author cs-d-lg-block">

	<h5 class="title-block">Зарплата</h5>
			<div class="author-wrap">
			<div class="author">
				<div class="author-description">
					<h5 class="title-author">
						<span class="fn">' . esc_html( $salary ) . ' руб.</span></h5>
					</div>
			</div>
		</div>
		
</section>';
  }
  else {	
  	echo '<section class="post-section post-sidebar-author cs-d-lg-block">

	<h5 class="title-block">Зарплата</h5>
			<div class="author-wrap">
			<div class="author">
				<div class="author-description">
					<h5 class="title-author"><span class="fn">На собеседовании</span>
					</h5>
					</div>
			</div>
		</div>
		
</section>';
  }
}
add_action( 'job_listing_meta_salary', 'job_list_salary_data' );

function job_list_salary_data() {
global $post;

  $salary = get_post_meta( $post->ID, '_job_salary', true );

  if ( $salary ) {
    echo '' . esc_html( $salary ) . ' руб.';
  }
  else {	
  	echo 'На собеседовании';
  }
}

/**
 * Remove the preview step. Code goes in theme functions.php or custom plugin.
 */
function custom_submit_job_steps( $steps ) {
	unset( $steps['preview'] );
	return $steps;
}
add_filter( 'submit_job_steps', 'custom_submit_job_steps' );
/**
 * Change button text (won't work until v1.16.2)
 */
function change_preview_text() {
	return __( 'Добавить вакансию' );
}
add_filter( 'submit_job_form_submit_button_text', 'change_preview_text' );
/**
 * Since we removed the preview step and it's handler, we need to manually publish jobs
 * @param  int $job_id
 */
function done_publish_job( $job_id ) {
	$job = get_post( $job_id );
	if ( in_array( $job->post_status, array( 'preview', 'expired' ) ) ) {
		// Reset expirey
		delete_post_meta( $job->ID, '_job_expires' );
		// Update job listing
		$update_job                  = array();
		$update_job['ID']            = $job->ID;
		$update_job['post_status']   = get_option( 'job_manager_submission_requires_approval' ) ? 'pending' : 'publish';
		$update_job['post_date']     = current_time( 'mysql' );
		$update_job['post_date_gmt'] = current_time( 'mysql', 1 );
		wp_update_post( $update_job );
	}
}
add_action( 'job_manager_job_submitted', 'done_publish_job' );
add_filter( 'submit_job_form_fields', 'gma_custom_submit_job_form_fields' );

/**
 * Delete twitter from company info Job
 */
function gma_custom_submit_job_form_fields( $fields ) {
    
    unset($fields['company']['company_twitter']);
    return $fields;
}
