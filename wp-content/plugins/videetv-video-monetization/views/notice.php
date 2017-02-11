<?php global $locator; if(!$locator) exit('Forbidden. hacking attempt'); ?>

<?php foreach($notices as $notice): ?>
	<?php if ($notice['type'] === 'compatibility'): ?>
		<div class="notice notice-error">
			<p>
				<?php echo $notice['error'];?>
			</p>
		</div>
	<?php endif; ?>

	<?php if ($notice['type'] === 'verify'): ?>
		<div class="notice is-dismissible-notice notice-warning dismiss-verify" >
			<p>
				<b> <?php echo sprintf(__('Thanks for using Videe.TV plugin!')) ?> </b> <?php echo sprintf(__('To receive earnings, please', 'videe')); ?> 
				<a href="<?php echo esc_url($locator->admin->getPageUrl('login')); ?>" id="verify">   <?php echo sprintf(__('verify your account', 'videe')) ?>. </a>
			</p>
			<button type="button" class="notice-dismiss-button"><span class="screen-reader-text">Dismiss this notice.</span></button>
		</div>
	<?php endif; ?>


	<?php if ($notice['type'] === 'billing'): ?>
		<div class="notice notice-warning">
			<p>
				<a href="<?php echo esc_url($locator->admin->getPageUrl('settings')); ?>" >
					<?php echo __('Please fill out the payment info form to receive earnings', 'videe'); ?>
				</a>
			</p>
		</div>
	<?php endif; ?>

	<?php if ($notice['type'] === 'new_version'): ?>
		<div>
			<p class="update-nag">
				<?php echo sprintf(__('New version of a «Videe.TV Video Monetization» plugin is available. <a href="%s">Please update now</a>.', 'videe'), admin_url('plugins.php')); ?>

			</p>
		</div>
	<?php endif; ?>

<?php endforeach; ?>
