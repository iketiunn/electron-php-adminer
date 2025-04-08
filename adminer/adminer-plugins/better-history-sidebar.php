<?php

/** Improve Adminer server history sidebar UI/UX */
class AdminerBetterHistorySidebar
{
    function head()
    {
        ?>
        <style>
            /* Target both login page and main interface */
            .menu-history, #menu .menu-history {
                max-height: 300px;
                overflow-y: auto;
                margin-bottom: 10px;
            }

            /* Improved link wrapping for long URLs */
            .menu-history a, #menu .menu-history a, #menu #logins a {
                display: block;
                white-space: normal;
                word-wrap: break-word;
                word-break: break-all;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 200px;
                position: relative;
                padding-right: 20px; /* Space for delete button */
                line-height: 1.4;
                margin-bottom: 3px;
            }

            /* Ensure login links also wrap */
            #logins li {
                max-width: 250px;
                overflow: hidden;
            }

            #logins a {
                white-space: normal !important;
                word-wrap: break-word;
                word-break: break-all;
                display: block;
                line-height: 1.4;
            }

            .menu-history a:hover, #menu .menu-history a:hover, #menu #logins a:hover {
                overflow: visible;
                background: #f0f0f0;
                z-index: 10;
                position: relative;
                color: black;
            }
        </style>
        <?php
    }
}
