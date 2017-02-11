<?php
/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

?>

<div class="single-job-details">

	<div class="inner">


        <div itemprop="description" class="description"><?php the_content(); ?></div>
        
        
        <?php
        $job_bm_responsibilities = get_post_meta(get_the_ID(), 'job_bm_responsibilities', true);
        
        if(!empty($job_bm_responsibilities)){
        
        ?>
        <h3><?php _e('Responsibilities:', job_bm_textdomain); ?></h3>
        <div itemprop="responsibilities" class="responsibilities">
        <?php 
        
        $job_bm_responsibilities = get_post_meta(get_the_ID(), 'job_bm_responsibilities', true);
        
        echo $job_bm_responsibilities;
        
        ?>
         
        </div>
        <?php
        }
        ?>
        
        
        <?php
        $job_bm_education_requirements = get_post_meta(get_the_ID(), 'job_bm_education_requirements', true);
        
        if(!empty($job_bm_education_requirements)){
        
        ?>
        <h3><?php _e('Educational requirements:', job_bm_textdomain); ?></h3>
        <p itemprop="educationRequirements" class="educationRequirements">
        <?php 
        echo $job_bm_education_requirements;
        ?>
        </p>
        <?php
        }
        ?>
        
        
        <?php
        $job_bm_experience_requirements = get_post_meta(get_the_ID(), 'job_bm_experience_requirements', true);
        
        if(!empty($job_bm_experience_requirements)){
        
        ?>
        
        <h3><?php _e('Experience requirements:', job_bm_textdomain); ?></h3>
        <p itemprop="experienceRequirements" class="experienceRequirements">
        <?php 
        
        $job_bm_experience_requirements = get_post_meta(get_the_ID(), 'job_bm_experience_requirements', true);
        
        echo $job_bm_experience_requirements;
        
        ?>
        </p>
        <?php
        }
        ?>
        
        
        <?php
        $job_bm_skills_requirements = get_post_meta(get_the_ID(), 'job_bm_skills_requirements', true);
        
        if(!empty($job_bm_skills_requirements)){
        
        ?>
        <h3><?php _e('Desired Skills:', job_bm_textdomain); ?></h3>
        <p itemprop="skills" class="skills">
        <?php 
        
        $job_bm_skills_requirements = get_post_meta(get_the_ID(), 'job_bm_skills_requirements', true);
        
        echo $job_bm_skills_requirements;
        
        ?>
        </p>
        <?php
        }
        ?>
        
        <?php
        $job_bm_qualifications = get_post_meta(get_the_ID(), 'job_bm_qualifications', true);
        
        if(!empty($job_bm_qualifications)){
        
        ?>
        
        <h3><?php _e('Qualifications:', job_bm_textdomain); ?></h3>
        <p itemprop="qualifications" class="qualifications">
        <?php 
        
        $job_bm_qualifications = get_post_meta(get_the_ID(), 'job_bm_qualifications', true);
        
        echo $job_bm_qualifications;
        
        ?>
        </p>
        <?php
        }
        ?>
	</div>

</div>