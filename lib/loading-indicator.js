// Function to handle loading indicator
function setupLoadingIndicator(window) {
  // Create spinner HTML with improved animation
  const spinnerHtml = `
    <div id="electron-loading-overlay" style="
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      display: flex;
      justify-content: center;
      align-items: center;
      background-color: rgba(255, 255, 255, 0.3);
      z-index: 9999;
    ">
      <div id="electron-spinner" style="
        border: 5px solid #f3f3f3;
        border-top: 5px solid #3498db;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: electron-spin 1s linear infinite !important;
      "></div>
    </div>
    <style id="electron-spinner-style">
      @keyframes electron-spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
      }
      #electron-spinner {
        animation: electron-spin 1s linear infinite !important;
        -webkit-animation: electron-spin 1s linear infinite !important;
      }
      @-webkit-keyframes electron-spin {
        0% { -webkit-transform: rotate(0deg); }
        100% { -webkit-transform: rotate(360deg); }
      }
    </style>
  `;

  // Function to show loading spinner
  const showLoadingSpinner = () => {
    window.webContents.executeJavaScript(`
      (function() {
        // Wait for document.body to be available
        if (document.body) {
          if (!document.getElementById('electron-loading-overlay')) {
            const div = document.createElement('div');
            div.innerHTML = \`${spinnerHtml}\`;
            document.body.appendChild(div.firstElementChild);
          }
          return true;
        }
        return false;
      })();
    `)
    .then(result => {
      if (!result) {
        // If body wasn't ready, try again after a short delay
        setTimeout(() => {
          window.webContents.executeJavaScript(`
            if (document.body && !document.getElementById('electron-loading-overlay')) {
              const div = document.createElement('div');
              div.innerHTML = \`${spinnerHtml}\`;
              document.body.appendChild(div.firstElementChild);
            }
          `).catch(err => console.error('Could not inject spinner on retry:', err));
        }, 200);
      }
    })
    .catch(err => console.error('Could not inject spinner:', err));
  };

  // Function to hide loading spinner
  const hideLoadingSpinner = () => {
    setTimeout(() => {
      window.webContents.executeJavaScript(`
        (function() {
          try {
            const overlay = document.getElementById('electron-loading-overlay');
            const style = document.getElementById('electron-spinner-style');
            if (overlay && document.body.contains(overlay)) {
              overlay.remove();
              return true;
            }
            if (style && document.head.contains(style)) {
              style.remove();
            }
            return false;
          } catch (e) {
            console.error('Error removing overlay:', e);
            return false;
          }
        })();
      `)
      .catch(err => console.error('Could not execute overlay removal script:', err));
    }, 100);
  };

  // Create navigation monitor script - this will be injected once and remain active
  const navigationMonitorScript = `
    (function() {
      // Only set up once
      if (window._electronNavigationMonitorActive) return;
      window._electronNavigationMonitorActive = true;
      
      // Track loading state
      let isLoading = false;
      
      // Create spinner element to be reused - improved spinner with working animation
      const spinnerHtml = \`
        <div id="electron-loading-overlay" style="
          position: fixed;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          display: flex;
          justify-content: center;
          align-items: center;
          background-color: rgba(255, 255, 255, 0.3);
          z-index: 9999;
        ">
          <div id="electron-spinner" style="
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: electron-spin 1s linear infinite !important;
          "></div>
        </div>
        <style id="electron-spinner-style">
          @keyframes electron-spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
          }
          #electron-spinner {
            animation: electron-spin 1s linear infinite !important;
            -webkit-animation: electron-spin 1s linear infinite !important;
          }
          @-webkit-keyframes electron-spin {
            0% { -webkit-transform: rotate(0deg); }
            100% { -webkit-transform: rotate(360deg); }
          }
        </style>
      \`;
      
      // Function to show spinner
      function showSpinner() {
        if (document.body && !document.getElementById('electron-loading-overlay')) {
          const div = document.createElement('div');
          div.innerHTML = spinnerHtml;
          document.body.appendChild(div.firstElementChild);
          document.head.appendChild(div.firstElementChild); // Add the style to head
        }
      }
      
      // Function to hide spinner
      function hideSpinner() {
        const overlay = document.getElementById('electron-loading-overlay');
        const style = document.getElementById('electron-spinner-style');
        if (overlay && document.body.contains(overlay)) {
          overlay.remove();
        }
        if (style && document.head.contains(style)) {
          style.remove();
        }
      }
      
      // Track all link clicks for navigation
      document.addEventListener('click', function(e) {
        // Find if a link was clicked
        let element = e.target;
        while (element && element !== document) {
          if (element.tagName === 'A') {
            const href = element.getAttribute('href');
            if (href && !href.startsWith('#') && !href.startsWith('javascript:')) {
              showSpinner();
              break;
            }
          }
          element = element.parentElement;
        }
      }, true);
      
      // Track all form submissions
      document.addEventListener('submit', function(e) {
        showSpinner();
      }, true);
      
      // Track all button clicks that might submit forms
      document.addEventListener('click', function(e) {
        let element = e.target;
        while (element && element !== document) {
          if (element.tagName === 'BUTTON' || 
              (element.tagName === 'INPUT' && 
               (element.type === 'submit' || element.type === 'button'))) {
            showSpinner();
            break;
          }
          element = element.parentElement;
        }
      }, true);
      
      // Use MutationObserver to detect when content is actively changing
      // which might indicate loading without a full navigation
      let contentChangingTimer = null;
      const observer = new MutationObserver(function(mutations) {
        // If significant DOM changes are happening, show the spinner
        if (mutations.length > 5) {  // Threshold for "significant change"
          if (!isLoading) {
            isLoading = true;
            showSpinner();
          }
          
          // Reset the timer each time we see changes
          clearTimeout(contentChangingTimer);
          contentChangingTimer = setTimeout(function() {
            isLoading = false;
            hideSpinner();
          }, 500);  // Wait for 500ms of no changes before hiding
        }
      });
      
      // Start observing the document with configured parameters
      observer.observe(document.documentElement, {
        childList: true,
        subtree: true,
        attributes: false,
        characterData: false
      });
      
      // Override history methods to catch SPA-style navigation
      const originalPushState = history.pushState;
      const originalReplaceState = history.replaceState;
      
      history.pushState = function() {
        showSpinner();
        originalPushState.apply(this, arguments);
      };
      
      history.replaceState = function() {
        showSpinner();
        originalReplaceState.apply(this, arguments);
      };
      
      // Also listen for back/forward navigation
      window.addEventListener('popstate', function() {
        showSpinner();
      });
      
      // Monitor AJAX requests (specific to Adminer if it uses them)
      const originalXHROpen = XMLHttpRequest.prototype.open;
      const originalXHRSend = XMLHttpRequest.prototype.send;
      
      XMLHttpRequest.prototype.open = function() {
        this._url = arguments[1];
        return originalXHROpen.apply(this, arguments);
      };
      
      XMLHttpRequest.prototype.send = function() {
        showSpinner(); // Show spinner when request starts
        
        // Set up listeners to hide spinner when done
        this.addEventListener('load', function() {
          setTimeout(hideSpinner, 300); // Small delay to allow rendering
        });
        
        this.addEventListener('error', function() {
          hideSpinner();
        });
        
        return originalXHRSend.apply(this, arguments);
      };
      
    })();
  `;

  // Primary setup - inject our persistent monitoring script on page load
  window.webContents.on('did-finish-load', () => {
    window.webContents.executeJavaScript(navigationMonitorScript)
      .catch(err => console.error('Failed to inject navigation monitor:', err));
    
    // Hide any spinner from initial load
    hideLoadingSpinner();
  });
  
  // Keep standard events as backup, but client-side monitor should handle most cases
  window.webContents.on('did-start-loading', () => {
    showLoadingSpinner();
  });
  
  window.webContents.on('did-stop-loading', () => {
    hideLoadingSpinner();
  });
}

module.exports = { setupLoadingIndicator }
