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
            
            .history-controls {
                margin-top: 10px;
                margin-bottom: 10px;
            }
            
            .history-delete-btn {
                color: red;
                font-size: 12px;
                cursor: pointer;
                position: absolute;
                right: 5px;
                top: 50%;
                transform: translateY(-50%);
                display: none;
            }
            
            .menu-history a:hover .history-delete-btn {
                display: inline;
            }
            
            .history-search {
                width: 100%;
                margin-bottom: 5px;
                padding: 2px 5px;
                box-sizing: border-box;
            }
        </style>
        <script>
            function initHistorySidebar() {
                // Work with both login screen and main interface
                const historyContainers = document.querySelectorAll('.menu-history, #menu .menu-history');
                
                if (historyContainers.length === 0) return;
                
                historyContainers.forEach(container => {
                    const fieldset = container.closest('fieldset');
                    if (!fieldset) return;
                    
                    // Add search input
                    const searchInput = document.createElement('input');
                    searchInput.type = 'text';
                    searchInput.placeholder = 'Search history...';
                    searchInput.className = 'history-search';
                    searchInput.addEventListener('input', function() {
                        const query = this.value.toLowerCase();
                        const links = container.querySelectorAll('a');
                        links.forEach(link => {
                            const text = link.textContent.toLowerCase();
                            link.style.display = text.includes(query) ? '' : 'none';
                        });
                    });
                    
                    // Add tooltips and delete buttons
                    const items = container.querySelectorAll('a');
                    items.forEach(a => {
                        a.title = a.textContent;
                        
                        // Add delete button to each history item
                        const deleteBtn = document.createElement('span');
                        deleteBtn.textContent = '×';
                        deleteBtn.className = 'history-delete-btn';
                        deleteBtn.title = 'Remove from history';
                        deleteBtn.onclick = function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            if (confirm('Remove this item from history?')) {
                                // We'll use localStorage to track deleted items
                                const deletedHistory = JSON.parse(localStorage.getItem('adminer_deleted_history') || '[]');
                                deletedHistory.push(a.href);
                                localStorage.setItem('adminer_deleted_history', JSON.stringify(deletedHistory));
                                a.parentNode.removeChild(a);
                            }
                        };
                        a.appendChild(deleteBtn);
                    });
                    
                    // Create controls div
                    const controls = document.createElement('div');
                    controls.className = 'history-controls';
                    
                    // Collapse button
                    const toggleBtn = document.createElement('button');
                    toggleBtn.textContent = 'Hide history';
                    toggleBtn.style.marginRight = '5px';
                    toggleBtn.onclick = function() {
                        const hidden = container.style.display === 'none';
                        container.style.display = hidden ? '' : 'none';
                        this.textContent = hidden ? 'Hide history' : 'Show history';
                    };
                    
                    // Clear all button
                    const clearBtn = document.createElement('button');
                    clearBtn.textContent = 'Clear all';
                    clearBtn.onclick = function() {
                        if (confirm('Clear all history items?')) {
                            const links = container.querySelectorAll('a');
                            links.forEach(link => {
                                link.parentNode.removeChild(link);
                            });
                        }
                    };
                    
                    // Add elements in order
                    fieldset.insertBefore(searchInput, container);
                    controls.appendChild(toggleBtn);
                    controls.appendChild(clearBtn);
                    fieldset.appendChild(controls);
                });
                
                // Handle previously deleted items
                const deletedHistory = JSON.parse(localStorage.getItem('adminer_deleted_history') || '[]');
                if (deletedHistory.length > 0) {
                    const allLinks = document.querySelectorAll('.menu-history a, #menu .menu-history a');
                    allLinks.forEach(link => {
                        if (deletedHistory.includes(link.href)) {
                            link.parentNode.removeChild(link);
                        }
                    });
                }
            }
            
            // Execute as soon as possible but also when DOM changes
            document.addEventListener('DOMContentLoaded', initHistorySidebar);
            
            // Also run shortly after page load to catch any dynamically added elements
            setTimeout(initHistorySidebar, 500);
        </script>
        <?php
    }
}
