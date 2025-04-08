<?php

class AdminerTurboHotwire
{
    function head()
    {
        ?>
        <meta name="turbo-prefetch" content="false">
        <!-- <script type="module" src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@latest/dist/turbo.es2017-esm.min.js"></script> -->
        <script <?php echo Adminer\nonce(); ?> type="module" src="/static/turbo.es2017-esm.min.js"></script>
        <?php
    }
}
