<div id="acf-hidden-wp-editor" style="display: none;">
    <div id="wp-acf_content-wrap" class="wp-core-ui wp-editor-wrap html-active">
        <div id="wp-acf_content-editor-tools" class="wp-editor-tools hide-if-no-js">
            <div id="wp-acf_content-media-buttons" class="wp-media-buttons">
                <button type="button" class="button insert-media add_media" data-editor="acf_content" aria-haspopup="dialog" aria-controls="wp-media-modal">
                    <span class="wp-media-buttons-icon" aria-hidden="true"></span>
                    افزودن پروندهٔ چندرسانه‌ای
                </button>
            </div>
            <div class="wp-editor-tabs">
                <button type="button" id="acf_content-tmce" class="wp-switch-editor switch-tmce" data-wp-editor-id="acf_content">دیداری</button>
                <button type="button" id="acf_content-html" aria-pressed="true" class="wp-switch-editor switch-html" data-wp-editor-id="acf_content">کد</button>
            </div>
        </div>
        <div id="wp-acf_content-editor-container" class="wp-editor-container">
            <div id="qt_acf_content_toolbar" class="quicktags-toolbar hide-if-no-js"></div>
            <textarea class="wp-editor-area" rows="20" autocomplete="off" cols="40" name="acf_content" id="acf_content"></textarea>
        </div>
    </div>
</div>

<div id="post-lock-dialog" class="notification-dialog-wrap hidden">
    <div class="notification-dialog-background"></div>
    <div class="notification-dialog">
        <div class="post-taken-over">
            <div class="post-locked-avatar"></div>
            <p class="wp-tab-first" tabindex="0">
                <span class="currently-editing"></span><br />
                <span class="locked-saving hidden"><img src="https://sornaz.com/wp-admin/images/spinner-2x.gif" width="16" height="16" alt="" /> ذخیرهٔ رونوشت&hellip;</span>
                <span class="locked-saved hidden">آخرین تغییرات شما به صورت یک رونوشت ذخیره شده است.</span>
            </p>
            <p><a class="button button-primary wp-tab-last" href="https://sornaz.com/wp-admin/edit.php">همهٔ نوشته‌ها</a></p>
        </div>
    </div>
</div>

<div id="local-storage-notice" class="notice is-dismissible hidden">
    <p class="local-restore">نسخه پشتیبان این نوشته در مرورگر شما با نگارش زیر متفاوت است.
        <button type="button" class="button restore-backup">بازیابی نسخهٔ پشتیبان</button>
    </p>
    <p class="help">این کار محتوای کنونی ویرایشگر را با آخرین نسخهٔ ذخیره‌شده جایگزین می‌کند. می‌توانید از دکمه‌های بازگردانی/بازانجام برای بازگرداندن محتوای پیشین یا استفاده از محتوای تازه استفاده کنید.</p>
</div>

<div id="wp-auth-check-wrap" class="hidden">
    <div id="wp-auth-check-bg"></div>
    <div id="wp-auth-check">
        <button type="button" class="wp-auth-check-close button-link"><span class="screen-reader-text">بستن کادر</span></button>
        <div id="wp-auth-check-form" class="loading" data-src="https://sornaz.com/wp-login.php?interim-login=1&#038;wp_lang=fa_IR"></div>
        <div class="wp-auth-fallback">
            <p><b class="wp-auth-fallback-expired" tabindex="0">نشست باطل شده است</b></p>
            <p>
                <a href="https://sornaz.com/wp-login.php" target="_blank">لطفاً دوباره وارد شوید.</a>
                برگهٔ ورود در یک پنجرهٔ تازه باز خواهد شد. پس از ورود می‌توانید پنجره را بسته و به این برگه بازگردید.
            </p>
        </div>
    </div>
</div>

<div id="wp-link-backdrop" style="display: none"></div>
<div id="wp-link-wrap" class="wp-core-ui" style="display: none" role="dialog" aria-modal="true" aria-labelledby="link-modal-title">
    <form id="wp-link" tabindex="-1">
        <input type="hidden" id="_ajax_linking_nonce" name="_ajax_linking_nonce" value="88f59ca659" />		<h1 id="link-modal-title">درج/ویرایش پیوند</h1>
        <button type="button" id="wp-link-close"><span class="screen-reader-text">
            بستن		</span></button>
        <div id="link-selector">
            <div id="link-options">
                <p class="howto" id="wplink-enter-url">نشانی مقصد را وارد نمایید</p>
                <div>
                    <label><span>نشانی اینترنتی</span>
                    <input id="wp-link-url" type="text" aria-describedby="wplink-enter-url" /></label>
                </div>
                <div class="wp-link-text-field">
                    <label><span>متن پیوند</span>
                    <input id="wp-link-text" type="text" /></label>
                </div>
                <div class="link-target">
                    <label><span></span>
                    <input type="checkbox" id="wp-link-target" /> بازکردن پیوند در زبانه تازه</label>
                </div>
            </div>
            <p class="howto" id="wplink-link-existing-content">یا پیوند به محتوای موجود</p>
            <div id="search-panel">
                <div class="link-search-wrapper">
                    <label>
                        <span class="search-label">جستجو</span>
                        <input type="search" id="wp-link-search" class="link-search-field" autocomplete="off" aria-describedby="wplink-link-existing-content" />
                        <span class="spinner"></span>
                    </label>
                </div>
                <div id="search-results" class="query-results" tabindex="0">
                    <ul></ul>
                    <div class="river-waiting">
                        <span class="spinner"></span>
                    </div>
                </div>
                <div id="most-recent-results" class="query-results" tabindex="0">
                    <div class="query-notice" id="query-notice-message">
                        <em class="query-notice-default">معیاری برای جستجو مشخص نشده است. نمایش دادن آخرین موارد.</em>
                        <em class="query-notice-hint screen-reader-text">جستجو کنید یا برای انتخاب موارد، از کلیدهای جهت بالا و پایین استفاده کنید.</em>
                    </div>
                    <ul></ul>
                    <div class="river-waiting">
                        <span class="spinner"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="submitbox">
            <div id="wp-link-cancel">
                <button type="button" class="button">لغو</button>
            </div>
            <div id="wp-link-update">
                <input type="submit" value="افزودن پیوند" class="button button-primary" id="wp-link-submit" name="wp-link-submit">
            </div>
        </div>
    </form>
</div>
<div class="clear"></div>