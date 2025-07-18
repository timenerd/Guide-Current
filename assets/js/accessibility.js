// Enhanced Accessibility System for GuideAI
// Designed specifically for families with disabled children

class GuideAIAccessibility {
    constructor() {
        // Initialize preferences with defaults first
        this.preferences = {
            highContrast: false,
            largeFonts: false,
            dyslexiaFont: false,
            readAloud: false,
            ttsVoice: 'nova',
            ttsSpeed: 1.0
        };
        
        this.isDrawerOpen = false;
        
        // Detect current language from page
        this.currentLanguage = document.documentElement.lang || 
                              document.querySelector('html').getAttribute('lang') || 
                              'en';
        
        // Load saved preferences after initialization
        this.loadPreferences();
        
        this.init();
    }

    init() {
        console.log('🚀 Initializing GuideAI Accessibility System...');
        
        // Don't create button/drawer - they're now static HTML
        // Just enhance the existing elements
        this.enhanceExistingElements();
        this.setupKeyboardNavigation();
        this.setupScreenReaderSupport();
        this.setupFocusManagement();
        this.setupEmergencyModal();
        this.applyPreferences();
        
        // Sync with GuideAI if available
        this.syncWithGuideAI();
        
        // Verify button exists and is visible
        setTimeout(() => {
            this.verifyButtonVisibility();
        }, 100);
        
        // Add resize listener for responsive positioning
        window.addEventListener('resize', () => {
            this.updateButtonPosition();
        });
        
        console.log('Enhanced Accessibility System initialized');
    }

    // Load saved accessibility preferences
    loadPreferences() {
        try {
            const saved = localStorage.getItem('guideai_accessibility_preferences');
            if (saved) {
                const savedPrefs = JSON.parse(saved);
                this.preferences = { ...this.preferences, ...savedPrefs };
            }
            
            // Set default TTS preferences if not set
            if (!this.preferences.ttsVoice) {
                this.preferences.ttsVoice = 'nova';
            }
            if (!this.preferences.ttsSpeed) {
                this.preferences.ttsSpeed = 1.0;
            }
            
            // Force Nova as default voice for new users
            if (!saved) {
                this.preferences.ttsVoice = 'nova';
                this.savePreferences();
            }
            
            console.log('✅ Preferences loaded:', this.preferences);
        } catch (error) {
            console.error('Error loading accessibility preferences:', error);
            // Use defaults if loading fails
            this.preferences.ttsVoice = 'nova';
            this.preferences.ttsSpeed = 1.0;
        }
    }

    // Save accessibility preferences
    savePreferences() {
        try {
            localStorage.setItem('guideai_accessibility_preferences', JSON.stringify(this.preferences));
            console.log('✅ Preferences saved:', this.preferences);
        } catch (e) {
            console.warn('Could not save accessibility preferences:', e);
        }
    }

    // Reset preferences to defaults (useful for testing)
    resetPreferences() {
        this.preferences = {
            highContrast: false,
            largeFonts: false,
            dyslexiaFont: false,
            readAloud: false,
            ttsVoice: 'nova',
            ttsSpeed: 1.0
        };
        this.savePreferences();
        console.log('✅ Preferences reset to defaults with Nova voice');
    }

    // Enhance existing static HTML elements
    enhanceExistingElements() {
        const button = document.getElementById('accessibilityToggle');
        const drawer = document.getElementById('accessibilityDrawer');
        
        if (button) {
            // Update button with proper translations and enhanced functionality
            button.title = this.t('accessibility_options');
            button.setAttribute('aria-label', this.t('open_accessibility_options'));
            
            // Add enhanced event listeners for mobile compatibility
        button.addEventListener('click', (e) => {
            e.preventDefault();
                e.stopPropagation();
                console.log('Enhanced click event triggered');
            this.toggleAccessibilityDrawer();
        });
            
            button.addEventListener('touchstart', (e) => {
                e.preventDefault();
                e.stopPropagation();
                console.log('Enhanced touchstart event triggered');
            }, { passive: false });
            
            button.addEventListener('touchend', (e) => {
                e.preventDefault();
                e.stopPropagation();
                console.log('Enhanced touchend event triggered');
                this.toggleAccessibilityDrawer();
            }, { passive: false });
            
            // Add keyboard support
            button.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.toggleAccessibilityDrawer();
                }
            });
        }
        
        if (drawer) {
            // Update drawer content with translations
            this.updateDrawerContent();
            
            // Set up drawer event listeners
            this.setupDrawerEventListeners();
        }
        
        // Add styles if not present
        this.addAccessibilityStyles();
    }

    // Update drawer content with translations
    updateDrawerContent() {
        const drawer = document.getElementById('accessibilityDrawer');
        if (!drawer) {
            console.error('❌ Drawer not found for content update');
            return;
        }
        
        console.log('🔄 Updating drawer content with language:', this.currentLanguage);
        
        // Replace entire drawer content with full version including voice selection
        drawer.innerHTML = this.createDrawerContent();

        // Setup event listeners for the new content
        this.setupDrawerEventListeners();
        
        console.log('✅ Drawer content update completed with full voice options');
    }

    // Setup accessibility drawer - REMOVED - now using static HTML
    // setupAccessibilityDrawer() { ... }

    createDrawerContent() {
        console.log('🎤 Creating drawer content with voice preference:', this.preferences.ttsVoice);
        
        const languageButtons = this.currentLanguage === 'es' 
            ? `
                <button type="button" class="btn btn-outline-primary btn-sm" data-lang="en">
                    🇺🇸 English
                </button>
                <button type="button" class="btn btn-outline-primary btn-sm active" data-lang="es">
                    🇪🇸 Español
                </button>
            `
            : `
                <button type="button" class="btn btn-outline-primary btn-sm active" data-lang="en">
                    🇺🇸 English
                </button>
                <button type="button" class="btn btn-outline-primary btn-sm" data-lang="es">
                    🇪🇸 Español
                </button>
            `;

        return `
            <div class="drawer-content">
                <div class="drawer-header">
                    <h2 id="accessibilityTitle" class="h6 mb-0">
                        <span>${this.t('accessibility_options')}</span>
                    </h2>
                    <button class="btn-close btn-close-white" id="closeAccessibilityDrawer" aria-label="${this.t('close_accessibility_options')}">×</button>
                </div>
                
                <div class="drawer-body">
                    <!-- Language Selection -->
                    <div class="accessibility-section">
                        <h3 class="h6 text-primary mb-2">
                            <i class="fas fa-language me-2" aria-hidden="true"></i>
                            <span>${this.t('language_section_title')}</span>
                        </h3>
                        <div class="btn-group w-100" role="group" aria-label="${this.t('language_selection')}">
                            ${languageButtons}
                        </div>
                    </div>

                    <!-- Visual Accessibility -->
                    <div class="accessibility-section">
                        <h3 class="h6 text-primary mb-2">
                            <i class="fas fa-eye me-2" aria-hidden="true"></i>
                            <span>${this.t('visual_section')}</span>
                        </h3>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm ${this.preferences.highContrast ? 'active' : ''}" 
                                    id="toggleHighContrast" 
                                    title="${this.t('toggle_high_contrast')}" 
                                    aria-label="${this.t('toggle_high_contrast')}"
                                    aria-pressed="${this.preferences.highContrast}">
                                <i class="fas fa-adjust me-2" aria-hidden="true"></i>
                                <span>${this.t('high_contrast')}</span>
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm ${this.preferences.largeFonts ? 'active' : ''}" 
                                    id="toggleLargeFonts" 
                                    title="${this.t('toggle_large_fonts')}" 
                                    aria-label="${this.t('toggle_large_fonts')}"
                                    aria-pressed="${this.preferences.largeFonts}">
                                <i class="fas fa-font me-2" aria-hidden="true"></i>
                                <span>${this.t('large_text')}</span>
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm ${this.preferences.dyslexiaFont ? 'active' : ''}" 
                                    id="toggleDyslexiaFont" 
                                    title="${this.t('toggle_dyslexia_font')}" 
                                    aria-label="${this.t('toggle_dyslexia_font')}"
                                    aria-pressed="${this.preferences.dyslexiaFont}">
                                <i class="fas fa-spell-check me-2" aria-hidden="true"></i>
                                <span>${this.t('dyslexia_font')}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Audio Accessibility -->
                    <div class="accessibility-section">
                        <h3 class="h6 text-primary mb-2">
                            <i class="fas fa-volume-up me-2" aria-hidden="true"></i>
                            <span>${this.t('audio_section')}</span>
                        </h3>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="drawerReadAloudToggle" 
                                   ${this.preferences.readAloud ? 'checked' : ''}>
                            <label class="form-check-label" for="drawerReadAloudToggle">
                                ${this.t('read_aloud')}
                            </label>
                        </div>
                        
                        <div class="mb-3">
                            <label for="ttsVoiceSelect" class="form-label small">${this.t('voice_selection')}:</label>
                            <select class="form-select form-select-sm" id="ttsVoiceSelect">
                                <option value="alloy" ${this.preferences.ttsVoice === 'alloy' ? 'selected' : ''}>${this.t('voice_alloy')}</option>
                                <option value="echo" ${this.preferences.ttsVoice === 'echo' ? 'selected' : ''}>${this.t('voice_echo')}</option>
                                <option value="fable" ${this.preferences.ttsVoice === 'fable' ? 'selected' : ''}>${this.t('voice_fable')}</option>
                                <option value="onyx" ${this.preferences.ttsVoice === 'onyx' ? 'selected' : ''}>${this.t('voice_onyx')}</option>
                                <option value="nova" ${this.preferences.ttsVoice === 'nova' ? 'selected' : ''}>${this.t('voice_nova')}</option>
                                <option value="shimmer" ${this.preferences.ttsVoice === 'shimmer' ? 'selected' : ''}>${this.t('voice_shimmer')}</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="ttsSpeedRange" class="form-label small">${this.t('speed_control')}: <span id="speedValue">${this.preferences.ttsSpeed || 1.0}</span>x</label>
                            <input type="range" class="form-range" id="ttsSpeedRange" 
                                   min="0.25" max="4.0" step="0.25" 
                                   value="${this.preferences.ttsSpeed || 1.0}">
                        </div>
                        
                        <button class="btn btn-outline-primary btn-sm" onclick="testTTS()">
                            <i class="fas fa-play me-2"></i>${this.t('test_voice')}
                        </button>
                    </div>

                    <!-- Emergency Help -->
                    <div class="accessibility-section">
                        <button class="btn btn-danger w-100" id="drawerEmergencyBtn" 
                                aria-label="${this.t('emergency_contacts_aria')}">
                            <i class="fas fa-phone-alt me-2" aria-hidden="true"></i>
                            <span>${this.t('emergency_help')}</span>
                        </button>
                    </div>

                    <!-- Keyboard Shortcuts Info -->
                    <div class="accessibility-section">
                        <h3 class="h6 text-primary mb-2">
                            <i class="fas fa-keyboard me-2" aria-hidden="true"></i>
                            <span>${this.t('keyboard_shortcuts')}</span>
                        </h3>
                        <div class="small">
                            <div class="mb-2"><kbd>Alt + 1</kbd> ${this.t('focus_input')}</div>
                            <div class="mb-2"><kbd>Alt + 2</kbd> ${this.t('focus_prompts')}</div>
                            <div class="mb-2"><kbd>Alt + E</kbd> ${this.t('emergency_contacts')}</div>
                            <div class="mb-2"><kbd>Alt + A</kbd> ${this.t('accessibility_options')}</div>
                            <div class="mb-2"><kbd>Alt + H</kbd> ${this.t('toggle_high_contrast')}</div>
                            <div class="mb-2"><kbd>Alt + F</kbd> ${this.t('toggle_large_fonts')}</div>
                            <div class="mb-2"><kbd>Alt + D</kbd> ${this.t('toggle_dyslexia_font')}</div>
                            <div class="mb-2"><kbd>Alt + R</kbd> ${this.t('toggle_read_aloud')}</div>
                            <div class="mb-2"><kbd>Escape</kbd> ${this.t('close_or_stop')}</div>
                            <div class="mb-2"><kbd>Ctrl + Enter</kbd> ${this.t('submit_form')}</div>
                        </div>
                    </div>

                    <!-- Language Options -->
                    <div class="accessibility-section">
                        <h3 class="h6 text-primary mb-2">
                            <i class="fas fa-language me-2" aria-hidden="true"></i>
                            <span>${this.t('language_options')}</span>
                        </h3>
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary btn-sm" onclick="window.guideAIAccessibility.changeLanguage('en')">
                                <i class="fas fa-flag me-2"></i>English
                            </button>
                            <button class="btn btn-outline-primary btn-sm" onclick="window.guideAIAccessibility.changeLanguage('es')">
                                <i class="fas fa-flag me-2"></i>Español
                            </button>
                        </div>
                    </div>

                    <!-- Testing Tools -->
                    <div class="accessibility-section">
                        <h3 class="h6 text-primary mb-2">
                            <i class="fas fa-tools me-2" aria-hidden="true"></i>
                            <span>${this.t('testing_tools')}</span>
                        </h3>
                        <div class="d-grid gap-2">
                            <button class="btn btn-warning btn-sm" onclick="window.guideAIAccessibility.testAccessibilityFeatures()">
                                <i class="fas fa-vial me-2"></i>${this.t('test_accessibility')}
                            </button>
                            <button class="btn btn-info btn-sm" onclick="window.guideAIAccessibility.announceToScreenReader('Testing screen reader announcement')">
                                <i class="fas fa-volume-up me-2"></i>${this.t('test_screen_reader')}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    setupDrawerEventListeners() {
        const drawer = document.getElementById('accessibilityDrawer');
        if (!drawer) {
            console.error('❌ Accessibility drawer not found');
            return;
        }

        console.log('🔧 Setting up drawer event listeners...');

        // Close button
        const closeBtn = document.getElementById('closeAccessibilityDrawer');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.closeAccessibilityDrawer());
            console.log('✅ Close button listener attached');
        } else {
            console.error('❌ Close button not found');
        }

        // Language buttons
        const langButtons = drawer.querySelectorAll('[data-lang]');
        langButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const lang = e.target.getAttribute('data-lang');
                this.changeLanguage(lang);
            });
        });
        console.log('✅ Language buttons listeners attached:', langButtons.length);

        // Accessibility toggles
        const toggleButtons = {
            'toggleHighContrast': () => this.toggleHighContrast(),
            'toggleLargeFonts': () => this.toggleLargeFonts(),
            'toggleDyslexiaFont': () => this.toggleDyslexiaFont()
        };

        Object.entries(toggleButtons).forEach(([id, handler]) => {
            const btn = document.getElementById(id);
            if (btn) {
                btn.addEventListener('click', handler);
                console.log('✅ Event listener attached to:', id);
            } else {
                console.error('❌ Button not found:', id);
            }
        });

        // Read aloud toggle
        const readAloudToggle = document.getElementById('drawerReadAloudToggle');
        if (readAloudToggle) {
            readAloudToggle.addEventListener('change', (e) => {
                this.toggleReadAloud(e.target.checked);
            });
        }

        // TTS Voice selection
        const ttsVoiceSelect = document.getElementById('ttsVoiceSelect');
        if (ttsVoiceSelect) {
            console.log('🎤 Voice select found, current value:', ttsVoiceSelect.value);
            ttsVoiceSelect.addEventListener('change', (e) => {
                console.log('🎤 Voice selection changed to:', e.target.value);
                this.preferences.ttsVoice = e.target.value;
                this.savePreferences();
                this.announceToScreenReader(`Voice changed to ${e.target.value}`);
            });
        } else {
            console.error('❌ Voice select element not found');
        }

        // TTS Speed control
        const ttsSpeedRange = document.getElementById('ttsSpeedRange');
        const speedValue = document.getElementById('speedValue');
        if (ttsSpeedRange && speedValue) {
            ttsSpeedRange.addEventListener('input', (e) => {
                const speed = parseFloat(e.target.value);
                speedValue.textContent = speed.toFixed(2);
                this.preferences.ttsSpeed = speed;
                this.savePreferences();
            });
            
            ttsSpeedRange.addEventListener('change', (e) => {
                const speed = parseFloat(e.target.value);
                this.announceToScreenReader(`Speed set to ${speed.toFixed(2)}x`);
            });
        }

        // Emergency button
        const emergencyBtn = document.getElementById('drawerEmergencyBtn');
        if (emergencyBtn) {
            emergencyBtn.addEventListener('click', () => {
                this.showEmergencyModal();
                this.closeAccessibilityDrawer();
            });
        }

        // Click outside to close
        document.addEventListener('click', (e) => {
            const accessibilityBtn = document.getElementById('accessibilityToggle');
            if (this.isDrawerOpen && 
                !drawer.contains(e.target) && 
                !accessibilityBtn.contains(e.target)) {
                this.closeAccessibilityDrawer();
            }
        });
    }

    // Toggle functions
    toggleAccessibilityDrawer() {
        console.log('🔧 Toggle accessibility drawer called from accessibility.js');
        if (this.isDrawerOpen) {
            this.closeAccessibilityDrawer();
        } else {
            this.openAccessibilityDrawer();
        }
    }

    openAccessibilityDrawer() {
        console.log('🔧 Opening accessibility drawer from accessibility.js');
        const drawer = document.getElementById('accessibilityDrawer');
        const button = document.getElementById('accessibilityToggle');
        
        if (drawer) {
            drawer.classList.add('open');
            drawer.setAttribute('aria-hidden', 'false');
            this.isDrawerOpen = true;
            console.log('✅ Drawer opened successfully');
        } else {
            console.error('❌ Drawer element not found');
        }
        
        if (button) {
            button.classList.add('active');
            button.setAttribute('aria-expanded', 'true');
            console.log('✅ Button state updated');
        } else {
            console.error('❌ Button element not found');
        }
        
        this.announceToScreenReader('Accessibility options opened');
    }

    closeAccessibilityDrawer() {
        console.log('🔧 Closing accessibility drawer from accessibility.js');
        const drawer = document.getElementById('accessibilityDrawer');
        const button = document.getElementById('accessibilityToggle');
        
        if (drawer) {
            drawer.classList.remove('open');
            drawer.setAttribute('aria-hidden', 'true');
            this.isDrawerOpen = false;
            console.log('✅ Drawer closed successfully');
        } else {
            console.error('❌ Drawer element not found');
        }
        
        if (button) {
            button.classList.remove('active');
            button.setAttribute('aria-expanded', 'false');
            console.log('✅ Button state updated');
        } else {
            console.error('❌ Button element not found');
        }
        
        this.announceToScreenReader('Accessibility options closed');
    }

    // Accessibility feature toggles
    toggleHighContrast() {
        console.log('🔧 toggleHighContrast called, current state:', this.preferences.highContrast);
        
        this.preferences.highContrast = !this.preferences.highContrast;
        document.body.classList.toggle('high-contrast', this.preferences.highContrast);
        
        console.log('🔧 High contrast toggled to:', this.preferences.highContrast);
        console.log('🔧 Body classes after toggle:', document.body.className);
        
        const btn = document.getElementById('toggleHighContrast');
        if (btn) {
            btn.classList.toggle('active', this.preferences.highContrast);
            btn.setAttribute('aria-pressed', this.preferences.highContrast.toString());
            console.log('✅ High contrast button updated');
        } else {
            console.error('❌ High contrast button not found');
        }
        
        this.savePreferences();
        this.announceToScreenReader(
            `High contrast mode ${this.preferences.highContrast ? 'enabled' : 'disabled'}`
        );
    }

    toggleLargeFonts() {
        console.log('🔧 toggleLargeFonts called, current state:', this.preferences.largeFonts);
        
        this.preferences.largeFonts = !this.preferences.largeFonts;
        document.body.classList.toggle('large-fonts', this.preferences.largeFonts);
        
        console.log('🔧 Large fonts toggled to:', this.preferences.largeFonts);
        console.log('🔧 Body classes after toggle:', document.body.className);
        
        const btn = document.getElementById('toggleLargeFonts');
        if (btn) {
            btn.classList.toggle('active', this.preferences.largeFonts);
            btn.setAttribute('aria-pressed', this.preferences.largeFonts.toString());
            console.log('✅ Large fonts button updated');
        } else {
            console.error('❌ Large fonts button not found');
        }
        
        this.savePreferences();
        this.announceToScreenReader(
            `Large fonts ${this.preferences.largeFonts ? 'enabled' : 'disabled'}`
        );
    }

    toggleDyslexiaFont() {
        console.log('🔧 toggleDyslexiaFont called, current state:', this.preferences.dyslexiaFont);
        
        this.preferences.dyslexiaFont = !this.preferences.dyslexiaFont;
        document.body.classList.toggle('dyslexia-friendly', this.preferences.dyslexiaFont);
        
        console.log('🔧 Dyslexia font toggled to:', this.preferences.dyslexiaFont);
        console.log('🔧 Body classes after toggle:', document.body.className);
        
        const btn = document.getElementById('toggleDyslexiaFont');
        if (btn) {
            btn.classList.toggle('active', this.preferences.dyslexiaFont);
            btn.setAttribute('aria-pressed', this.preferences.dyslexiaFont.toString());
            console.log('✅ Dyslexia font button updated');
        } else {
            console.error('❌ Dyslexia font button not found');
        }
        
        this.savePreferences();
        this.announceToScreenReader(
            `Dyslexia-friendly font ${this.preferences.dyslexiaFont ? 'enabled' : 'disabled'}`
        );
    }

    // Enhanced read aloud functionality with OpenAI TTS
    toggleReadAloud(enabled) {
        console.log('🔧 toggleReadAloud called with:', enabled);
        
        this.preferences.readAloud = enabled;
        
        // Sync with GuideAI instance if available
        if (window.guideAI && typeof window.guideAI.savePreferences === 'function') {
            try {
                window.guideAI.readAloudEnabled = enabled;
                window.guideAI.updateAccessibilityPreferences({
                    readAloud: enabled,
                    ttsVoice: this.preferences.ttsVoice,
                    ttsSpeed: this.preferences.ttsSpeed
                });
                console.log('✅ GuideAI preferences synced');
            } catch (error) {
                console.warn('⚠️ Could not sync with GuideAI:', error);
            }
        } else {
            console.log('ℹ️ GuideAI not available for preference sync');
        }
        
        this.savePreferences();
        
        if (enabled) {
            this.announceToScreenReader('Read aloud enabled with OpenAI TTS');
        } else {
            // Stop any playing audio
            this.stopCurrentAudio();
            this.announceToScreenReader('Read aloud disabled');
        }
    }

    // Stop current audio playback
    stopCurrentAudio() {
        if (this.currentAudio) {
            this.currentAudio.pause();
            this.currentAudio.currentTime = 0;
            this.currentAudio = null;
        }
        
        // Also stop browser speech synthesis if it's running
            if ('speechSynthesis' in window) {
                speechSynthesis.cancel();
            }
    }

    // Speak text using OpenAI TTS with fallback to browser speech synthesis
    async speakText(text, options = {}) {
        console.log('🔊 speakText called with readAloud:', this.preferences.readAloud);
        
        if (!this.preferences.readAloud) {
            console.log('🔇 Read aloud disabled, skipping TTS');
            return;
        }

        // Stop any current audio
        this.stopCurrentAudio();

        const {
            voice = this.preferences.ttsVoice || 'nova',
            speed = this.preferences.ttsSpeed || 1.0,
            language = document.documentElement.lang || 'en',
            priority = 'normal'
        } = options;

        // Debug logging
        console.log('🔊 speakText called with:', {
            text: text.substring(0, 100) + '...',
            voice: voice,
            speed: speed,
            preferences: this.preferences,
            readAloud: this.preferences.readAloud,
            currentVoice: this.preferences.ttsVoice
        });

        // Clean text
        const cleanText = this.stripHtml(text);
        if (!cleanText.trim()) {
            console.log('🔇 No text to speak after cleaning');
            return;
        }

        try {
            console.log('🎤 Attempting OpenAI TTS with voice:', voice);
            // Try OpenAI TTS first
            await this.speakWithOpenAI(cleanText, { voice, speed, language });
        } catch (error) {
            console.warn('❌ OpenAI TTS failed, falling back to browser speech synthesis:', error);
            
            try {
                // Fallback to browser speech synthesis
                this.speakWithBrowser(cleanText, { language, speed });
            } catch (fallbackError) {
                console.error('❌ Both TTS methods failed:', fallbackError);
                this.announceToScreenReader('Text-to-speech is currently unavailable');
            }
        }
    }

    // Speak using OpenAI TTS API
    async speakWithOpenAI(text, options = {}) {
        const { voice = 'nova', speed = 1.0, language = 'en' } = options;

        console.log('🎤 OpenAI TTS called with:', {
            text: text.substring(0, 100) + '...',
            voice: voice,
            speed: speed,
            language: language,
            preferences: this.preferences,
            apiEndpoint: 'api/tts.php'
        });

        const response = await fetch('api/tts.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                text: text,
                voice: voice,
                language: language,
                speed: speed
            })
        });

        console.log('📡 TTS API response status:', response.status);

        if (!response.ok) {
            const errorData = await response.json();
            console.error('❌ TTS API error:', errorData);
            throw new Error(errorData.error || `HTTP ${response.status}`);
        }

        const data = await response.json();
        
        console.log('✅ TTS API response received:', {
            success: data.success,
            voice: data.voice,
            format: data.format,
            text_length: data.text_length
        });
        
        if (!data.success || !data.audio) {
            console.error('❌ Invalid TTS response:', data);
            throw new Error('Invalid TTS response');
        }

        console.log('✅ OpenAI TTS response received for voice:', voice);

        // Create audio element and play
        const audio = new Audio(`data:audio/mp3;base64,${data.audio}`);
        audio.volume = 1.0;
        
        // Store reference to stop later
        this.currentAudio = audio;
        
        // Play the audio
        await audio.play();
        
        // Clean up when done
        audio.onended = () => {
            console.log('✅ Audio playback completed');
            this.currentAudio = null;
        };
        
        audio.onerror = (error) => {
            console.error('❌ Audio playback error:', error);
            this.currentAudio = null;
            throw new Error('Audio playback failed');
        };

        this.announceToScreenReader(`Playing audio with ${voice} voice`);
    }

    // Fallback to browser speech synthesis
    speakWithBrowser(text, options = {}) {
        if (!('speechSynthesis' in window)) {
            throw new Error('Speech synthesis not supported');
        }

        const { language = 'en', speed = 1.0 } = options;

        // Cancel any existing speech
        speechSynthesis.cancel();

        const utterance = new SpeechSynthesisUtterance(text);
        
        // Set language
        utterance.lang = language === 'es' ? 'es-ES' : 'en-US';
        
        // Set voice preferences
        const voices = speechSynthesis.getVoices();
        const preferredVoice = voices.find(voice => 
            (language === 'es' && voice.lang.startsWith('es')) ||
            (language === 'en' && voice.lang.startsWith('en'))
        );
        
        if (preferredVoice) {
            utterance.voice = preferredVoice;
        }
        
        // Set speech parameters
        utterance.rate = Math.max(0.5, Math.min(2.0, speed)); // Clamp between 0.5 and 2.0
        utterance.pitch = 1.0;
        utterance.volume = 1.0;
        
        // Speak the text
        speechSynthesis.speak(utterance);
        
        this.announceToScreenReader('Playing audio with browser speech synthesis');
    }

    // Language switching
    changeLanguage(lang) {
        // Show loading indicator
        this.showLanguageLoading();
        
        // Update current language
        this.currentLanguage = lang;
        
        // Update accessibility drawer content with new language
        this.updateDrawerContent();
        
        // Update URL to trigger page reload with new language
        const url = new URL(window.location);
        url.searchParams.set('lang', lang);
        
        // Set cookie for persistence
        document.cookie = `guideai_language=${lang}; max-age=${86400 * 30}; path=/`;
        
        // Reload page
        setTimeout(() => {
            window.location.href = url.toString();
        }, 500);
    }

    // Refresh drawer content with current language
    refreshDrawerContent() {
        const drawer = document.getElementById('accessibilityDrawer');
        if (drawer) {
            drawer.innerHTML = this.createDrawerContent();
            this.setupDrawerEventListeners();
        }
    }

    showLanguageLoading() {
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'language-loading active';
        loadingDiv.innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p>Changing language...</p>
            </div>
        `;
        document.body.appendChild(loadingDiv);
    }

    // Enhanced keyboard navigation
    setupKeyboardNavigation() {
        // Enhanced keyboard navigation for prompts
        const promptItems = document.querySelectorAll('#promptList .list-group-item');
        
        promptItems.forEach((item, index) => {
            // Make items keyboard focusable
            if (!item.hasAttribute('tabindex')) {
                item.setAttribute('tabindex', '0');
            }
            
            item.addEventListener('keydown', (e) => {
                switch(e.key) {
                    case 'Enter':
                    case ' ':
                        e.preventDefault();
                        item.click();
                        this.announceToScreenReader(`Selected: ${item.textContent.trim()}`);
                        break;
                    case 'ArrowDown':
                        e.preventDefault();
                        const next = promptItems[index + 1] || promptItems[0];
                        next.focus();
                        break;
                    case 'ArrowUp':
                        e.preventDefault();
                        const prev = promptItems[index - 1] || promptItems[promptList.length - 1];
                        prev.focus();
                        break;
                    case 'Home':
                        e.preventDefault();
                        promptItems[0].focus();
                        break;
                    case 'End':
                        e.preventDefault();
                        promptItems[promptItems.length - 1].focus();
                        break;
                }
            });
        });

        // Global keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            // Alt + 1: Focus on input
            if (e.altKey && e.key === '1') {
                e.preventDefault();
                const input = document.getElementById('userInput');
                if (input) {
                    input.focus();
                    this.announceToScreenReader('Question input focused');
                }
            }
            
            // Alt + 2: Focus on prompts
            if (e.altKey && e.key === '2') {
                e.preventDefault();
                const firstPrompt = document.querySelector('#promptList .list-group-item');
                if (firstPrompt) {
                    firstPrompt.focus();
                    this.announceToScreenReader('Suggested prompts focused');
                }
            }
            
            // Alt + E: Open emergency contacts
            if (e.altKey && e.key === 'e') {
                e.preventDefault();
                this.showEmergencyModal();
            }
            
            // Alt + A: Open accessibility options
            if (e.altKey && e.key === 'a') {
                e.preventDefault();
                this.toggleAccessibilityDrawer();
            }
            
            // Alt + L: Toggle language
            if (e.altKey && e.key === 'l') {
                e.preventDefault();
                const currentLang = document.documentElement.lang || 'en';
                const newLang = currentLang === 'en' ? 'es' : 'en';
                this.changeLanguage(newLang);
            }
            
            // Alt + H: Toggle high contrast
            if (e.altKey && e.key === 'h') {
                e.preventDefault();
                this.toggleHighContrast();
            }
            
            // Alt + F: Toggle large fonts
            if (e.altKey && e.key === 'f') {
                e.preventDefault();
                this.toggleLargeFonts();
            }
            
            // Alt + D: Toggle dyslexia font
            if (e.altKey && e.key === 'd') {
                e.preventDefault();
                this.toggleDyslexiaFont();
            }
            
            // Alt + R: Toggle read aloud
            if (e.altKey && e.key === 'r') {
                e.preventDefault();
                const drawerToggle = document.getElementById('drawerReadAloudToggle');
                if (drawerToggle) {
                    drawerToggle.checked = !drawerToggle.checked;
                    this.toggleReadAloud(drawerToggle.checked);
                }
            }
            
            // Escape: Stop speech synthesis or close modals/drawers
            if (e.key === 'Escape') {
                if ('speechSynthesis' in window && speechSynthesis.speaking) {
                    speechSynthesis.cancel();
                    this.announceToScreenReader('Speech stopped');
                } else if (this.isDrawerOpen) {
                    this.closeAccessibilityDrawer();
                } else {
                    // Close any open modals
                    const modals = document.querySelectorAll('.modal.show');
                    modals.forEach(modal => {
                        const bsModal = bootstrap.Modal.getInstance(modal);
                        if (bsModal) {
                            bsModal.hide();
                        }
                    });
                }
            }
            
            // Ctrl + Enter: Submit form
            if (e.ctrlKey && e.key === 'Enter') {
                e.preventDefault();
                const form = document.getElementById('chatForm');
                if (form) {
                    form.dispatchEvent(new Event('submit'));
                }
            }
            
            // Tab navigation enhancement
            if (e.key === 'Tab') {
                // Ensure focus is visible
                document.body.classList.add('keyboard-navigation');
            }
        });
        
        // Remove keyboard navigation class when mouse is used
        document.addEventListener('mousedown', () => {
            document.body.classList.remove('keyboard-navigation');
        });
    }

    // Screen reader support
    setupScreenReaderSupport() {
        // Create live region for announcements if it doesn't exist
        if (!document.getElementById('sr-live-region')) {
            const liveRegion = document.createElement('div');
            liveRegion.id = 'sr-live-region';
            liveRegion.setAttribute('aria-live', 'polite');
            liveRegion.setAttribute('aria-atomic', 'true');
            liveRegion.className = 'sr-only';
            document.body.appendChild(liveRegion);
        }

        // Enhance form accessibility
        this.enhanceFormAccessibility();
        
        // Add keyboard shortcut information
        this.addKeyboardShortcutsInfo();
    }

    enhanceFormAccessibility() {
        const userInput = document.getElementById('userInput');
        if (userInput) {
            // Ensure proper labeling
            userInput.setAttribute('aria-describedby', 'inputHelp inputInstructions');
            
            // Add instructions for screen readers if not present
            if (!document.getElementById('inputInstructions')) {
                const instructions = document.createElement('div');
                instructions.id = 'inputInstructions';
                instructions.className = 'sr-only';
                instructions.textContent = 'Type your question about IEP or special education. Press Enter to send, Alt+1 to focus here, or Alt+2 to browse suggested prompts.';
                userInput.parentNode.appendChild(instructions);
            }
        }

        // Enhance voice button accessibility
        const voiceBtn = document.getElementById('voiceBtn');
        if (voiceBtn) {
            voiceBtn.setAttribute('aria-describedby', 'voiceInstructions');
            
            if (!document.getElementById('voiceInstructions')) {
                const voiceInstructions = document.createElement('div');
                voiceInstructions.id = 'voiceInstructions';
                voiceInstructions.className = 'sr-only';
                voiceInstructions.textContent = 'Click to use voice input. Speak your question when the microphone is active.';
                voiceBtn.parentNode.appendChild(voiceInstructions);
            }
        }
    }

    addKeyboardShortcutsInfo() {
        if (!document.getElementById('keyboard-shortcuts-info')) {
            const shortcutsInfo = document.createElement('div');
            shortcutsInfo.id = 'keyboard-shortcuts-info';
            shortcutsInfo.className = 'sr-only';
            shortcutsInfo.innerHTML = `
                <h2>Keyboard Shortcuts</h2>
                <ul>
                    <li>Alt + 1: Focus on question input</li>
                    <li>Alt + 2: Focus on suggested prompts</li>
                    <li>Alt + E: Open emergency contacts</li>
                    <li>Escape: Stop text-to-speech or close dialogs</li>
                    <li>Enter: Send message or activate button</li>
                    <li>Arrow keys: Navigate through prompts</li>
                    <li>Home/End: Jump to first/last prompt</li>
                    <li>Ctrl + Enter: Submit form</li>
                </ul>
            `;
            document.body.appendChild(shortcutsInfo);
        }
    }

    // Focus management for dynamic content
    setupFocusManagement() {
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach((node) => {
                        if (node.nodeType === Node.ELEMENT_NODE) {
                            // If a new chat message is added, announce it
                            if (node.classList?.contains('chat-message')) {
                                this.announceChatMessage(node);
                            }
                            
                            // If modal is opened, manage focus
                            if (node.classList?.contains('modal')) {
                                setTimeout(() => {
                                    const focusTarget = node.querySelector('[autofocus], .btn-close, .modal-title');
                                    if (focusTarget) {
                                        focusTarget.focus();
                                    }
                                }, 100);
                            }
                        }
                    });
                }
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    // Emergency modal setup
    setupEmergencyModal() {
        const emergencyBtns = document.querySelectorAll('#emergencyContactsBtn, [onclick*="showEmergencyModal"]');
        
        emergencyBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                this.showEmergencyModal();
            });
        });
    }

    showEmergencyModal() {
        const modal = document.getElementById('emergencyModal');
        if (modal) {
            // Check if modal is already shown
            if (modal.classList.contains('show')) {
                return; // Modal is already open
            }
            
            // Create or get existing Bootstrap modal instance
            let bsModal = bootstrap.Modal.getInstance(modal);
            if (!bsModal) {
                bsModal = new bootstrap.Modal(modal);
            }
            
            bsModal.show();
            this.announceToScreenReader('Emergency contacts dialog opened');
            
            // Focus management
            modal.addEventListener('shown.bs.modal', () => {
                const closeBtn = modal.querySelector('.btn-close');
                if (closeBtn) {
                    closeBtn.focus();
                }
            }, { once: true }); // Use once to prevent multiple listeners
            
            // Ensure close buttons work
            const closeButtons = modal.querySelectorAll('[data-bs-dismiss="modal"]');
            closeButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    bsModal.hide();
                });
            });
        }
    }



    // Announce chat messages to screen readers and read aloud if enabled
    announceChatMessage(messageElement) {
        const isBot = messageElement.classList.contains('bot');
        const isUser = messageElement.classList.contains('user');
        
        let announcement = '';
        if (isBot) {
            announcement = 'GuideAI responded: ';
        } else if (isUser) {
            announcement = 'You said: ';
        }
        
        const messageText = this.stripHtml(messageElement.textContent || '');
        announcement += messageText.substring(0, 150); // Limit length for better UX
        
        // Delay announcement slightly to ensure message is fully rendered
        setTimeout(() => {
            this.announceToScreenReader(announcement);
            
            // If read aloud is enabled and this is a bot message, read it aloud
            if (this.preferences.readAloud && isBot && messageText.trim()) {
                // Small delay to let screen reader announcement finish first
                setTimeout(() => {
                    this.speakText(messageText);
                }, 500);
            }
        }, 100);
    }

    // Announce messages to screen readers
    announceToScreenReader(message) {
        const liveRegion = document.getElementById('sr-live-region');
        if (liveRegion) {
            // Clear previous message first
            liveRegion.textContent = '';
            
            // Small delay to ensure screen readers notice the change
            setTimeout(() => {
                liveRegion.textContent = message;
            }, 10);
            
            // Clear after announcement to allow for new announcements
            setTimeout(() => {
                liveRegion.textContent = '';
            }, 3000);
        }
    }

    // Strip HTML from text for screen readers
    stripHtml(html) {
        const div = document.createElement('div');
        div.innerHTML = html;
        
        // Replace abbreviations with full text for better TTS
        const abbrs = div.querySelectorAll('abbr[title]');
        abbrs.forEach(abbr => {
            abbr.textContent = abbr.getAttribute('title');
        });
        
        return div.textContent || div.innerText || '';
    }

    // Apply saved accessibility preferences
    applyPreferences() {
        if (this.preferences.highContrast) {
            document.body.classList.add('high-contrast');
        }
        
        if (this.preferences.largeFonts) {
            document.body.classList.add('large-fonts');
        }
        
        if (this.preferences.dyslexiaFont) {
            document.body.classList.add('dyslexia-friendly');
        }
        
        if (this.preferences.reducedMotion) {
            document.body.classList.add('reduce-motion');
        }

        // Update button states
        this.updateButtonStates();
    }

    // Update accessibility button states
    updateButtonStates() {
        const buttons = {
            'toggleHighContrast': this.preferences.highContrast,
            'toggleLargeFonts': this.preferences.largeFonts,
            'toggleDyslexiaFont': this.preferences.dyslexiaFont
        };

        Object.entries(buttons).forEach(([id, active]) => {
            const btn = document.getElementById(id);
            if (btn) {
                btn.classList.toggle('active', active);
                btn.setAttribute('aria-pressed', active.toString());
            }
        });
    }

    // Add CSS styles for accessibility features
    addAccessibilityStyles() {
        if (document.getElementById('accessibility-styles')) return;

        const styles = document.createElement('style');
        styles.id = 'accessibility-styles';
        styles.textContent = `
            /* Language Loading */
            .language-loading {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(255, 255, 255, 0.9);
                display: none;
                align-items: center;
                justify-content: center;
                z-index: 9999;
                backdrop-filter: blur(2px);
            }

            .language-loading.active {
                display: flex;
            }

            /* High contrast mode adjustments */
            .high-contrast .accessibility-float-btn {
                background: #000;
                color: #fff;
                border: 2px solid #fff;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
            }

            .high-contrast .accessibility-float-btn:hover {
                background: #333;
                box-shadow: 0 6px 16px rgba(0, 0, 0, 0.7);
            }

            .high-contrast .accessibility-drawer {
                background: #000;
                color: #fff;
                border-color: #fff;
            }

            .high-contrast .drawer-header {
                background: #333;
                border-bottom-color: #fff;
            }

            .high-contrast .accessibility-section {
                border-bottom-color: #333;
            }

            /* Large fonts mode adjustments */
            .large-fonts .accessibility-float-btn {
                width: 60px;
                height: 60px;
                font-size: 1.4rem;
            }

            .large-fonts .accessibility-drawer {
                font-size: 1.1rem;
            }

            .large-fonts .drawer-header h2 {
                font-size: 1.2rem;
            }

            .large-fonts .accessibility-section h3 {
                font-size: 1.1rem;
            }

            .large-fonts .btn {
                font-size: 1rem;
                padding: 0.5rem 1rem;
            }

            /* Reduced motion support */
            @media (prefers-reduced-motion: reduce) {
                .accessibility-float-btn,
                .accessibility-drawer {
                    transition: none;
                }

                .accessibility-float-btn:hover {
                    transform: none;
                }
            }
        `;
        
        document.head.appendChild(styles);
    }

    // Public method to get current preferences
    getPreferences() {
        return { ...this.preferences };
    }

    // Public method to update preferences programmatically
    updatePreferences(newPrefs) {
        this.preferences = { ...this.preferences, ...newPrefs };
        this.applyPreferences();
        this.savePreferences();
    }

    // Sync preferences with GuideAI system
    syncWithGuideAI() {
        if (window.guideAI && typeof window.guideAI.updateAccessibilityPreferences === 'function') {
            try {
                window.guideAI.updateAccessibilityPreferences({
                    readAloud: this.preferences.readAloud,
                    ttsVoice: this.preferences.ttsVoice,
                    ttsSpeed: this.preferences.ttsSpeed
                });
                console.log('✅ Accessibility preferences synced with GuideAI');
            } catch (error) {
                console.warn('⚠️ Could not sync with GuideAI:', error);
            }
        } else {
            console.log('ℹ️ GuideAI not available for preference sync');
        }
    }

    // Test accessibility features
    testAccessibilityFeatures() {
        console.log('🧪 Testing accessibility features...');
        
        // Test read aloud
        const testText = 'This is a test of the read aloud functionality. If you can hear this, the system is working correctly.';
        
        if (this.preferences.readAloud) {
            console.log('✅ Read aloud is enabled, testing...');
            this.speakText(testText);
        } else {
            console.log('❌ Read aloud is disabled. Enable it in accessibility options to test.');
        }
        
        // Test screen reader announcement
        this.announceToScreenReader('Accessibility test completed');
        
        console.log('🧪 Accessibility test finished');
    }
    
    // Test read aloud specifically
    testReadAloud() {
        const testText = 'Testing read aloud with Nova voice. This should be spoken clearly and naturally.';
        console.log('🔊 Testing read aloud with text:', testText);
        this.speakText(testText);
    }
    
    // Read welcome message with same voice settings
    readWelcomeMessage() {
        const welcomeText = `${this.t('welcome_title')}. ${this.t('welcome_message')} ${this.t('tip_label')}: ${this.t('welcome_tip')}`;
        console.log('🔊 Reading welcome message:', welcomeText);
        this.speakText(welcomeText);
    }

    // Translation method for bilingual support
    t(key, params = {}) {
        console.log('🔤 Translation requested for key:', key);
        
        // Get current language from page
        const currentLang = document.documentElement.lang || 
                           document.querySelector('html').getAttribute('lang') || 
                           this.currentLanguage || 
                           'en';
        
        console.log('🌍 Current language detected:', currentLang);
        
        // Fallback translations
        const translations = {
            'en': {
                'accessibility_options': 'Accessibility Options',
                'open_accessibility_options': 'Accessibility Options',
                'close_accessibility_options': 'Close accessibility options',
                'language_section_title': 'Language / Idioma',
                'language_selection': 'Language selection',
                'visual_section': 'Visual',
                'audio_section': 'Audio',
                'toggle_high_contrast': 'Toggle high contrast mode',
                'high_contrast': 'High Contrast',
                'toggle_large_fonts': 'Toggle large fonts',
                'large_text': 'Large Text',
                'toggle_dyslexia_font': 'Toggle dyslexia-friendly font',
                'dyslexia_font': 'Dyslexia Font',
                'read_aloud': 'Read Aloud',
                'voice_selection': 'Voice Selection',
                'voice_alloy': 'Alloy (Neutral)',
                'voice_echo': 'Echo (Male)',
                'voice_fable': 'Fable (Male)',
                'voice_onyx': 'Onyx (Male)',
                'voice_nova': 'Nova (Female)',
                'voice_shimmer': 'Shimmer (Female)',
                'speed_control': 'Speed Control',
                'test_voice': 'Test Voice',
                'emergency_contacts_aria': 'Emergency contacts and crisis support',
                'emergency_help': 'Emergency Help',
                'keyboard_shortcuts': 'Keyboard Shortcuts',
                'focus_input': 'Focus on input field',
                'focus_prompts': 'Focus on suggested prompts',
                'emergency_contacts': 'Open emergency contacts',
                'accessibility_options': 'Accessibility Options',
                'toggle_high_contrast': 'Toggle high contrast',
                'toggle_large_fonts': 'Toggle large fonts',
                'toggle_dyslexia_font': 'Toggle dyslexia font',
                'toggle_read_aloud': 'Toggle read aloud',
                'close_or_stop': 'Close modal or stop speech',
                'submit_form': 'Submit form',
                'language_options': 'Language Options',
                'testing_tools': 'Testing Tools',
                'test_accessibility': 'Test All Features',
                'test_screen_reader': 'Test Screen Reader',
                'welcome_title': 'Welcome to GuideAI!',
                'welcome_message': 'I\'m here to help you navigate your child\'s special education journey with confidence and support.',
                'welcome_tip': 'You can ask questions about IEPs, accommodations, your rights, or use the voice input button to speak your questions!',
                'tip_label': 'Tip'
            },
            'es': {
                'accessibility_options': 'Opciones de Accesibilidad',
                'open_accessibility_options': 'Opciones de Accesibilidad',
                'close_accessibility_options': 'Cerrar opciones de accesibilidad',
                'language_section_title': 'Idioma / Language',
                'language_selection': 'Selección de idioma',
                'visual_section': 'Visual',
                'audio_section': 'Audio',
                'toggle_high_contrast': 'Alternar modo de alto contraste',
                'high_contrast': 'Alto Contraste',
                'toggle_large_fonts': 'Alternar fuentes grandes',
                'large_text': 'Texto Grande',
                'toggle_dyslexia_font': 'Alternar fuente para dislexia',
                'dyslexia_font': 'Fuente para Dislexia',
                'read_aloud': 'Lectura en Voz Alta',
                'voice_selection': 'Selección de Voz',
                'voice_alloy': 'Alloy (Neutral)',
                'voice_echo': 'Echo (Masculino)',
                'voice_fable': 'Fable (Masculino)',
                'voice_onyx': 'Onyx (Masculino)',
                'voice_nova': 'Nova (Femenino)',
                'voice_shimmer': 'Shimmer (Femenino)',
                'speed_control': 'Control de Velocidad',
                'test_voice': 'Probar Voz',
                'emergency_contacts_aria': 'Contactos de emergencia y apoyo en crisis',
                'emergency_help': 'Ayuda de Emergencia',
                'keyboard_shortcuts': 'Accesos Directos',
                'focus_input': 'Enfoque en el campo de entrada',
                'focus_prompts': 'Enfoque en sugerencias sugeridas',
                'emergency_contacts': 'Contactos de Emergencia',
                'accessibility_options': 'Accesibilidad',
                'toggle_high_contrast': 'Contraste Alto',
                'toggle_large_fonts': 'Fuentes Grandes',
                'toggle_dyslexia_font': 'Fuente para Dislexia',
                'toggle_read_aloud': 'Lectura en Voz Alta',
                'close_or_stop': 'Cerrar modal o detener voz',
                'submit_form': 'Enviar formulario',
                'language_options': 'Opciones de Idioma',
                'testing_tools': 'Herramientas de Prueba',
                'test_accessibility': 'Probar Todas las Funciones',
                'test_screen_reader': 'Probar Lector de Pantalla',
                'welcome_title': '¡Bienvenido a GuideAI!',
                'welcome_message': 'Estoy aquí para ayudarte a navegar el viaje de educación especial de tu hijo con confianza y apoyo.',
                'welcome_tip': '¡Puedes hacer preguntas sobre IEP, adaptaciones, tus derechos, o usar el botón de entrada de voz para hablar tus preguntas!',
                'tip_label': 'Consejo'
            }
        };
        
        const lang = currentLang;
        const text = translations[lang]?.[key] || translations['en']?.[key] || key;
        
        console.log('📝 Translation result:', text);
        
        // Replace parameters if any
        return text.replace(/\{(\w+)\}/g, (match, param) => {
            return params[param] || match;
        });
    }

    // Force Nova voice and update UI
    forceNovaVoice() {
        console.log('🔄 Forcing Nova voice...');
        this.preferences.ttsVoice = 'nova';
        this.savePreferences();
        this.updateDrawerContent();
        console.log('✅ Nova voice forced, preferences:', this.preferences);
    }

    // Test Nova voice specifically
    testNovaVoice() {
        console.log('🎤 Testing Nova voice specifically...');
        const testText = 'This is a test of the Nova female voice. It should sound clear and natural.';
        this.speakText(testText, { voice: 'nova' });
    }

    // Check current voice selection state
    checkVoiceState() {
        console.log('🔍 Checking voice selection state...');
        console.log('Current preferences:', this.preferences);
        
        const voiceSelect = document.getElementById('ttsVoiceSelect');
        if (voiceSelect) {
            console.log('Voice select element found:', {
                value: voiceSelect.value,
                selectedIndex: voiceSelect.selectedIndex,
                options: Array.from(voiceSelect.options).map(opt => ({ value: opt.value, selected: opt.selected }))
            });
        } else {
            console.error('❌ Voice select element not found');
        }
        
        // Check if Nova is actually selected
        if (this.preferences.ttsVoice === 'nova') {
            console.log('✅ Nova is set as preferred voice');
        } else {
            console.log('❌ Nova is NOT set as preferred voice. Current:', this.preferences.ttsVoice);
        }
    }

    // Comprehensive TTS test function
    testTTSComprehensive() {
        console.log('🧪 Running comprehensive TTS test...');
        
        // Check preferences
        console.log('📋 Current preferences:', this.preferences);
        
        // Check if read aloud is enabled
        if (!this.preferences.readAloud) {
            console.log('⚠️ Read aloud is disabled. Enabling for test...');
            this.preferences.readAloud = true;
        this.savePreferences();
    }
        
        // Force Nova voice
        console.log('🔄 Forcing Nova voice...');
        this.preferences.ttsVoice = 'nova';
        this.savePreferences();
        
        // Test with different text lengths
        const testTexts = [
            'Hello, this is a short test.',
            'This is a medium length test to verify that the Nova voice is working properly with OpenAI TTS.',
            'This is a longer test to ensure that the text-to-speech system can handle more substantial content while maintaining the Nova female voice quality and natural speech patterns.'
        ];
        
        let testIndex = 0;
        const runTest = () => {
            if (testIndex < testTexts.length) {
                console.log(`🧪 Test ${testIndex + 1}/${testTexts.length}:`, testTexts[testIndex]);
                this.speakText(testTexts[testIndex], { voice: 'nova' });
                testIndex++;
                setTimeout(runTest, 3000); // Wait 3 seconds between tests
            } else {
                console.log('✅ All TTS tests completed');
            }
        };
        
        runTest();
    }

    // Verify accessibility button visibility
    verifyButtonVisibility() {
        const button = document.getElementById('accessibilityToggle');
        if (button) {
            console.log('✅ Accessibility button found in static HTML');
            
            // Force button to be visible
            button.style.display = 'flex';
            button.style.opacity = '1';
            button.style.visibility = 'visible';
            button.style.zIndex = '9999';
            button.style.position = 'fixed';
            
            // Check if it's visible
            const rect = button.getBoundingClientRect();
            const isVisible = rect.width > 0 && rect.height > 0 && 
                            window.getComputedStyle(button).display !== 'none' &&
                            window.getComputedStyle(button).visibility !== 'hidden';
            
            console.log('Button visibility check:', {
                width: rect.width,
                height: rect.height,
                top: rect.top,
                left: rect.left,
                display: window.getComputedStyle(button).display,
                visibility: window.getComputedStyle(button).visibility,
                opacity: window.getComputedStyle(button).opacity,
                zIndex: window.getComputedStyle(button).zIndex,
                isVisible: isVisible
            });
            
            if (!isVisible) {
                console.warn('⚠️ Button may not be visible - forcing visibility');
                this.forceButtonVisibility();
            }
        } else {
            console.error('❌ Accessibility button not found');
        }
    }

    // Force button visibility
    forceButtonVisibility() {
        const button = document.getElementById('accessibilityToggle');
        if (button) {
            // Add inline styles to override any CSS conflicts
            button.style.cssText = `
                position: fixed !important;
                background: #007bff !important;
                color: white !important;
                border: none !important;
                border-radius: 50% !important;
                box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3) !important;
                z-index: 9999 !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                opacity: 1 !important;
                visibility: visible !important;
                pointer-events: auto !important;
                cursor: pointer !important;
            `;
            
            // Set responsive positioning
            this.updateButtonPosition();
            
            console.log('✅ Forced button visibility with inline styles');
        }
    }

    // Update button position based on screen size
    updateButtonPosition() {
        const button = document.getElementById('accessibilityToggle');
        if (button) {
            if (window.innerWidth <= 480) {
                // Extra small devices
                button.style.top = 'auto';
                button.style.bottom = '15px';
                button.style.left = '10px';
                button.style.width = '52px';
                button.style.height = '52px';
                button.style.fontSize = '1.2rem';
            } else if (window.innerWidth <= 768) {
                // Small devices
                button.style.top = 'auto';
                button.style.bottom = '20px';
                button.style.left = '15px';
                button.style.width = '56px';
                button.style.height = '56px';
                button.style.fontSize = '1.3rem';
            } else {
                // Desktop
                button.style.top = '80px';
                button.style.bottom = 'auto';
                button.style.left = '20px';
                button.style.width = '50px';
                button.style.height = '50px';
                button.style.fontSize = '1.2rem';
            }
        }
    }

    // Comprehensive debugging function for visual accessibility
    debugVisualAccessibility() {
        console.log('🔍 DEBUGGING VISUAL ACCESSIBILITY...');
        
        // Check if accessibility system is initialized
        console.log('1. Accessibility System Status:', {
            initialized: typeof this !== 'undefined',
            preferences: this.preferences,
            currentLanguage: this.currentLanguage
        });
        
        // Check if buttons exist
        const buttons = {
            toggleHighContrast: document.getElementById('toggleHighContrast'),
            toggleLargeFonts: document.getElementById('toggleLargeFonts'),
            toggleDyslexiaFont: document.getElementById('toggleDyslexiaFont'),
            accessibilityToggle: document.getElementById('accessibilityToggle'),
            accessibilityDrawer: document.getElementById('accessibilityDrawer')
        };
        
        console.log('2. Button Elements:', buttons);
        
        // Check current body classes
        console.log('3. Current Body Classes:', document.body.className);
        
        // Check if CSS is loaded
        const accessibilityCSS = Array.from(document.styleSheets).find(sheet => 
            sheet.href && sheet.href.includes('accessibility.css')
        );
        console.log('4. Accessibility CSS Loaded:', !!accessibilityCSS);
        
        // Test toggle functions
        console.log('5. Testing Toggle Functions...');
        
        // Test high contrast
        const originalHighContrast = this.preferences.highContrast;
        this.toggleHighContrast();
        console.log('   High Contrast Toggle:', {
            before: originalHighContrast,
            after: this.preferences.highContrast,
            bodyClass: document.body.classList.contains('high-contrast'),
            buttonActive: buttons.toggleHighContrast?.classList.contains('active')
        });
        
        // Test large fonts
        const originalLargeFonts = this.preferences.largeFonts;
        this.toggleLargeFonts();
        console.log('   Large Fonts Toggle:', {
            before: originalLargeFonts,
            after: this.preferences.largeFonts,
            bodyClass: document.body.classList.contains('large-fonts'),
            buttonActive: buttons.toggleLargeFonts?.classList.contains('active')
        });
        
        // Test dyslexia font
        const originalDyslexiaFont = this.preferences.dyslexiaFont;
        this.toggleDyslexiaFont();
        console.log('   Dyslexia Font Toggle:', {
            before: originalDyslexiaFont,
            after: this.preferences.dyslexiaFont,
            bodyClass: document.body.classList.contains('dyslexia-friendly'),
            buttonActive: buttons.toggleDyslexiaFont?.classList.contains('active')
        });
        
        // Reset to original state
        this.preferences.highContrast = originalHighContrast;
        this.preferences.largeFonts = originalLargeFonts;
        this.preferences.dyslexiaFont = originalDyslexiaFont;
        this.applyPreferences();
        
        console.log('6. Final State:', {
            preferences: this.preferences,
            bodyClasses: document.body.className
        });
        
        console.log('🔍 VISUAL ACCESSIBILITY DEBUG COMPLETE');
    }

    // Global test functions for browser console
    static testAll() {
        if (window.guideAIAccessibility) {
            window.guideAIAccessibility.debugVisualAccessibility();
        } else {
            console.error('❌ GuideAI Accessibility not initialized');
        }
    }
    
    static testHighContrast() {
        if (window.guideAIAccessibility) {
            window.guideAIAccessibility.toggleHighContrast();
        } else {
            console.error('❌ GuideAI Accessibility not initialized');
        }
    }
    
    static testLargeFonts() {
        if (window.guideAIAccessibility) {
            window.guideAIAccessibility.toggleLargeFonts();
        } else {
            console.error('❌ GuideAI Accessibility not initialized');
        }
    }
    
    static testDyslexiaFont() {
        if (window.guideAIAccessibility) {
            window.guideAIAccessibility.toggleDyslexiaFont();
        } else {
            console.error('❌ GuideAI Accessibility not initialized');
        }
    }
    
    static resetAll() {
        if (window.guideAIAccessibility) {
            window.guideAIAccessibility.resetPreferences();
            window.guideAIAccessibility.applyPreferences();
            console.log('✅ All accessibility options reset');
        } else {
            console.error('❌ GuideAI Accessibility not initialized');
        }
    }
}

// Initialize accessibility manager when DOM is ready
let accessibilityManager;

document.addEventListener('DOMContentLoaded', () => {
    // Create global accessibility manager instance
    accessibilityManager = new GuideAIAccessibility();
    window.accessibilityManager = accessibilityManager;
    window.guideAIAccessibility = accessibilityManager; // Add this alias for compatibility
    
    // Add welcome announcement for screen readers
    setTimeout(() => {
        accessibilityManager.announceToScreenReader(
            'Welcome to GuideAI. This page is optimized for screen readers and keyboard navigation. Press Alt+1 to focus on the question input, or Alt+2 to browse suggested prompts.'
        );
    }, 2000);
    
    // Check for emergency URL parameter
    try {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('emergency') === 'true') {
            setTimeout(() => {
                accessibilityManager.showEmergencyModal();
            }, 500);
        }
    } catch (error) {
        console.warn('URL parameter parsing error:', error);
        // Continue without URL parameter checking
    }
});

// Handle visibility change for better screen reader experience
document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible' && accessibilityManager) {
        // Announce when user returns to tab
        setTimeout(() => {
            accessibilityManager.announceToScreenReader('GuideAI tab is now active');
        }, 500);
    }
});

// Export for potential use in other scripts
window.GuideAIAccessibility = GuideAIAccessibility;

// Global function for testing TTS
window.testTTS = function() {
    if (window.guideAIAccessibility) {
        const testText = "This is a test of the OpenAI text-to-speech system. The voice should sound natural and clear.";
        window.guideAIAccessibility.speakText(testText);
    } else {
        console.error('Accessibility system not initialized');
    }
};

// Global function for testing read aloud specifically
window.testReadAloud = function() {
    if (window.guideAIAccessibility) {
        window.guideAIAccessibility.testReadAloud();
    } else {
        console.error('Accessibility system not initialized');
    }
};

// Global function for testing welcome message read aloud
window.testWelcomeReadAloud = function() {
    if (window.guideAIAccessibility) {
        window.guideAIAccessibility.readWelcomeMessage();
    } else {
        console.error('Accessibility system not initialized');
    }
};

// Global function to reset preferences and test Nova voice
window.resetAndTestNova = function() {
    if (window.guideAIAccessibility) {
        window.guideAIAccessibility.resetPreferences();
        window.guideAIAccessibility.updateDrawerContent();
        console.log('🔄 Preferences reset, Nova should now be selected by default');
        
        // Test the voice
        setTimeout(() => {
            window.guideAIAccessibility.speakText('Testing Nova voice after reset. This should be spoken with the Nova female voice.');
        }, 1000);
    } else {
        console.error('Accessibility system not initialized');
    }
};

// Global function to force Nova voice
window.forceNova = function() {
    if (window.guideAIAccessibility) {
        window.guideAIAccessibility.forceNovaVoice();
        setTimeout(() => {
            window.guideAIAccessibility.testNovaVoice();
        }, 500);
    } else {
        console.error('Accessibility system not initialized');
    }
};

// Global function to test Nova voice directly
window.testNovaDirect = function() {
    if (window.guideAIAccessibility) {
        window.guideAIAccessibility.testNovaVoice();
    } else {
        console.error('Accessibility system not initialized');
    }
};

// Global function to check voice state
window.checkVoiceState = function() {
    if (window.guideAIAccessibility) {
        window.guideAIAccessibility.checkVoiceState();
    } else {
        console.error('Accessibility system not initialized');
    }
};

// Global function for comprehensive TTS testing
window.testTTSComprehensive = function() {
    if (window.guideAIAccessibility) {
        window.guideAIAccessibility.testTTSComprehensive();
    } else {
        console.error('Accessibility system not initialized');
    }
};

// Global function to force Nova voice and test
window.forceNovaAndTest = function() {
    if (window.guideAIAccessibility) {
        window.guideAIAccessibility.forceNovaVoice();
        setTimeout(() => {
            window.guideAIAccessibility.testNovaVoice();
        }, 500);
    } else {
        console.error('Accessibility system not initialized');
    }
};

// Global function to test TTS with specific text
window.testTTSWithText = function(text = 'This is a test of the Nova voice.') {
    if (window.guideAIAccessibility) {
        window.guideAIAccessibility.speakText(text, { voice: 'nova' });
    } else {
        console.error('Accessibility system not initialized');
    }
};

// Global test function for accessibility button
window.testAccessibilityButton = function() {
    console.log('🧪 Testing accessibility button...');
    
    const button = document.getElementById('accessibilityToggle');
    if (button) {
        console.log('✅ Button found:', button);
        console.log('Button styles:', {
            display: window.getComputedStyle(button).display,
            visibility: window.getComputedStyle(button).visibility,
            opacity: window.getComputedStyle(button).opacity,
            position: window.getComputedStyle(button).position,
            zIndex: window.getComputedStyle(button).zIndex,
            top: window.getComputedStyle(button).top,
            left: window.getComputedStyle(button).left,
            bottom: window.getComputedStyle(button).bottom,
            width: window.getComputedStyle(button).width,
            height: window.getComputedStyle(button).height
        });
        
        const rect = button.getBoundingClientRect();
        console.log('Button rect:', rect);
        
        // Force visibility
        button.style.display = 'flex';
        button.style.visibility = 'visible';
        button.style.opacity = '1';
        button.style.zIndex = '9999';
        
        console.log('✅ Button visibility forced');
        
        // Test click
        button.click();
        console.log('✅ Button click tested');
        
    } else {
        console.error('❌ Button not found');
    }
};

// Global test function for visual accessibility options
window.testVisualAccessibility = function() {
    console.log('🧪 Testing visual accessibility options...');
    
    // Check if accessibility system is initialized
    if (window.guideAIAccessibility) {
        console.log('✅ Accessibility system found:', window.guideAIAccessibility);
        
        // Test high contrast
        console.log('🔍 Testing high contrast toggle...');
        window.guideAIAccessibility.toggleHighContrast();
        
        // Wait 2 seconds, then test large fonts
        setTimeout(() => {
            console.log('🔍 Testing large fonts toggle...');
            window.guideAIAccessibility.toggleLargeFonts();
        }, 2000);
        
        // Wait 4 seconds, then test dyslexia font
        setTimeout(() => {
            console.log('🔍 Testing dyslexia font toggle...');
            window.guideAIAccessibility.toggleDyslexiaFont();
        }, 4000);
        
        // Wait 6 seconds, then reset all
        setTimeout(() => {
            console.log('🔍 Resetting all visual options...');
            window.guideAIAccessibility.resetPreferences();
            window.guideAIAccessibility.applyPreferences();
        }, 6000);
        
    } else {
        console.error('❌ Accessibility system not found. Make sure GuideAIAccessibility is initialized.');
    }
};

// Global function to check accessibility system status
window.checkAccessibilityStatus = function() {
    console.log('🔍 Checking accessibility system status...');
    
    // Check if class exists
    console.log('GuideAIAccessibility class exists:', typeof GuideAIAccessibility !== 'undefined');
    
    // Check if instance exists
    console.log('Accessibility instance exists:', !!window.guideAIAccessibility);
    
    if (window.guideAIAccessibility) {
        console.log('Current preferences:', window.guideAIAccessibility.getPreferences());
        console.log('Button states:', {
            highContrast: document.getElementById('toggleHighContrast')?.classList.contains('active'),
            largeFonts: document.getElementById('toggleLargeFonts')?.classList.contains('active'),
            dyslexiaFont: document.getElementById('toggleDyslexiaFont')?.classList.contains('active')
        });
        console.log('Body classes:', document.body.className);
    }
    
    // Check if buttons exist
    console.log('Accessibility buttons found:', {
        toggleHighContrast: !!document.getElementById('toggleHighContrast'),
        toggleLargeFonts: !!document.getElementById('toggleLargeFonts'),
        toggleDyslexiaFont: !!document.getElementById('toggleDyslexiaFont'),
        accessibilityToggle: !!document.getElementById('accessibilityToggle'),
        accessibilityDrawer: !!document.getElementById('accessibilityDrawer')
    });
};

// Make it available immediately
if (typeof window !== 'undefined') {
    window.testAccessibilityButton = window.testAccessibilityButton;
}

// Make test functions available globally
window.GuideAIAccessibility = GuideAIAccessibility;
window.testAccessibility = GuideAIAccessibility.testAll;
window.testHighContrast = GuideAIAccessibility.testHighContrast;
window.testLargeFonts = GuideAIAccessibility.testLargeFonts;
window.testDyslexiaFont = GuideAIAccessibility.testDyslexiaFont;
window.resetAccessibility = GuideAIAccessibility.resetAll;