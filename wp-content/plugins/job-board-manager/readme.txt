=== Job Board Manager ===
	Contributors: pickplugins
	Donate link: http://pickplugins.com
	Tags:  Job Board Manager, Job Board, job portal, Job, Job Poster, job manager, job, job list, job listing, Job Listings, job lists, job management, job manager,
	Requires at least: 4.1
	Tested up to: 4.7
	Stable tag: 2.0.20
	License: GPLv2 or later
	License URI: http://www.gnu.org/licenses/gpl-2.0.html

	Awesome Job Board Manager

== Description ==


Creating job for WordPress made easy by “Job Board Manager” plugin. super lightweight plugin allows you to create job manager site employer can submit job and employee can apply for job.

Based on short-code made easy to use anywhere displaying job-list, single job page & etc.

Easy to customizable made this plugin supper developer friendly , you can add your own values for some options via filter hook. You can create unlimited themes for job archive page & job single page by filter hook.

### Job Board Manager by http://pickplugins.com
* [Live Demo!&raquo;](http://www.pickplugins.com/demo/job-board-manager/)
* [Plugin details!&raquo;](http://www.pickplugins.com/item/job-board-manager-create-job-site-for-wordpress/)
* [Documentation!&raquo;](http://www.pickplugins.com/docs/documentation/job-board-manager/)

<strong>Video tutorial</strong>

* [How to Install and Settings &raquo;](https://www.youtube.com/watch?v=avfOO82Kz2g)
* [Create a job &raquo;](https://www.youtube.com/watch?v=KZygrlmNrE8)


<strong>Add-on for Job Board Manager</strong>

<b>Free</b>

* [Locations &raquo;](https://wordpress.org/plugins/job-board-manager-locations/)
* [Company Profile &raquo;](https://wordpress.org/plugins/job-board-manager-company-profile/)
* [Expired Check &raquo;](https://wordpress.org/plugins/job-board-manager-expired-check/)
* [Widgets &raquo;](https://wordpress.org/plugins/job-board-manager-widgets/)
* [Breadcrumb &raquo;](https://wordpress.org/plugins/job-board-manager-breadcrumb/)


<b>Premium</b>

* [Saved Jobs &raquo;](http://www.pickplugins.com/item/job-board-manager-saved-jobs/)
* [Application Manager &raquo;](http://www.pickplugins.com/item/job-board-manager-application-manager/)
* [WooCommerce Paid Listing &raquo;](http://www.pickplugins.com/item/job-board-manager-woocommerce-paid-listing/)
* [Stats &raquo;](http://www.pickplugins.com/item/job-board-manager-stats/)
* [Categories &raquo;](http://www.pickplugins.com/item/job-board-manager-categories/)
* [Job List Ads &raquo;](http://www.pickplugins.com/item/job-board-manager-job-list-ads/)
* [Search & Filter &raquo;](http://www.pickplugins.com/item/job-board-manager-search/)
* [Job Feed &raquo;](http://www.pickplugins.com/item/job-board-manager-job-feed/)
* [Report Job &raquo;](http://www.pickplugins.com/item/job-board-manager-report-job/)


<strong>Plugin Features</strong>

* schema.org support.
* Job list with pagination support via short-codes.
* Job single page.
* Extensible supported setting page by filter hook.
* reCAPTCHA for job submission form.
* Notification email for new job posted, published.
* Extensible supported job meta input by filter hook.
* Front-End job submission form via short-codes
* Featured job marker.

<strong>Job List page</strong>

Use this short-code `[job_list]` to display latest job with pagination

<strong>Front-End Job submit form</strong>

Use this short-code `[job_submit_form]` to display new job submission form.

<strong>Front-End Job edit form</strong>

Use this short-code `[job_bm_job_edit]` to display new job edit page.

<strong>Front-End My Account form</strong>

Use this short-code `[job_bm_my_account]` to display new account page for login & register form.

<strong>Client job list</strong>

Display list of jobs posted by logged in clients/employer by using following short-code `[client_job_list]`

<strong>Filters job type</strong>

you can add your job type by filter hook as following example bellow.

`
function job_bm_filters_job_type_extra($job_type){
	
	$job_type_new = array('job_type_1'=>'Job Type 1','job_type_2'=>'Job Type 2');
	$job_type = array_merge($job_type,$job_type_new);
	
	return $job_type;
		
	}
add_filter('job_bm_filters_job_type','job_bm_filters_job_type_extra');
`

<strong>Filters salary type</strong>

you can add your salary type by filter hook as following example bellow.

`
function job_bm_filters_salary_type_extra($salary_type){
	
	$salary_type_new = array('salary_type_1'=>'Salary Type 1','salary_type_1'=>'Salary Type 2',);
	$salary_type = array_merge($salary_type,$salary_type_new);
	
	return $salary_type;
		
	}
add_filter('job_bm_filters_salary_type','job_bm_filters_salary_type_extra');
`

<strong>Extend meta fields</strong>

If you need some extra input fields under job post type you can use filter hook as following, currently support input fileds are text, textarea, radio, select, checkbox, multi-text,

Please see the file <strong>includes/class-post-meta.php</strong> for example option input by array. 

`
function job_bm_filter_job_input_fields_extra($input_fields){
    

	$meta_fields = $input_fields['meta_fields'];
	
	// Add new fields in company tab , default meta group is company_info, job_info, salary_info, application
	
	$company_info = $meta_fields['company_info']['meta_fields'];

    $company_field_extra[] = array(
									'meta_key'=>'job_bm_company_extra',
									'css_class'=>'company_extra',
									'placeholder'=>__('Write Company Name Extra.',job_bm_textdomain),					
									'title'=>__('Company Extra',job_bm_textdomain),
									'option_details'=>__('Company Extra, ex: Google Inc.',job_bm_textdomain),						
									'input_type'=>'text', // text, radio, checkbox, select, 
									'input_values'=>'', // could be array
									);
			
									
	$company_info = array_merge($company_info, $company_field_extra);						
	
	$input_fields['meta_fields']['company_info']['meta_fields'] = 	$company_info;

									
	return $input_fields;
        
    }

add_filter('job_bm_filter_job_input_fields','job_bm_filter_job_input_fields_extra');
`


<strong>Translation</strong>

Pluign is translation ready , please find the 'en.po' for default transaltion file under 'languages' folder and add your own translation. you can also contribute in translation, please contact us http://www.pickplugins.com/contact/

Contributors 

* [Italian , Criss Seregni  &raquo;](http://www.agendadelvolo.info)
* [German , Britta Skulima  &raquo;](http://www.deardesign.de)
* [Portuguese , Susana Araújo  &raquo;](http://www.epochmultimedia.com/)


== Installation ==

1. Install as regular WordPress plugin.<br />
2. Go your plugin setting via WordPress Dashboard and find "<strong>Job Board Manager</strong>" activate it.<br />




== Screenshots ==

1. List of latest job with pagination.
2. Single job page.
3. Job submit input admin side.
4. Settings page style tab
5. Client job list
6. Front-end Job Submission form.
7. Ready addons for Job Board Manager.


== Frequently Asked Questions ==

= Single job page showing 404 error , how to solve ? =

Pelase go "Settings > Permalink Settings" and save again to reset permalink.


= Single job page style broken, what should i do ? =

you nedd to define container for single job page as come with your theme, please add following code to your theme functions.php file
`
add_action('job_bm_action_before_single_job', 'job_bm_action_before_single_job', 10);
add_action('job_bm_action_after_single_job', 'job_bm_action_after_single_job', 10);

function job_bm_action_before_single_job() {
  echo '<div class="content-wrapper ">';
}

function job_bm_action_after_single_job() {
  echo '</div>';
}
`





== Changelog ==



	= 2.0.20 =
    * 13/11/2016 - add - translation file update

	= 2.0.19 =
    * 13/11/2016 - add - job view count.
    * 16/11/2016 - add - Minor php error fixed.	

	= 2.0.18 =
    * 12/11/2016 - fix - excerpt word count issue fixed.

	= 2.0.17 =
    * 12/11/2016 - update - security issue update.

	= 2.0.16 =
    * 11/11/2016 - update - security issue update.

	= 2.0.15 =
    * 21/10/2016 - update - admin editing is back.

	= 2.0.14 =
    * 06/10/2016 - add - reset eamil templates.
    * 10/10/2016 - add - redirect job preview after submit job.
    * 10/10/2016 - add - job submission type step by step and accordion.
    * 18/10/2016 - add - added FAQ on help page.
	

	= 2.0.13 =
    * 08/09/2016 - add - categories added on archive meta.
    * 08/09/2016 - fix - social share issue fixed.	
    * 08/09/2016 - add - create demo job category on plugin activation.
    * 09/09/2016 - removed - Field Editor feature removed.
    * 09/09/2016 - add - company logo upload on job submission.		


	= 2.0.12 =
    * 03/09/2016 - Fix - transaltion issue fixed.
	
	= 2.0.11 =
    * 02/09/2016 - Fix - Single job page CSS minor issue fixed.

	= 2.0.10 =
    * 01/09/2016 - add - salary range added.
	
	= 2.0.9 =
    * 10/08/2016 - add - report for custom two date.

	= 2.0.8 =
    * 27/07/2016 - add - report menu added for job stats.

	= 2.0.7 =
    * 07/07/2016 - add - default apply method "none" to hide.
	* 24/07/2016 - add - Portuguese  translation file.

	= 2.0.6 =
    * 02/07/2016 - add - Dashboard widget to display last 7 days stats.

	= 2.0.5 =
    * 01/07/2016 - add - added action on my account.
	

	= 2.0.4 =
    * 29/06/2016 - add - German translation added, by Britta Skulima.

	= 2.0.3 =
    * 29/06/2016 - add - validations only for required fields.
    * 29/06/2016 - fix - job category issue on edit page.	

	= 2.0.2 =
    * 27/06/2016 - add - Job Meta fields editor.

	= 2.0.1 =
    * 23/06/2016 - add - application can't submit on expired job.
    * 23/06/2016 - update - Client job list update UI.
    * 23/06/2016 - add - Client can delete their own jobs.	
    * 23/06/2016 - add - data validation of job submit.	
    * 23/06/2016 - add - Job edit page.
    * 23/06/2016 - add - Logged in user will Redirect to preview job after job submit.	
	* 23/06/2016 - add - new option "Can user delete jobs ?".
	* 23/06/2016 - add - new option "Can user edit jobs ?".
	* 23/06/2016 - add - Share button on single job page.	

	= 2.0.0 =
    * 30/05/2016 - add - added job categories.
    * 30/05/2016 - add - added job tags.
    * 30/05/2016 - add - added welcome page to save initial settings.	
		

	= 1.0.30 =
    * 22/04/2016 - add - Italian translation file.
	
	= 1.0.29 =
    * 22/04/2016 - update - minor update.

	= 1.0.28 =
    * 21/04/2016 - update - single job page improvement.
    * 21/04/2016 - removed - featured image for job post type removed.	

	= 1.0.27 =
    * 20/04/2016 - fix - single job width for mobile device issue fixed.

	= 1.0.26 =
    * 07/04/2016 - add - added video tutorials.
	
	= 1.0.25 =
    * 02/04/2016 - update - Minor CSS update.
    * 02/04/2016 - update - Minor PHP issue update.
    * 02/04/2016 - Remove - $ symbol removed form salary on single job sidebar.
    * 02/04/2016 - fix - featured job background color issue fixed.

	= 1.0.24 =
    * 21/03/2016 - removed - removed apply method from job post meta.
    * 21/03/2016 - add - add option apply methods for jobs.	

	= 1.0.23 =
    * 18/03/2016 - fix - Salary currency issue fixed.
	
	= 1.0.22 =
    * 17/03/2016 - fix - Featured job checkbox issue fixed.
    * 17/03/2016 - add - new filter added "job_bm_filter_file_upload_extensions".
    * 17/03/2016 - removed - option removed "Hide Admin Bar to Revoke Access".

	= 1.0.21 =
    * 12/03/2016 - fix - single job minor css issue fixed.

	= 1.0.20 =
    * 16/02/2016 - fix - Fix minor php issue.
    * 16/02/2016 - add - Added log file.	

	= 1.0.19 =
    * 11/02/2016 - add - added more fields.

	= 1.0.18 =
    * 06/02/2016 - remove - removed single job short-code.

	= 1.0.17 =
    * 03/02/2016 - update - demo company logo update.
    * 03/02/2016 - update - demo email logo update.
    * 03/02/2016 - update - Removed option "Listing Duration".       

	= 1.0.16 =
    * 02/02/2016 - add - Customize email templates.

	= 1.0.15 =
    * 02/02/2016 - add - admin UI update.

	= 1.0.13 =
    * 26/12/2015 - fix - fixed minor php issue.
    
	= 1.0.13 =
    * 18/11/2015 - add - auto display job content on job single page.
    
	= 1.0.12 =
    * 09/11/2015 - add - Excerpt display from content automatically or short content.
    * 09/11/2015 - add - Setting UI update.    

	= 1.0.11 =
    * 16/09/2015 - fix - minor php error fixed in job list.
    
	= 1.0.10 =
    * 08/09/2015 - add - sanitization for job front submission form.

	= 1.0.9 =
    * 03/09/2015 - add - option for pages.
    * 03/09/2015 - add - display job list by job type, job status, expiry date.
    
	= 1.0.8 =
    * 02/09/2015 - add - Client job list.

    
	= 1.0.7 =
    * 01/09/2015 - fix - job submission email issue fixed.
    
	= 1.0.6 =
    * 30/08/2015 - add - date picker for job submit form.
    * 30/08/2015 - add - new job status 'expired' added.
    * 30/08/2015 - add - Emails Templates for email notification.    

	= 1.0.5 =
    * 24/08/2015 - add - front-end job submit form validation check.
    
	= 1.0.4 =
    * 24/08/2015 - add - front-end job submit form.
    * 24/08/2015 - add - reCAPTCHA for job submit form.
    * 24/08/2015 - add - New Submitted Job Status.       
    
	= 1.0.3 =
    * 14/08/2015 - add - company page link to job & job list.
    
	= 1.0.2 =
    * 10/08/2015 - add - company page link to job & job list.

	= 1.0.1 =
    * 10/08/2015 - add -Menu page for addons list.
    
	= 1.0.0 =
    * 05/08/2015 Initial release.
