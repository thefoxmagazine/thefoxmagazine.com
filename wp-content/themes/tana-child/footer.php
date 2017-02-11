        <?php 
        if(Tana_Std::get_mod('footer_disable') !== '1') :
        	get_template_part('templates/tpl', 'footer-content');
        endif;
        ?>

	</div>
	<!--// .wrapper -->
	<?php wp_footer(); ?>

</body>
</html>