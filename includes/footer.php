<?php
$siteDesc = get_setting('site_description', SITE_DESCRIPTION);
?>
<footer class="container footer-note">
    <p><?= sanitize($siteDesc) ?></p>
    <p style="font-size: 0.85rem; margin-top: 12px; border-top: 1px dashed #999; padding-top: 8px;">
        &copy; 2026 Castle of illusions gang. Powered by <a href="https://github.com/IESXS/Sigil-Engine---blog-software" target="_blank"><strong>SigilEngine 2026</strong></a>. 
        Licensed under <a href="https://www.gnu.org/licenses/gpl-3.0.html" target="_blank">GNU GPL 3.0</a>.
    </p>
</footer>
