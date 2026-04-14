{{-- Page Loader Component - Shows during page transitions --}}
<div id="page-loader" class="page-loader">
    <div class="page-loader-overlay"></div>
    <div class="page-loader-spinner">
        <div class="spinner-container">
            {{-- Modern rotating spinner --}}
            <div class="spinner-ring"></div>
            <div class="spinner-ring"></div>
            <div class="spinner-ring"></div>
        </div>
        <p class="loading-text">{{ __('Loading') }}<span class="loading-dots"></span></p>
    </div>
</div>

<style>
    /* Page Loader Overlay */
    .page-loader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        pointer-events: none;
        will-change: opacity;
    }

    .page-loader.active {
        display: flex;
        pointer-events: all;
    }

    /* Semi-transparent backdrop */
    .page-loader-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.9);
        opacity: 0;
        transition: opacity 0.15s ease-out;
        will-change: opacity;
    }

    /* Dark mode overlay */
    .dark .page-loader-overlay {
        background: rgba(17, 24, 39, 0.9);
    }

    .page-loader.active .page-loader-overlay {
        opacity: 1;
    }

    /* Spinner Container */
    .page-loader-spinner {
        position: relative;
        z-index: 10;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.75rem;
        opacity: 0;
        transform: scale(0.9);
        transition: all 0.15s ease-out;
        will-change: transform, opacity;
    }

    .page-loader.active .page-loader-spinner {
        opacity: 1;
        transform: scale(1);
    }

    /* Modern Ring Spinner */
    .spinner-container {
        position: relative;
        width: 48px;
        height: 48px;
    }

    .spinner-ring {
        position: absolute;
        width: 100%;
        height: 100%;
        border: 3px solid transparent;
        border-top-color: #3b82f6;
        border-radius: 50%;
        animation: spin 0.8s cubic-bezier(0.5, 0, 0.5, 1) infinite;
    }

    .spinner-ring:nth-child(1) {
        animation-delay: -0.3s;
        border-top-color: #3b82f6;
    }

    .spinner-ring:nth-child(2) {
        animation-delay: -0.2s;
        border-top-color: #8b5cf6;
        opacity: 0.7;
    }

    .spinner-ring:nth-child(3) {
        animation-delay: -0.1s;
        border-top-color: #10b981;
        opacity: 0.5;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

    /* Loading Text */
    .loading-text {
        font-size: 0.875rem;
        font-weight: 500;
        color: #6b7280;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .dark .loading-text {
        color: #9ca3af;
    }

    /* Animated dots */
    .loading-dots::after {
        content: '';
        animation: dots 1.5s steps(4, end) infinite;
    }

    @keyframes dots {
        0%, 20% {
            content: '';
        }
        40% {
            content: '.';
        }
        60% {
            content: '..';
        }
        80%, 100% {
            content: '...';
        }
    }

    /* Smooth fade animations */
    .page-loader.fade-in {
        animation: fadeIn 0.15s ease-out;
    }

    .page-loader.fade-out {
        animation: fadeOut 0.15s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    @keyframes fadeOut {
        from {
            opacity: 1;
        }
        to {
            opacity: 0;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loader = document.getElementById('page-loader');
        let isLoading = false;
        let loadTimeout = null;
        let showTimeout = null;

        // Show loader function (optimized with delay)
        function showLoader() {
            if (isLoading) return;
            
            isLoading = true;
            
            // Delay showing loader for 100ms - don't show for very fast page loads
            showTimeout = setTimeout(() => {
                if (isLoading) {
                    loader.classList.add('active', 'fade-in');
                }
            }, 100);
            
            // Safety timeout: hide loader after 5 seconds
            clearTimeout(loadTimeout);
            loadTimeout = setTimeout(() => {
                hideLoader();
            }, 5000);
        }

        // Hide loader function (optimized)
        function hideLoader() {
            clearTimeout(showTimeout);
            isLoading = false;
            
            if (loader.classList.contains('active')) {
                loader.classList.remove('active');
                loader.classList.add('fade-out');
                
                setTimeout(() => {
                    loader.classList.remove('fade-in', 'fade-out');
                }, 150);
            }
        }

        // Intercept all link clicks (regular navigation) - optimized
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            
            if (link && 
                link.href && 
                !link.target && 
                link.href.startsWith(window.location.origin) &&
                !link.href.endsWith('#') && // Exclude hash links (dropdowns, modals)
                link.getAttribute('href') !== '#' && // Exclude direct hash links
                !link.hasAttribute('data-no-loader') &&
                !link.classList.contains('no-loader') &&
                !link.hasAttribute('download') &&
                !link.hasAttribute('@click.prevent') && // Exclude Alpine.js prevent
                !link.closest('[x-data]')) { // Exclude links inside Alpine components
                
                showLoader();
            }
        }, { passive: true });

        // Handle form submissions - optimized
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form && !form.hasAttribute('data-no-loader')) {
                showLoader();
            }
        }, { passive: true });

        // Hide loader when page is fully loaded
        window.addEventListener('load', function() {
            hideLoader();
        });

        // Handle browser back/forward buttons
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                hideLoader();
            }
        });

        // Handle page visibility change (user switches tabs)
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                hideLoader();
            }
        });

        // Livewire integration (if using Livewire)
        if (typeof Livewire !== 'undefined') {
            // Show loader on Livewire navigation
            Livewire.hook('message.sent', () => {
                showLoader();
            });

            // Hide loader when Livewire response received
            Livewire.hook('message.processed', () => {
                hideLoader();
            });

            // Hide on error
            Livewire.hook('message.failed', () => {
                hideLoader();
            });
        }
    });
</script>
