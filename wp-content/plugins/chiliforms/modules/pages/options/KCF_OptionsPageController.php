<?php
/**
 * Project: ChiliForms.
 * Copyright: 2015-2016 @KonstruktStudio
 */

class KCF_OptionsPageController extends KCF_BaseOptionsPageController {
    public function page_html() {
        $translations = KCF_i18n::get_translations();

        ?>
        <div id="kcf-options-mount" class="kcf-app-mount">
            <div class="loader-fullwidth">
                <div class="loader-fullwidth__inner">
                    <div class="kcf__logo">
                        <div class="kcf__logo-inner">
                            <span class="kcf-app-loader__logo"></span>
                            <span class="kcf-app-header__version"><?php echo esc_html(KCF_VERSION); ?></span>
                        </div>
                    </div>
                    <?php echo esc_html( $translations['forms.loading'] ); ?>
                </div>
            </div>
        </div>
    <?php
    }

}