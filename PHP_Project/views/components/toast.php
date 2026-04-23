<?php

?>

<div id="toast-container" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">
    <!-- Toast messages will be dynamically inserted here -->
</div>

<script>

function showToast(message, type = 'info', duration = 4000) {
    const container = document.getElementById('toast-container');
    
    const typeConfig = {
        'success': { bg: '#28a745', icon: 'fa-check-circle' },
        'error': { bg: '#dc3545', icon: 'fa-exclamation-circle' },
        'warning': { bg: '#ffc107', icon: 'fa-exclamation-triangle', color: '#000' },
        'info': { bg: '#17a2b8', icon: 'fa-info-circle' }
    };
    
    const config = typeConfig[type] || typeConfig['info'];
    
    const toastId = 'toast-' + Date.now();
    const toastHTML = `
        <div id="${toastId}" class="toast align-items-center border-0" 
             style="background-color: ${config.bg}; color: ${config.color || 'white'}; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border-radius: 8px; min-width: 300px;" 
             role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center">
                    <i class="fas ${config.icon}" style="margin-right: 12px; font-size: 1.2rem;"></i>
                    <span>${message}</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"
                        style="filter: brightness(1.2);"></button>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', toastHTML);
    
    const toastElement = document.getElementById(toastId);
    const bsToast = new bootstrap.Toast(toastElement, { delay: duration });
    bsToast.show();
    
    toastElement.addEventListener('hidden.bs.toast', function() {
        toastElement.remove();
    });
}


const Toast = {
    success: (msg, duration = 3000) => showToast(msg, 'success', duration),
    error: (msg, duration = 4000) => showToast(msg, 'error', duration),
    warning: (msg, duration = 3500) => showToast(msg, 'warning', duration),
    info: (msg, duration = 3000) => showToast(msg, 'info', duration)
};

document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    
    if (urlParams.has('success')) {
        Toast.success(urlParams.get('success'));
    }
    
    if (urlParams.has('error')) {
        Toast.error(urlParams.get('error'));
    }
    
    if (urlParams.has('warning')) {
        Toast.warning(urlParams.get('warning'));
    }
    
    if (urlParams.has('info')) {
        Toast.info(urlParams.get('info'));
    }
});
</script>

<style>
        gap: 12px;
        display: flex;
        flex-direction: column;
    }
    
    .toast {
        animation: slideIn 0.3s ease-out;
    }
    
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    
    @media (max-width: 576px) {
            left: 12px !important;
            right: 12px !important;
            bottom: 12px !important;
        }
        
        .toast {
            min-width: auto !important;
            width: 100%;
        }
    }
</style>
