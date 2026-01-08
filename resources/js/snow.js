/**
 * Snow Animation for MyExamly Dashboard
 * Safe, performant snow effect with user controls
 */

class SnowAnimation {
    constructor() {
        this.isActive = false;
        this.snowflakes = [];
        this.container = null;
        this.toggleButton = null;
        this.maxSnowflakes = 50;
        this.animationId = null;

        // Check if user prefers reduced motion
        this.respectsMotion = !window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        // Check if it's winter season (December, January, February)
        this.isWinterSeason = this.checkWinterSeason();

        // Load user preference
        this.userPreference = localStorage.getItem('snow-animation-enabled') === 'true';

        this.init();
    }

    init() {
        // Only initialize if motion is allowed and it's winter season
        if (!this.respectsMotion || !this.isWinterSeason) {
            return;
        }

        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setup());
        } else {
            this.setup();
        }
    }

    setup() {
        this.createContainer();
        this.createToggleButton();
        this.setupEventListeners();

        // Set month data attribute for seasonal CSS
        document.body.setAttribute('data-month', new Date().getMonth() + 1);

        // Auto-start if user had it enabled previously
        if (this.userPreference) {
            this.start();
        }
    }

    createContainer() {
        this.container = document.createElement('div');
        this.container.className = 'snow-container snow-seasonal';
        this.container.id = 'snow-container';
        document.body.appendChild(this.container);
    }

    createToggleButton() {
        this.toggleButton = document.createElement('button');
        this.toggleButton.className = 'snow-toggle snow-seasonal';
        this.toggleButton.innerHTML = '❄️';
        this.toggleButton.title = 'Toggle Snow Animation';
        this.toggleButton.setAttribute('aria-label', 'Toggle snow animation');
        document.body.appendChild(this.toggleButton);
    }

    setupEventListeners() {
        // Toggle button click
        this.toggleButton.addEventListener('click', () => {
            this.toggle();
        });

        // Pause animation when tab is not visible
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.pause();
            } else if (this.isActive) {
                this.resume();
            }
        });

        // Pause on window blur
        window.addEventListener('blur', () => this.pause());
        window.addEventListener('focus', () => {
            if (this.isActive) this.resume();
        });

        // Clean up on page unload
        window.addEventListener('beforeunload', () => this.cleanup());
    }

    toggle() {
        if (this.isActive) {
            this.stop();
        } else {
            this.start();
        }
    }

    start() {
        if (!this.respectsMotion || !this.isWinterSeason) return;

        this.isActive = true;
        if (this.toggleButton) {
            this.toggleButton.classList.add('active');
        }
        if (this.container) {
            this.container.style.display = 'block';
        }

        // Save user preference
        localStorage.setItem('snow-animation-enabled', 'true');

        this.createSnowflakes();
        this.animate();
    }

    stop() {
        this.isActive = false;
        this.toggleButton.classList.remove('active');
        this.container.style.display = 'none';

        // Save user preference
        localStorage.setItem('snow-animation-enabled', 'false');

        this.cleanup();
    }

    pause() {
        if (this.animationId) {
            cancelAnimationFrame(this.animationId);
            this.animationId = null;
        }
    }

    resume() {
        if (this.isActive && !this.animationId) {
            this.animate();
        }
    }

    createSnowflakes() {
        const snowflakeSymbols = ['❄', '❅', '❆', '*', '✦'];
        const sizes = ['small', 'medium', 'large'];
        const swayTypes = ['sway1', 'sway2', 'sway3'];

        for (let i = 0; i < this.maxSnowflakes; i++) {
            const snowflake = document.createElement('div');
            snowflake.className = 'snowflake';

            // Random properties
            const symbol = snowflakeSymbols[Math.floor(Math.random() * snowflakeSymbols.length)];
            const size = sizes[Math.floor(Math.random() * sizes.length)];
            const sway = swayTypes[Math.floor(Math.random() * swayTypes.length)];

            snowflake.textContent = symbol;
            snowflake.classList.add(size, sway);

            // Random position and delay
            snowflake.style.left = Math.random() * 100 + '%';
            snowflake.style.animationDelay = Math.random() * 10 + 's';
            snowflake.style.animationDuration = (Math.random() * 8 + 6) + 's';

            // Random opacity
            snowflake.style.opacity = Math.random() * 0.8 + 0.2;

            this.container.appendChild(snowflake);
            this.snowflakes.push(snowflake);
        }
    }

    animate() {
        if (!this.isActive) return;

        // Simple animation loop for any additional effects
        this.animationId = requestAnimationFrame(() => this.animate());
    }

    cleanup() {
        if (this.animationId) {
            cancelAnimationFrame(this.animationId);
            this.animationId = null;
        }

        // Remove all snowflakes
        this.snowflakes.forEach(snowflake => {
            if (snowflake.parentNode) {
                snowflake.parentNode.removeChild(snowflake);
            }
        });
        this.snowflakes = [];
    }

    checkWinterSeason() {
        const month = new Date().getMonth() + 1; // 1-12
        return month === 12 || month === 1 || month === 2;
    }

    // Public methods for external control
    destroy() {
        this.cleanup();

        if (this.container && this.container.parentNode) {
            this.container.parentNode.removeChild(this.container);
        }

        if (this.toggleButton && this.toggleButton.parentNode) {
            this.toggleButton.parentNode.removeChild(this.toggleButton);
        }
    }
}

// Initialize snow animation when the script loads
let snowAnimation;

console.log('Snow script loading...');

// Wait for document ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSnow);
} else {
    initSnow();
}

function initSnow() {
    console.log('Initializing snow animation...');

    // Check if already initialized
    if (snowAnimation) {
        console.log('Snow animation already initialized');
        return;
    }

    // Only initialize on desktop devices (temporarily disabled for testing)
    // const isMobile = window.innerWidth <= 768 || /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

    // if (!isMobile) {
        snowAnimation = new SnowAnimation();
        console.log('Snow animation instance created');
    // } else {
    //     console.log('Snow animation disabled on mobile device');
    // }
}

// Clean up on page unload
window.addEventListener('beforeunload', () => {
    if (snowAnimation) {
        snowAnimation.destroy();
    }
});

// Export for potential external use
window.SnowAnimation = SnowAnimation;

// Manual test function
window.testSnow = function() {
    console.log('Manual snow test triggered');
    if (!snowAnimation) {
        snowAnimation = new SnowAnimation();
    }
    snowAnimation.toggle();
};
