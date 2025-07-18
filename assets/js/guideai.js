/**
 * GuideAI Enhanced Main JavaScript
 * Handles chat functionality, multi-AI integration, and accessibility
 */

class GuideAI {
    constructor() {
        // Enhanced AI endpoints with specific roles
        const timestamp = Date.now();
        this.apiEndpoints = [
            `/api/claude.php?t=${timestamp}`,           // Claude for response processing
            `/api/gemini.php?t=${timestamp}`,           // Gemini for links/locations
            `/api.php?t=${timestamp}`,                  // OpenAI fallback
        ];
        
        this.currentEndpointIndex = 0;
        this.endpointHealth = {};
        this.apiEndpoints.forEach(endpoint => {
            this.endpointHealth[endpoint] = { healthy: true, lastError: null, errorCount: 0, lastUsed: 0 };
        });
        
        // Enhanced user preferences
        this.userPreferences = {
            language: document.documentElement.lang || 'en',
            responseStyle: 'conversational', // conversational, formal, simple
            resourceLinks: true,
            animations: true,
            readAloud: false,
            ttsVoice: 'nova',
            ttsSpeed: 1.0,
            highContrast: false,
            largeFonts: false,
            dyslexiaFont: false,
            saveChatHistory: true,
            maxHistoryItems: 100
        };
        
        // Performance optimizations
        this.responseCache = new Map();
        this.cacheExpiry = 5 * 60 * 1000; // 5 minutes
        this.isProcessing = false;
        this.chatHistory = [];
        this.currentConversationId = this.generateConversationId();
        
        // Enhanced accessibility
        this.accessibilityFeatures = {
            screenReader: false,
            keyboardNavigation: true,
            focusManagement: true,
            liveRegions: true
        };
        
        // Progressive response display
        this.progressiveDisplay = {
            enabled: false, // Disabled by default for reliability
            chunkSize: 50,
            delay: 30
        };
        
        console.log('🚀 Initializing Enhanced GuideAI...', {
            endpoints: this.apiEndpoints,
            preferences: this.userPreferences,
            accessibility: this.accessibilityFeatures
        });
        
        this.init();
    }

    init() {
        console.log('🚀 Starting GuideAI initialization...');
        
        // Initialize DOM elements
        console.log('🔧 Initializing DOM elements...');
        this.initializeDOMElements();
        
        // Setup accessibility integration first
        console.log('🔗 Setting up accessibility integration...');
        this.setupAccessibilityIntegration();
        
        // Setup enhanced event listeners
        console.log('🎧 Setting up enhanced event listeners...');
        this.setupEnhancedEventListeners();
        
        // Initialize improved input area
        console.log('📝 Initializing input area...');
        this.initializeInputArea();
        
        // Initialize local storage
        console.log('💾 Initializing local storage...');
        this.initializeLocalStorage();
        
        // Load user preferences
        console.log('⚙️ Loading user preferences...');
        this.loadUserPreferences();
        
        // Initialize accessibility system
        console.log('♿ Initializing accessibility system...');
        this.initializeAccessibility();
        
        // Setup progressive response system
        console.log('📊 Setting up progressive response system...');
        this.setupProgressiveDisplay();
        
        // Test all endpoints
        console.log('🔍 Testing all endpoints...');
        this.testAllEndpoints();
        
        // Ensure welcome message is visible
        setTimeout(() => {
            this.ensureWelcomeMessageVisible();
        }, 500);
        
        // Test enhanced formatting
        setTimeout(() => {
            this.testEnhancedFormatting();
            // Run diagnostics after a delay
            setTimeout(() => {
                this.diagnoseMessageVisibility();
            }, 2000);
        }, 1000);
        
        console.log('✅ Enhanced GuideAI initialized successfully');
    }

    initializeDOMElements() {
        this.chatForm = document.getElementById('chatForm');
        this.userInput = document.getElementById('userInput');
        this.chatMessages = document.getElementById('chatMessages');
        this.voiceBtn = document.getElementById('voiceBtn');
        this.clearBtn = document.getElementById('clearBtn');
        this.printBtn = document.getElementById('printBtn');
        this.charCount = document.getElementById('charCount');
        
        // Enhanced elements
        this.responseContainer = document.getElementById('responseContainer');
        this.typingIndicator = document.getElementById('typingIndicator');
        this.resourcePanel = document.getElementById('resourcePanel');
        this.preferencesPanel = document.getElementById('preferencesPanel');
        
        // Debug DOM element finding
        console.log('🔍 DOM Elements Found:', {
            clearBtn: !!this.clearBtn,
            printBtn: !!this.printBtn,
            voiceBtn: !!this.voiceBtn,
            chatForm: !!this.chatForm,
            userInput: !!this.userInput
        });
    }

    setupEnhancedEventListeners() {
        // Enhanced form submission with progressive display
        if (this.chatForm) {
            console.log('🔧 Setting up form event listener');
            
            this.chatForm.addEventListener('submit', (e) => {
                e.preventDefault();
                e.stopPropagation();
                console.log('📝 Form submitted, calling handleEnhancedSubmit');
                this.handleEnhancedSubmit();
                return false;
            });
            
            console.log('✅ Form event listener set up successfully');
        } else {
            console.error('❌ Chat form not found');
        }

        // Enhanced input handling
        if (this.userInput) {
            this.userInput.addEventListener('input', () => {
                this.updateCharCount();
                this.debounceSearch();
            });
            
            this.userInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.handleEnhancedSubmit();
                }
            });
            
            // Enhanced focus management
            this.userInput.addEventListener('focus', () => {
                this.announceToScreenReader('Chat input focused');
            });
        }

        // Enhanced clear chat with robust event binding
        this.setupButtonEventListener('clearBtn', this.clearBtn, '🧹 Clear button clicked', () => {
            this.clearChatWithConfirmation();
        });

        // Enhanced print chat with robust event binding
        this.setupButtonEventListener('printBtn', this.printBtn, '🖨️ Print button clicked', () => {
            this.printEnhancedChat();
        });

        // Enhanced voice input with robust event binding
        this.setupButtonEventListener('voiceBtn', this.voiceBtn, '🎤 Voice button clicked', () => {
            this.startEnhancedVoiceInput();
        });

        // Setup prompt sidebar with enhanced interactions
        this.setupEnhancedPromptSidebar();
        
        // Setup keyboard shortcuts
        this.setupKeyboardShortcuts();
    }

    // Robust button event listener setup
    setupButtonEventListener(buttonName, buttonElement, logMessage, callback) {
        // Primary method: Use the provided element
        if (buttonElement) {
            console.log(`✅ Setting up ${buttonName} event listener`);
            buttonElement.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                console.log(logMessage);
                callback();
            });
            return;
        }

        // Fallback method: Try to find the element by ID
        console.warn(`⚠️ ${buttonName} not found, trying fallback...`);
        const fallbackElement = document.getElementById(buttonName);
        
        if (fallbackElement) {
            console.log(`✅ Found ${buttonName} via fallback method`);
            fallbackElement.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                console.log(logMessage);
                callback();
            });
            return;
        }

        // Ultimate fallback: Set up a document-level listener
        console.warn(`❌ ${buttonName} not found, setting up document-level fallback`);
        document.addEventListener('click', (e) => {
            if (e.target && e.target.id === buttonName) {
                e.preventDefault();
                e.stopPropagation();
                console.log(`${logMessage} (via document listener)`);
                callback();
            }
        });
    }

    setupAccessibilityIntegration() {
        // Integrate with accessibility system if available
        if (window.guideAIAccessibility) {
            console.log('🔗 Integrating with accessibility system');
            
            // Announce chat events to screen readers
            this.announceToScreenReader = (message) => {
                window.guideAIAccessibility.announceToScreenReader(message);
            };
            
            // Coordinate language changes
            this.updateLanguage = (lang) => {
                window.guideAIAccessibility.changeLanguage(lang);
            };
            
            // Expose GuideAI methods to accessibility system
            this.readAloudEnabled = false;
            this.accessibilityPreferences = {
                readAloud: false,
                ttsVoice: 'nova',
                ttsSpeed: 1.0
            };
        } else {
            // Silent fallback - no warning needed
            this.announceToScreenReader = (message) => {
                // Fallback announcement
                const announcement = document.createElement('div');
                announcement.setAttribute('aria-live', 'polite');
                announcement.setAttribute('aria-atomic', 'true');
                announcement.className = 'sr-only';
                announcement.textContent = message;
                document.body.appendChild(announcement);
                setTimeout(() => {
                    if (document.body.contains(announcement)) {
                        document.body.removeChild(announcement);
                    }
                }, 1000);
            };
            
            this.updateLanguage = (lang) => {
                // Fallback language change
                console.log('Language change requested:', lang);
            };
            
            // Initialize accessibility preferences even without accessibility system
            this.readAloudEnabled = false;
            this.accessibilityPreferences = {
                readAloud: false,
                ttsVoice: 'nova',
                ttsSpeed: 1.0
            };
        }
    }

    // Save accessibility preferences (called by accessibility system)
    savePreferences() {
        try {
            if (this.accessibilityPreferences) {
                localStorage.setItem('guideai_accessibility_preferences', JSON.stringify(this.accessibilityPreferences));
                console.log('✅ GuideAI preferences saved:', this.accessibilityPreferences);
            }
        } catch (error) {
            console.warn('Could not save GuideAI preferences:', error);
        }
    }

    // Load accessibility preferences
    loadPreferences() {
        try {
            const saved = localStorage.getItem('guideai_accessibility_preferences');
            if (saved) {
                this.accessibilityPreferences = JSON.parse(saved);
                this.readAloudEnabled = this.accessibilityPreferences.readAloud || false;
                console.log('✅ GuideAI preferences loaded:', this.accessibilityPreferences);
            }
        } catch (error) {
            console.warn('Could not load GuideAI preferences:', error);
            // Set defaults
            this.accessibilityPreferences = {
                readAloud: false,
                ttsVoice: 'nova',
                ttsSpeed: 1.0
            };
            this.readAloudEnabled = false;
        }
    }

    // Update accessibility preferences
    updateAccessibilityPreferences(newPrefs) {
        if (this.accessibilityPreferences) {
            this.accessibilityPreferences = { ...this.accessibilityPreferences, ...newPrefs };
            this.readAloudEnabled = this.accessibilityPreferences.readAloud || false;
            this.savePreferences();
        }
    }

    setupPromptSidebar() {
        const promptList = document.getElementById('promptList');
        if (!promptList) return;

        // Add click and keyboard event listeners to prompt items
        const promptItems = promptList.querySelectorAll('.list-group-item');
        promptItems.forEach(item => {
            // Click event
            item.addEventListener('click', () => {
                const prompt = item.getAttribute('data-prompt');
                if (prompt && this.userInput) {
                    this.userInput.value = prompt;
                    this.updateCharCount();
                    this.userInput.focus();
                    this.announceToScreenReader(`Selected prompt: ${prompt}`);
                }
            });

            // Keyboard event (Enter and Space)
            item.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    item.click();
                }
            });

            // Hover effect for better UX
            item.addEventListener('mouseenter', () => {
                item.style.cursor = 'pointer';
            });
        });
    }

    async handleSubmit() {
        const message = this.userInput.value.trim();
        if (!message || this.isProcessing) return;

        // Add user message to chat
        this.addMessage(message, 'user');
        this.userInput.value = '';
        this.updateCharCount();

        // Show typing indicator
        this.showTypingIndicator();

        try {
            this.isProcessing = true;

            // Get user location for context
            const location = await this.getUserLocation();

            // Prepare request data
            const requestData = {
                question: message,
                language: this.currentLanguage,
                user_location: location,
                urgency: 'normal',
                family_context: true
            };

            // Try multiple endpoints in order
            let response = null;
            let lastError = null;
            
            for (let i = 0; i < this.apiEndpoints.length; i++) {
                const endpoint = this.apiEndpoints[i];
                console.log(`Trying endpoint ${i + 1}/${this.apiEndpoints.length}: ${endpoint}`);
                
                try {
                    response = await this.callAPI(endpoint, requestData);
                    
                    if (response.success) {
                        // Mark this endpoint as healthy
                        this.endpointHealth[endpoint].healthy = true;
                        this.endpointHealth[endpoint].errorCount = 0;
                        this.currentEndpointIndex = i;
                        console.log(`✅ Success with endpoint: ${endpoint}`);
                        break;
                    } else {
                        // Mark endpoint as having issues
                        this.markEndpointUnhealthy(endpoint, response.error);
                        lastError = response.error;
                    }
                } catch (error) {
                    // Mark endpoint as unhealthy
                    this.markEndpointUnhealthy(endpoint, error.message);
                    lastError = error.message;
                }
            }

            // Handle response
            if (response && response.success) {
                let content = '';
                if (response.result && response.result.mega_response) {
                    content = response.result.mega_response;
                } else if (response.content) {
                    content = response.content;
                } else if (response.response) {
                    content = response.response;
                } else {
                    content = 'Response received';
                }
                
                this.addMessage(content, 'bot');
                this.announceToScreenReader('GuideAI responded to your question');
            } else {
                throw new Error(lastError || 'All AI services are currently unavailable');
            }

        } catch (error) {
            console.error('API Error:', error);
            let errorMessage = this.getErrorMessage(error);
            
            // Provide more specific error messages for common issues
            if (error.message.includes('503') || error.message.includes('Service Unavailable')) {
                errorMessage = 'The GuideAI service is temporarily unavailable. Please try again in a few minutes.';
            } else if (error.message.includes('timeout') || error.message.includes('timed out')) {
                errorMessage = 'The request took too long. Please try again.';
            } else if (error.message.includes('network') || error.message.includes('fetch')) {
                errorMessage = 'Network connection issue. Please check your internet connection and try again.';
            } else if (error.message.includes('API key not configured') || error.message.includes('not configured')) {
                errorMessage = 'The AI service is not properly configured. Please contact support for assistance.';
            }
            
            this.addMessage(errorMessage, 'bot error');
            this.announceToScreenReader('An error occurred while processing your request');
        } finally {
            this.hideTypingIndicator();
            this.isProcessing = false;
        }
    }

    async callAPI(endpoint, data) {
        console.log('🌐 GuideAI callAPI - Making request to:', endpoint);
        console.log('📤 Request data:', data);
        
        try {
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 30000); // 30 second timeout per endpoint
            
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data),
                signal: controller.signal
            });

            clearTimeout(timeoutId);
            console.log('📥 GuideAI callAPI - Response status:', response.status);

            if (!response.ok) {
                const errorText = await response.text();
                console.error('❌ HTTP Error Response:', errorText);
                throw new Error(`HTTP ${response.status}: ${response.statusText} - ${errorText}`);
            }

            const result = await response.json();
            console.log('✅ GuideAI callAPI - Response received:', result);
            return result;

        } catch (error) {
            console.error('❌ GuideAI callAPI - Error:', error);
            console.error('Error details:', {
                name: error.name,
                message: error.message,
                stack: error.stack
            });
            
            if (error.name === 'AbortError') {
                return {
                    success: false,
                    error: 'Request timed out. Please try again.'
                };
            }
            
            // Mark endpoint as unhealthy for certain errors
            if (error.message.includes('HTTP 400') || error.message.includes('HTTP 401') || 
                error.message.includes('HTTP 403') || error.message.includes('HTTP 404')) {
                this.markEndpointUnhealthy(endpoint, error.message);
            }
            
            return {
                success: false,
                error: error.message
            };
        }
    }

    markEndpointUnhealthy(endpoint, error) {
        if (this.endpointHealth[endpoint]) {
            this.endpointHealth[endpoint].healthy = false;
            this.endpointHealth[endpoint].lastError = error;
            this.endpointHealth[endpoint].errorCount++;
            console.log(`❌ Endpoint marked unhealthy: ${endpoint} (Error: ${error})`);
        }
    }

    getNextHealthyEndpoint() {
        // Find the next healthy endpoint
        for (let i = 0; i < this.apiEndpoints.length; i++) {
            const endpoint = this.apiEndpoints[i];
            if (this.endpointHealth[endpoint] && this.endpointHealth[endpoint].healthy) {
                return endpoint;
            }
        }
        return null; // No healthy endpoints found
    }

    resetEndpointHealth() {
        // Reset all endpoints to healthy after a period
        this.apiEndpoints.forEach(endpoint => {
            if (this.endpointHealth[endpoint]) {
                this.endpointHealth[endpoint].healthy = true;
                this.endpointHealth[endpoint].errorCount = 0;
            }
        });
        console.log('🔄 All endpoints reset to healthy state');
    }

    ensureWelcomeMessageVisible() {
        // Find the welcome message (first bot message with alert class)
        const welcomeMessage = this.chatMessages.querySelector('.chat-message.bot .alert');
        if (welcomeMessage) {
            // Scroll to show the top of the welcome message
            const messageTop = welcomeMessage.offsetTop;
            const containerHeight = this.chatMessages.offsetHeight;
            
            // Position the welcome message at the top of the visible area
            this.chatMessages.scrollTop = Math.max(0, messageTop - 20); // 20px padding
            
            console.log('✅ Welcome message positioned for visibility');
        } else {
            // Fallback to regular scroll if welcome message not found
            this.scrollToBottom();
        }
    }

    addMessage(content, type) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `chat-message ${type}`;
        
        if (type === 'user') {
            messageDiv.innerHTML = `
                <div class="user-message">
                    <div class="message-content">
                        ${this.escapeHtml(content)}
                    </div>
                    <div class="message-time">${this.getCurrentTime()}</div>
                </div>
            `;
        } else if (type === 'bot') {
            messageDiv.innerHTML = `
                <div class="bot-message" style="display: block; margin: 10px 0; padding: 15px; background-color: #ede7f6; border-radius: 12px; border-left: 4px solid #6f42c1;">
                    <div class="message-content" style="display: block;">
                        <div style="display: flex; align-items-start; margin-bottom: 8px;">
                            <div style="flex-grow: 1;">
                                <small style="color: #6c757d; display: block; margin-bottom: 5px;">
                                    GuideAI Assistant
                                </small>
                                <div style="color: #212121; line-height: 1.5;">
                                    ${this.formatBotResponse(content)}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="message-time" style="color: #6c757d; font-size: 0.875rem; margin-top: 8px;">${this.getCurrentTime()}</div>
                </div>
            `;
            console.log('🔧 Bot message HTML created with inline styles for visibility');
        } else if (type === 'bot error') {
            messageDiv.innerHTML = `
                <div class="bot-message error">
                    <div class="message-content">
                        <strong>Error:</strong> ${this.escapeHtml(content)}
                    </div>
                    <div class="message-time">${this.getCurrentTime()}</div>
                </div>
            `;
        }

        this.chatMessages.appendChild(messageDiv);
        
        // Ensure the new message is visible
        this.scrollToBottom();
        
        // Force scroll after a brief delay to handle any dynamic content
        setTimeout(() => {
            this.scrollToBottom();
        }, 200);
        
        // Add to chat history using the new method
        this.addToChatHistory({
            role: type === 'user' ? 'user' : 'assistant',
            content: content
        });
    }

    showTypingIndicator() {
        const typingDiv = document.createElement('div');
        typingDiv.id = 'typingIndicator';
        typingDiv.className = 'chat-message bot typing';
        typingDiv.innerHTML = `
            <div class="bot-message">
                <div class="message-content">
                    <span class="typing-dots">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </div>
            </div>
        `;
        this.chatMessages.appendChild(typingDiv);
        this.scrollToBottom();
    }

    hideTypingIndicator() {
        const typingIndicator = document.getElementById('typingIndicator');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }

    updateCharCount() {
        if (this.charCount && this.userInput) {
            const count = this.userInput.value.length;
            this.charCount.textContent = count;
            
            // Visual feedback for character limit
            if (count > 450) {
                this.charCount.style.color = '#dc3545';
            } else if (count > 400) {
                this.charCount.style.color = '#ffc107';
            } else {
                this.charCount.style.color = '';
            }
        }
    }

    async startVoiceInput() {
        console.log('🎤 Starting voice input...');
        
        if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
            console.error('❌ Speech recognition not supported');
            alert('Speech recognition is not supported in this browser. Please use Chrome, Edge, or Safari.');
            return;
        }

        try {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            const recognition = new SpeechRecognition();

            recognition.continuous = false;
            recognition.interimResults = false;
            recognition.lang = this.currentLanguage === 'es' ? 'es-ES' : 'en-US';

            // Update button to show recording state
            this.voiceBtn.innerHTML = '<i class="fas fa-stop" aria-hidden="true"></i>';
            this.voiceBtn.classList.add('listening');
            this.voiceBtn.classList.remove('recording');

            recognition.onstart = () => {
                console.log('🎤 Speech recognition started');
                this.announceToScreenReader('Listening for voice input');
            };

            recognition.onresult = (event) => {
                const transcript = event.results[0][0].transcript;
                console.log('🎤 Speech recognized:', transcript);
                
                if (this.userInput) {
                    this.userInput.value = transcript;
                    this.updateCharCount();
                    this.userInput.focus();
                }
                
                this.announceToScreenReader(`Recognized: ${transcript}`);
            };

            recognition.onend = () => {
                console.log('🎤 Speech recognition ended');
                this.voiceBtn.innerHTML = '<i class="fas fa-microphone" aria-hidden="true"></i>';
                this.voiceBtn.classList.remove('listening');
                this.voiceBtn.classList.remove('recording');
            };

            recognition.onerror = (event) => {
                console.error('❌ Speech recognition error:', event.error);
                this.voiceBtn.innerHTML = '<i class="fas fa-microphone" aria-hidden="true"></i>';
                this.voiceBtn.classList.remove('listening');
                this.voiceBtn.classList.remove('recording');
                
                let errorMessage = 'Speech recognition error';
                switch (event.error) {
                    case 'no-speech':
                        errorMessage = 'No speech detected. Please try again.';
                        break;
                    case 'audio-capture':
                        errorMessage = 'Microphone not found or access denied.';
                        break;
                    case 'not-allowed':
                        errorMessage = 'Microphone access denied. Please allow microphone access.';
                        break;
                    case 'network':
                        errorMessage = 'Network error occurred. Please check your connection.';
                        break;
                    default:
                        errorMessage = `Speech recognition error: ${event.error}`;
                }
                
                this.announceToScreenReader(errorMessage);
            };

            recognition.start();
            console.log('✅ Voice input started successfully');
            
        } catch (error) {
            console.error('❌ Error starting voice input:', error);
            this.voiceBtn.innerHTML = '<i class="fas fa-microphone" aria-hidden="true"></i>';
            this.voiceBtn.classList.remove('listening');
            this.voiceBtn.classList.remove('recording');
            alert('Error starting voice input. Please try again.');
        }
    }

    clearChat() {
        if (confirm('Are you sure you want to clear the conversation?')) {
            // Keep only the welcome message
            const welcomeMessage = this.chatMessages.querySelector('.chat-message.bot .alert');
            this.chatMessages.innerHTML = '';
            if (welcomeMessage) {
                this.chatMessages.appendChild(welcomeMessage.parentElement);
            }
            this.chatHistory = [];
            this.announceToScreenReader('Chat conversation cleared');
        }
    }

    printChat() {
        const printWindow = window.open('', '_blank');
        const messages = this.chatMessages.innerHTML;
        
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>GuideAI Conversation</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    .chat-message { margin-bottom: 15px; }
                    .user-message { text-align: right; }
                    .bot-message { text-align: left; }
                    .message-time { font-size: 12px; color: #666; }
                    @media print { .no-print { display: none; } }
                </style>
            </head>
            <body>
                <h1>GuideAI Conversation</h1>
                <p>Date: ${new Date().toLocaleDateString()}</p>
                <div class="chat-content">${messages}</div>
            </body>
            </html>
        `);
        
        printWindow.document.close();
        printWindow.print();
    }

    async getUserLocation() {
        return new Promise((resolve) => {
            if ('geolocation' in navigator) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        resolve(`${position.coords.latitude},${position.coords.longitude}`);
                    },
                    () => {
                        resolve('Bend, Oregon, US'); // Default location
                    },
                    { timeout: 5000 }
                );
            } else {
                resolve('Bend, Oregon, US'); // Default location
            }
        });
    }

    async testConnection() {
        console.log('🔍 Testing all API endpoints...');
        let healthyEndpoints = 0;
        
        for (let i = 0; i < this.apiEndpoints.length; i++) {
            const endpoint = this.apiEndpoints[i];
            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        question: 'test',
                        test: true,
                        language: 'en'
                    })
                });
                
                if (response.ok) {
                    this.endpointHealth[endpoint].healthy = true;
                    this.endpointHealth[endpoint].errorCount = 0;
                    healthyEndpoints++;
                    console.log(`✅ Endpoint ${i + 1} healthy: ${endpoint}`);
                } else if (response.status === 400) {
                    // Skip endpoints that don't support test requests
                    console.log(`⚠️ Endpoint ${i + 1} doesn't support test requests: ${endpoint}`);
                    this.endpointHealth[endpoint].healthy = true; // Mark as healthy but skip testing
                } else {
                    this.markEndpointUnhealthy(endpoint, `HTTP ${response.status}`);
                    console.log(`❌ Endpoint ${i + 1} failed: ${endpoint}`);
                }
            } catch (error) {
                this.markEndpointUnhealthy(endpoint, error.message);
                console.log(`❌ Endpoint ${i + 1} error: ${endpoint} - ${error.message}`);
            }
        }
        
        if (healthyEndpoints > 0) {
            console.log(`✅ ${healthyEndpoints}/${this.apiEndpoints.length} endpoints healthy`);
        } else {
            console.log('❌ All endpoints unhealthy');
        }
        
        // Reset endpoint health every 5 minutes
        setTimeout(() => {
            this.resetEndpointHealth();
        }, 300000);
    }



    getErrorMessage(error) {
        if (error.message.includes('503') || error.message.includes('Service Unavailable')) {
            return 'The GuideAI service is temporarily unavailable. Please try again in a few minutes.';
        } else if (error.message.includes('Failed to fetch') || error.message.includes('network')) {
            return 'Unable to connect to the AI service. Please check your internet connection and try again.';
        } else if (error.message.includes('timeout') || error.message.includes('timed out')) {
            return 'The request took too long. Please try again.';
        } else if (error.message.includes('API')) {
            return 'The AI service is temporarily unavailable. Please try again in a few moments.';
        } else {
            return 'An unexpected error occurred. Please try again.';
        }
    }

    formatBotResponse(content) {
        console.log('🔧 formatBotResponse called with:', content.substring(0, 100) + '...');
        
        // Check if content is already HTML (from backend processing)
        const isAlreadyHtml = content.includes('<h') || content.includes('<p>') || 
                             content.includes('<ul>') || content.includes('<div>') ||
                             content.includes('<strong>') || content.includes('<em>');
        
        if (isAlreadyHtml) {
            console.log('✅ Content is already HTML, applying minimal enhancements');
            // Content is already processed HTML from backend, just add some enhancements
            let enhancedContent = content;
            
            // Enhance special terms and phone numbers in HTML content
            enhancedContent = enhancedContent
                .replace(/(IEP|IDEA|FAPE|504 Plan)/gi, '<span class="special-term">$1</span>')
                .replace(/(\d{3}-\d{3}-\d{4}|\(\d{3}\)\s*\d{3}-\d{4})/g, '<span class="phone-number">📞 $1</span>');
            
            return enhancedContent;
        }
        
        // Content is raw markdown/text, apply full formatting
        console.log('✅ Content is raw markdown, applying full formatting');
        let formattedContent = content;
        
        // First, strip any accidental code blocks that might interfere
        formattedContent = formattedContent
            .replace(/```markdown\s*/g, '')
            .replace(/```\s*/g, '')
            .replace(/`([^`]+)`/g, '<code class="response-code">$1</code>');
        
        // Ensure proper line breaks before headers for better parsing
        formattedContent = formattedContent
            .replace(/([^\n])\n(#{1,6}\s)/g, '$1\n\n$2');
        
        // Convert Markdown headers
        formattedContent = formattedContent
            .replace(/^## (.*?)$/gm, '<h4 class="response-header">$1</h4>')
            .replace(/^### (.*?)$/gm, '<h5 class="response-subheader">$1</h5>')
            .replace(/^#### (.*?)$/gm, '<h6 class="response-subheader">$1</h6>');
        
        // Convert Markdown bold and italic
        formattedContent = formattedContent
            .replace(/\*\*(.*?)\*\*/g, '<strong class="response-bold">$1</strong>')
            .replace(/\*(.*?)\*/g, '<em class="response-italic">$1</em>');
        
        // Convert Markdown lists with smart icons
        formattedContent = formattedContent
            .replace(/^- (.*?)$/gm, function(match, content) {
                let iconClass = 'response-list-item';
                const lowerContent = content.toLowerCase();
                
                // Legal Rights & Compliance
                if (lowerContent.includes('right') || lowerContent.includes('legal') || lowerContent.includes('law') || 
                    lowerContent.includes('idea') || lowerContent.includes('504') || lowerContent.includes('iep') ||
                    lowerContent.includes('fape') || lowerContent.includes('complaint') || lowerContent.includes('appeal') ||
                    lowerContent.includes('due process') || lowerContent.includes('mediation') || lowerContent.includes('hearing')) {
                    iconClass += ' info';
                }
                // Warnings & Cautions
                else if (lowerContent.includes('warning') || lowerContent.includes('caution') || lowerContent.includes('attention') ||
                         lowerContent.includes('be careful') || lowerContent.includes('avoid') || lowerContent.includes('risk') ||
                         lowerContent.includes('danger') || lowerContent.includes('problematic') || lowerContent.includes('concerning')) {
                    iconClass += ' warning';
                }
                // Errors & Problems
                else if (lowerContent.includes('error') || lowerContent.includes('problem') || lowerContent.includes('issue') ||
                         lowerContent.includes('violation') || lowerContent.includes('denied') || lowerContent.includes('refused') ||
                         lowerContent.includes('wrong') || lowerContent.includes('incorrect') || lowerContent.includes('failed')) {
                    iconClass += ' error';
                }
                // Success & Achievements
                else if (lowerContent.includes('success') || lowerContent.includes('completed') || lowerContent.includes('done') ||
                         lowerContent.includes('achieved') || lowerContent.includes('approved') || lowerContent.includes('granted') ||
                         lowerContent.includes('won') || lowerContent.includes('passed') || lowerContent.includes('accomplished')) {
                    iconClass += ' success';
                }
                // Steps & Actions
                else if (lowerContent.includes('step') || lowerContent.includes('next') || lowerContent.includes('then') ||
                         lowerContent.includes('action') || lowerContent.includes('do') || lowerContent.includes('call') ||
                         lowerContent.includes('write') || lowerContent.includes('request') || lowerContent.includes('schedule') ||
                         lowerContent.includes('meet') || lowerContent.includes('contact') || lowerContent.includes('submit')) {
                    iconClass += ' step';
                }
                // Tips & Advice
                else if (lowerContent.includes('tip') || lowerContent.includes('hint') || lowerContent.includes('suggestion') ||
                         lowerContent.includes('advice') || lowerContent.includes('recommend') || lowerContent.includes('consider') ||
                         lowerContent.includes('try') || lowerContent.includes('helpful') || lowerContent.includes('useful')) {
                    iconClass += ' tip';
                }
                // Information & Resources
                else if (lowerContent.includes('info') || lowerContent.includes('note') || lowerContent.includes('important') ||
                         lowerContent.includes('resource') || lowerContent.includes('contact') || lowerContent.includes('phone') ||
                         lowerContent.includes('email') || lowerContent.includes('website') || lowerContent.includes('address') ||
                         lowerContent.includes('organization') || lowerContent.includes('agency') || lowerContent.includes('department')) {
                    iconClass += ' info';
                }
                
                return '<li class="' + iconClass + '">' + content + '</li>';
            })
            .replace(/^• (.*?)$/gm, function(match, content) {
                let iconClass = 'response-list-item';
                const lowerContent = content.toLowerCase();
                
                if (lowerContent.includes('warning') || lowerContent.includes('caution') || lowerContent.includes('attention')) {
                    iconClass += ' warning';
                } else if (lowerContent.includes('error') || lowerContent.includes('problem') || lowerContent.includes('issue')) {
                    iconClass += ' error';
                } else if (lowerContent.includes('success') || lowerContent.includes('completed') || lowerContent.includes('done')) {
                    iconClass += ' success';
                } else if (lowerContent.includes('step') || lowerContent.includes('next') || lowerContent.includes('then')) {
                    iconClass += ' step';
                } else if (lowerContent.includes('tip') || lowerContent.includes('hint') || lowerContent.includes('suggestion')) {
                    iconClass += ' tip';
                } else if (lowerContent.includes('info') || lowerContent.includes('note') || lowerContent.includes('important')) {
                    iconClass += ' info';
                }
                
                return '<li class="' + iconClass + '">' + content + '</li>';
            })
            .replace(/^\* (.*?)$/gm, function(match, content) {
                let iconClass = 'response-list-item';
                const lowerContent = content.toLowerCase();
                
                if (lowerContent.includes('warning') || lowerContent.includes('caution') || lowerContent.includes('attention')) {
                    iconClass += ' warning';
                } else if (lowerContent.includes('error') || lowerContent.includes('problem') || lowerContent.includes('issue')) {
                    iconClass += ' error';
                } else if (lowerContent.includes('success') || lowerContent.includes('completed') || lowerContent.includes('done')) {
                    iconClass += ' success';
                } else if (lowerContent.includes('step') || lowerContent.includes('next') || lowerContent.includes('then')) {
                    iconClass += ' step';
                } else if (lowerContent.includes('tip') || lowerContent.includes('hint') || lowerContent.includes('suggestion')) {
                    iconClass += ' tip';
                } else if (lowerContent.includes('info') || lowerContent.includes('note') || lowerContent.includes('important')) {
                    iconClass += ' info';
                }
                
                return '<li class="' + iconClass + '">' + content + '</li>';
            })
            .replace(/^– (.*?)$/gm, function(match, content) {
                let iconClass = 'response-list-item';
                const lowerContent = content.toLowerCase();
                
                if (lowerContent.includes('warning') || lowerContent.includes('caution') || lowerContent.includes('attention')) {
                    iconClass += ' warning';
                } else if (lowerContent.includes('error') || lowerContent.includes('problem') || lowerContent.includes('issue')) {
                    iconClass += ' error';
                } else if (lowerContent.includes('success') || lowerContent.includes('completed') || lowerContent.includes('done')) {
                    iconClass += ' success';
                } else if (lowerContent.includes('step') || lowerContent.includes('next') || lowerContent.includes('then')) {
                    iconClass += ' step';
                } else if (lowerContent.includes('tip') || lowerContent.includes('hint') || lowerContent.includes('suggestion')) {
                    iconClass += ' tip';
                } else if (lowerContent.includes('info') || lowerContent.includes('note') || lowerContent.includes('important')) {
                    iconClass += ' info';
                }
                
                return '<li class="' + iconClass + '">' + content + '</li>';
            })
            .replace(/^— (.*?)$/gm, function(match, content) {
                let iconClass = 'response-list-item';
                const lowerContent = content.toLowerCase();
                
                if (lowerContent.includes('warning') || lowerContent.includes('caution') || lowerContent.includes('attention')) {
                    iconClass += ' warning';
                } else if (lowerContent.includes('error') || lowerContent.includes('problem') || lowerContent.includes('issue')) {
                    iconClass += ' error';
                } else if (lowerContent.includes('success') || lowerContent.includes('completed') || lowerContent.includes('done')) {
                    iconClass += ' success';
                } else if (lowerContent.includes('step') || lowerContent.includes('next') || lowerContent.includes('then')) {
                    iconClass += ' step';
                } else if (lowerContent.includes('tip') || lowerContent.includes('hint') || lowerContent.includes('suggestion')) {
                    iconClass += ' tip';
                } else if (lowerContent.includes('info') || lowerContent.includes('note') || lowerContent.includes('important')) {
                    iconClass += ' info';
                }
                
                return '<li class="' + iconClass + '">' + content + '</li>';
            });
        
        // Wrap consecutive list items in <ul> tags
        formattedContent = formattedContent.replace(
            /(<li class="response-list-item">.*?<\/li>)(\s*<li class="response-list-item">.*?<\/li>)*/gs,
            '<ul class="response-list">$&</ul>'
        );
        
        // Also handle any remaining asterisks that might not have been converted
        formattedContent = formattedContent.replace(
            /^\* (.*?)$/gm,
            '<li class="response-list-item">$1</li>'
        );
        
        // Wrap any remaining list items that weren't wrapped
        formattedContent = formattedContent.replace(
            /(<li class="response-list-item">.*?<\/li>)(\s*<li class="response-list-item">.*?<\/li>)*/gs,
            '<ul class="response-list">$&</ul>'
        );
        
        // Enhance special terms and phone numbers
        formattedContent = formattedContent
            .replace(/(IEP|IDEA|FAPE|504 Plan)/gi, '<span class="special-term">$1</span>')
            .replace(/(\d{3}-\d{3}-\d{4}|\(\d{3}\)\s*\d{3}-\d{4})/g, '<span class="phone-number">📞 $1</span>');
        
        // Handle line breaks and paragraphs
        if (!formattedContent.includes('<p>') && !formattedContent.includes('<h')) {
            formattedContent = formattedContent
                .replace(/\n\n+/g, '</p><p class="response-paragraph">')
                .replace(/\n/g, '<br>');
            
            // Wrap in paragraph tags
            if (!formattedContent.startsWith('<')) {
                formattedContent = '<p class="response-paragraph">' + formattedContent + '</p>';
            }
        }
        
        console.log('✅ Applied comprehensive formatting to raw content');
        return formattedContent;
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    getCurrentTime() {
        return new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    scrollToBottom() {
        // Calculate the height of the latest message to show its top
        const messages = this.chatMessages.querySelectorAll('.chat-message');
        if (messages.length > 0) {
            const lastMessage = messages[messages.length - 1];
            const lastMessageHeight = lastMessage.offsetHeight;
            const containerHeight = this.chatMessages.offsetHeight;
            
            // Scroll to show the top of the last message
            const scrollPosition = this.chatMessages.scrollHeight - containerHeight - lastMessageHeight;
            this.chatMessages.scrollTop = Math.max(0, scrollPosition);
        }
        
        // Additional scroll attempts to ensure it works
        setTimeout(() => {
            const messages = this.chatMessages.querySelectorAll('.chat-message');
            if (messages.length > 0) {
                const lastMessage = messages[messages.length - 1];
                const lastMessageHeight = lastMessage.offsetHeight;
                const containerHeight = this.chatMessages.offsetHeight;
                const scrollPosition = this.chatMessages.scrollHeight - containerHeight - lastMessageHeight;
                this.chatMessages.scrollTop = Math.max(0, scrollPosition);
            }
        }, 100);
        
        setTimeout(() => {
            const messages = this.chatMessages.querySelectorAll('.chat-message');
            if (messages.length > 0) {
                const lastMessage = messages[messages.length - 1];
                const lastMessageHeight = lastMessage.offsetHeight;
                const containerHeight = this.chatMessages.offsetHeight;
                const scrollPosition = this.chatMessages.scrollHeight - containerHeight - lastMessageHeight;
                this.chatMessages.scrollTop = Math.max(0, scrollPosition);
            }
        }, 300);
    }
    
    generateResourceLinks() {
        const resources = {
            federal: [
                { name: 'U.S. Department of Education', url: 'https://www.ed.gov/special-education', phone: '1-800-872-5327' },
                { name: 'IDEA Website', url: 'https://sites.ed.gov/idea/', phone: '' },
                { name: 'Office of Special Education Programs', url: 'https://www2.ed.gov/about/offices/list/osers/osep/index.html', phone: '202-245-7459' },
                { name: 'National Center for Learning Disabilities', url: 'https://www.ncld.org/', phone: '888-575-7373' }
            ],
            state: [
                { name: 'Oregon Department of Education', url: 'https://www.oregon.gov/ode/students-and-family/specialeducation/Pages/default.aspx', phone: '503-947-5600' }
            ],
            county: [
                { name: 'Central Oregon Disability Support Network', url: 'https://www.codsn.org/', phone: '541-548-8558' },
                { name: 'FACT Oregon', url: 'https://www.factoregon.org/', phone: '503-581-8156' }
            ],
            local: [
                { name: 'Disability Rights Oregon', url: 'https://droregon.org/', phone: '503-243-2081' },
                { name: 'Bend-La Pine Schools Special Education', url: 'https://www.bend.k12.or.us/district/departments/special-education', phone: '541-355-1000' }
            ],
            crisis: [
                { name: 'National Suicide Prevention Lifeline', url: 'https://988lifeline.org/', phone: '988' },
                { name: 'Crisis Text Line', url: 'https://www.crisistextline.org/', phone: 'Text HOME to 741741' },
                { name: 'Emergency Services', url: '', phone: '911' }
            ]
        };
        
        let resourceHtml = `
            <div class="resource-links mt-4 p-3 bg-light rounded border">
                <h6 class="text-primary mb-3">
                    Resources & Contacts
                </h6>
                <div class="row g-3">
        `;
        
        // Federal Resources
        resourceHtml += `
            <div class="col-md-6">
                <div class="resource-category">
                    <h6 class="text-dark mb-2">
                        Federal Resources
                    </h6>
                    <ul class="list-unstyled small">
        `;
        resources.federal.forEach(resource => {
            resourceHtml += `
                <li class="mb-2">
                    <a href="${resource.url}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                        <i class="fas fa-external-link-alt text-info me-1" aria-hidden="true"></i>${resource.name}
                    </a>
                    ${resource.phone ? `<br><small class="text-muted"><i class="fas fa-phone text-success me-1" aria-hidden="true"></i>${resource.phone}</small>` : ''}
                </li>
            `;
        });
        resourceHtml += `
                    </ul>
                </div>
            </div>
        `;
        
        // State Resources
        resourceHtml += `
            <div class="col-md-6">
                <div class="resource-category">
                    <h6 class="text-dark mb-2">
                        Oregon Resources
                    </h6>
                    <ul class="list-unstyled small">
        `;
        resources.state.forEach(resource => {
            resourceHtml += `
                <li class="mb-2">
                    <a href="${resource.url}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                        <i class="fas fa-external-link-alt text-info me-1" aria-hidden="true"></i>${resource.name}
                    </a>
                    ${resource.phone ? `<br><small class="text-muted"><i class="fas fa-phone text-success me-1" aria-hidden="true"></i>${resource.phone}</small>` : ''}
                </li>
            `;
        });
        resourceHtml += `
                    </ul>
                </div>
            </div>
        `;
        
        // County Resources
        resourceHtml += `
            <div class="col-md-6">
                <div class="resource-category">
                    <h6 class="text-dark mb-2">
                        Central Oregon County Resources
                    </h6>
                    <ul class="list-unstyled small">
        `;
        resources.county.forEach(resource => {
            resourceHtml += `
                <li class="mb-2">
                    <a href="${resource.url}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                        <i class="fas fa-external-link-alt text-info me-1" aria-hidden="true"></i>${resource.name}
                    </a>
                    ${resource.phone ? `<br><small class="text-muted"><i class="fas fa-phone text-success me-1" aria-hidden="true"></i>${resource.phone}</small>` : ''}
                </li>
            `;
        });
        resourceHtml += `
                    </ul>
                </div>
            </div>
        `;
        
        // Local Resources
        resourceHtml += `
            <div class="col-md-6">
                <div class="resource-category">
                    <h6 class="text-dark mb-2">
                        Local Resources
                    </h6>
                    <ul class="list-unstyled small">
        `;
        resources.local.forEach(resource => {
            resourceHtml += `
                <li class="mb-2">
                    <a href="${resource.url}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                        <i class="fas fa-external-link-alt text-info me-1" aria-hidden="true"></i>${resource.name}
                    </a>
                    ${resource.phone ? `<br><small class="text-muted"><i class="fas fa-phone text-success me-1" aria-hidden="true"></i>${resource.phone}</small>` : ''}
                </li>
            `;
        });
        resourceHtml += `
                    </ul>
                </div>
            </div>
        `;
        
        // Crisis Resources
        resourceHtml += `
            <div class="col-md-6">
                <div class="resource-category">
                    <h6 class="text-danger mb-2">
                        Crisis Support
                    </h6>
                    <ul class="list-unstyled small">
        `;
        resources.crisis.forEach(resource => {
            resourceHtml += `
                <li class="mb-2">
                    ${resource.url ? `<a href="${resource.url}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                        <i class="fas fa-exclamation-triangle text-danger me-1" aria-hidden="true"></i>${resource.name}
                    </a>` : `<strong><i class="fas fa-exclamation-triangle text-danger me-1" aria-hidden="true"></i>${resource.name}</strong>`}
                    ${resource.phone ? `<br><small class="text-muted"><i class="fas fa-phone text-danger me-1" aria-hidden="true"></i>${resource.phone}</small>` : ''}
                </li>
            `;
        });
        resourceHtml += `
                    </ul>
                </div>
            </div>
        `;
        
        resourceHtml += `
                </div>
                <div class="mt-3 pt-2 border-top">
                    <small class="text-muted">
                        These resources are provided for informational purposes. For urgent situations, please contact emergency services at 911.
                    </small>
                </div>
            </div>
        `;
        
        return resourceHtml;
    }
    
    generateBasicResponse(message, language) {
        const question = message.toLowerCase();
        
        if (language === 'es') {
            if (question.includes('iep')) {
                return "## ¿Qué es un IEP?\n\nUn IEP (Programa Educativo Individualizado) es un documento legal que describe la educación especial y los servicios relacionados que su hijo necesita para tener éxito en la escuela.\n\n### Componentes principales:\n- Evaluación actual\n- Metas anuales\n- Servicios\n- Acomodaciones\n- Participación\n\n### Recursos de Oregon:\n- **Departamento de Educación de Oregon**: https://oregon.gov/ode\n- **Derechos de Discapacidad de Oregon**: 503-243-2081";
            } else {
                return "## Respuesta de GuideAI\n\nEntiendo que está buscando información sobre educación especial. Como asistente compasivo, estoy aquí para ayudarle.\n\n### Recursos útiles:\n- **Centro de Información para Padres**: https://parentcenterhub.org\n- **Departamento de Educación de Oregon**: https://oregon.gov/ode\n\n¿Hay algún tema específico sobre el que le gustaría más información?";
            }
        } else {
            if (question.includes('iep')) {
                return "## What is an IEP?\n\nAn IEP (Individualized Education Program) is a legal document that describes the special education and related services your child needs to succeed in school.\n\n### Key Components:\n- Current Performance\n- Annual Goals\n- Services\n- Accommodations\n- Participation\n\n### Oregon Resources:\n- **Oregon Department of Education**: https://oregon.gov/ode\n- **Disability Rights Oregon**: 503-243-2081";
            } else {
                return "## GuideAI Response\n\nI understand you're seeking information about special education. As a compassionate assistant, I'm here to help you navigate this process.\n\n### Helpful Resources:\n- **Center for Parent Information**: https://parentcenterhub.org\n- **Oregon Department of Education**: https://oregon.gov/ode\n\nIs there a specific topic you'd like more information about?";
            }
        }
    }

    async handleEnhancedSubmit(message = null) {
        console.log('🚀 handleEnhancedSubmit called');
        
        // Get message from parameter or from input if not provided
        if (!message) {
            if (!this.userInput) {
                console.error('❌ userInput not found');
                return;
            }
            message = this.userInput.value.trim();
        }
        
        console.log('📝 Message:', message);
        
        if (!message) {
            console.log('⚠️ Empty message, returning');
            return;
        }
        
        if (this.isProcessing) {
            console.log('⚠️ Already processing, returning');
            return;
        }

        console.log('🚀 Starting enhanced submit for message:', message);

        // Add user message with enhanced styling
        this.addEnhancedMessage(message, 'user');
        if (this.userInput) {
            this.userInput.value = '';
            this.updateCharCount();
        }

        // Show enhanced typing indicator
        this.showEnhancedTypingIndicator();

        try {
            this.isProcessing = true;

            // Get user location for context
            const location = await this.getUserLocation();

            // Try multiple endpoints in order
            let response = null;
            let lastError = null;
            
            for (let i = 0; i < this.apiEndpoints.length; i++) {
                const endpoint = this.apiEndpoints[i];
                console.log(`Trying endpoint ${i + 1}/${this.apiEndpoints.length}: ${endpoint}`);
                
                // Prepare request data based on endpoint type
                let requestData;
                if (endpoint.includes('claude.php')) {
                    // Claude API format
                    requestData = {
                        action: 'chat',
                        message: message,
                        context: {
                            language: this.currentLanguage,
                            location: location,
                            urgency: 'normal',
                            family_context: true
                        },
                        preferences: {
                            language: this.currentLanguage,
                            responseStyle: 'conversational'
                        }
                    };
                } else if (endpoint.includes('gemini.php')) {
                    // Gemini API format
                    requestData = {
                        action: 'chat',
                        message: message,
                        location: location,
                        history: this.chatHistory.slice(-5) // Last 5 messages
                    };
                } else {
                    // Main API format
                    requestData = {
                        question: message,
                        language: this.currentLanguage,
                        user_location: location,
                        urgency: 'normal',
                        family_context: true
                    };
                }
                
                try {
                    response = await this.callAPI(endpoint, requestData);
                    
                    if (response.success) {
                        // Mark this endpoint as healthy
                        this.endpointHealth[endpoint].healthy = true;
                        this.endpointHealth[endpoint].errorCount = 0;
                        this.currentEndpointIndex = i;
                        console.log(`✅ Success with endpoint: ${endpoint}`);
                        break;
                    } else {
                        // Mark endpoint as having issues
                        this.markEndpointUnhealthy(endpoint, response.error);
                        lastError = response.error;
                    }
                } catch (error) {
                    // Mark endpoint as unhealthy
                    this.markEndpointUnhealthy(endpoint, error.message);
                    lastError = error.message;
                }
            }

            // Handle response
            if (response && response.success) {
                console.log('✅ API Response received:', response);
                let content = '';
                let resources = [];
                
                // Check for different response structures
                if (response.data && response.data.content) {
                    // Claude API format
                    content = response.data.content;
                    resources = response.data.resources || [];
                } else if (response.result && response.result.mega_response) {
                    // Main API format
                    content = response.result.mega_response;
                    // Extract resources from the response
                    if (response.result.gemini_resources) {
                        resources = response.result.gemini_resources;
                    }
                } else if (response.content) {
                    // Direct content format
                    content = response.content;
                    resources = response.resources || [];
                } else if (response.response) {
                    // Alternative response format
                    content = response.response;
                    resources = response.resources || [];
                } else {
                    content = 'Response received';
                }
                
                console.log('📝 Content to display:', content);
                console.log('📝 Content length:', content.length);
                console.log('📝 Content type:', typeof content);
                console.log('📝 Resources found:', resources.length);
                
                // Create a structured response object for display
                const displayResponse = {
                    content: content,
                    resources: resources
                };
                
                // Use the progressive response display which handles resources properly
                await this.displayProgressiveResponse(displayResponse);
                console.log('📝 Progressive response display completed');
                this.announceToScreenReader('GuideAI responded to your question');
            } else {
                console.error('❌ API Response failed:', response);
                throw new Error(lastError || 'All AI services are currently unavailable');
            }

            this.announceToScreenReader('GuideAI responded to your question');

        } catch (error) {
            console.error('❌ Enhanced API Error:', error);
            console.error('Error details:', {
                message: error.message,
                stack: error.stack,
                name: error.name
            });
            
            const errorMessage = this.getEnhancedErrorMessage(error);
            this.addEnhancedMessage(errorMessage, 'bot error');
            this.announceToScreenReader('An error occurred while processing your request');
        } finally {
            this.hideEnhancedTypingIndicator();
            this.isProcessing = false;
            console.log('✅ Enhanced submit completed');
        }
    }

    async generateMultiAIResponse(message, context) {
        console.log('🤖 Generating multi-AI response...');
        
        // Step 1: Generate core content with Claude
        let coreContent = null;
        try {
            coreContent = await this.callClaudeAPI(message, context);
            console.log('✅ Claude response generated:', coreContent);
        } catch (error) {
            console.warn('⚠️ Claude failed, trying OpenAI fallback:', error.message);
            try {
                coreContent = await this.callOpenAIAPI(message, context);
                console.log('✅ OpenAI fallback response generated:', coreContent);
            } catch (fallbackError) {
                console.error('❌ All AI services failed:', fallbackError.message);
                console.error('❌ Claude error:', error.message);
                console.error('❌ OpenAI error:', fallbackError.message);
                coreContent = this.generateFallbackResponse(message, context);
                console.log('🔄 Using fallback response:', coreContent);
            }
        }

        // Step 2: Enhance with Gemini for links and locations
        let enhancedContent = coreContent;
        try {
            const geminiEnhancement = await this.callGeminiAPI(message, context, coreContent);
            enhancedContent = this.mergeAIResponses(coreContent, geminiEnhancement);
            console.log('✅ Gemini enhancement applied');
        } catch (error) {
            console.warn('⚠️ Gemini enhancement failed, using core content:', error.message);
        }

        // Step 3: Apply user preferences and formatting
        const formattedResponse = this.applyUserPreferences(enhancedContent);
        
        return {
            content: formattedResponse.content,
            resources: formattedResponse.resources,
            metadata: {
                ai_used: ['claude', 'gemini'],
                processing_time: Date.now(),
                user_preferences: this.userPreferences
            }
        };
    }

    generateFallbackResponse(message, context) {
        console.log('🔄 Generating fallback response for:', message);
        
        const fallbackResponses = {
            'iep': 'I understand you\'re asking about IEPs. While I\'m currently experiencing technical difficulties, here are some general steps:\n\n1. **Request an evaluation** from your school district\n2. **Attend all meetings** with prepared questions\n3. **Keep detailed records** of all communications\n4. **Know your rights** under IDEA law\n5. **Consider an advocate** if needed\n\nFor immediate assistance, please contact your local Parent Training and Information Center.',
            'accommodation': 'Regarding accommodations, here are key points to remember:\n\n1. **Accommodations must be individualized** for your child\'s specific needs\n2. **They should be documented** in the IEP or 504 plan\n3. **Common types include**: extended time, preferential seating, assistive technology\n4. **You can request changes** if accommodations aren\'t working\n5. **The school must provide** reasonable accommodations\n\nContact your child\'s teacher or special education coordinator for specific guidance.',
            'rights': 'Your parental rights in special education include:\n\n1. **Right to participate** in all IEP meetings\n2. **Right to request evaluations** and independent evaluations\n3. **Right to receive written notice** before any changes\n4. **Right to dispute decisions** through due process\n5. **Right to access records** and request corrections\n\nFor specific legal advice, contact your state\'s Protection and Advocacy agency.',
            'default': 'I\'m here to help with special education questions. While I\'m experiencing technical difficulties, here are some helpful resources:\n\n• **Parent Center Hub**: Find your local parent center\n• **Wrightslaw**: Legal information and advocacy\n• **Understood.org**: Practical strategies and support\n• **Your state\'s Department of Education**: Local policies and procedures\n\nPlease try again in a few minutes, or contact your school district for immediate assistance.'
        };
        
        const lowerMessage = message.toLowerCase();
        let response = fallbackResponses.default;
        
        if (lowerMessage.includes('iep')) {
            response = fallbackResponses.iep;
        } else if (lowerMessage.includes('accommodation') || lowerMessage.includes('accommodate')) {
            response = fallbackResponses.accommodation;
        } else if (lowerMessage.includes('right') || lowerMessage.includes('legal')) {
            response = fallbackResponses.rights;
        }
        
        return response;
    }

    async callClaudeAPI(message, context) {
        const endpoint = this.apiEndpoints.claude;
        const requestData = {
            action: 'chat',
            message: message,
            context: context,
            preferences: this.userPreferences
        };

        console.log('🤖 Calling Claude API with:', requestData);
        const response = await this.callAPI(endpoint, requestData);
        console.log('🤖 Claude API response:', response);
        
        if (response.success && response.data) {
            return response.data.content || response.data;
        } else {
            throw new Error(response.error || 'Claude API failed');
        }
    }

    async callGeminiAPI(message, context, coreContent) {
        const endpoint = this.apiEndpoints.gemini;
        const requestData = {
            action: 'enhance',
            message: message,
            context: context,
            core_content: coreContent,
            focus: 'links_and_locations'
        };

        console.log('🤖 Calling Gemini API with:', requestData);
        const response = await this.callAPI(endpoint, requestData);
        console.log('🤖 Gemini API response:', response);
        
        if (response.success && response.data) {
            return response.data;
        } else {
            throw new Error(response.error || 'Gemini API failed');
        }
    }

    async callOpenAIAPI(message, context) {
        const endpoint = this.apiEndpoints.openai;
        const requestData = {
            question: message,
            language: this.userPreferences.language || 'en',
            user_location: context.location || null,
            urgency: 'normal',
            family_context: true
        };

        console.log('🤖 Calling OpenAI API with:', requestData);
        const response = await this.callAPI(endpoint, requestData);
        console.log('🤖 OpenAI API response:', response);
        
        if (response.success && response.result) {
            return response.result.mega_response || response.result.content || response.result.response;
        } else {
            throw new Error(response.error || 'OpenAI API failed');
        }
    }

    mergeAIResponses(coreContent, geminiEnhancement) {
        // Merge Claude's core content with Gemini's enhancements
        let mergedContent = coreContent;
        
        if (geminiEnhancement.links) {
            mergedContent += '\n\n## Related Resources\n';
            geminiEnhancement.links.forEach(link => {
                mergedContent += `- [${link.title}](${link.url}) - ${link.description}\n`;
            });
        }
        
        if (geminiEnhancement.locations) {
            mergedContent += '\n\n## Local Resources\n';
            geminiEnhancement.locations.forEach(location => {
                mergedContent += `- **${location.name}** - ${location.address}\n  Phone: ${location.phone}\n`;
            });
        }
        
        return mergedContent;
    }

    applyUserPreferences(content) {
        let processedContent = content;
        
        // Apply response style
        switch (this.userPreferences.responseStyle) {
            case 'simple':
                processedContent = this.simplifyContent(processedContent);
                break;
            case 'formal':
                processedContent = this.makeFormal(processedContent);
                break;
            default: // conversational
                processedContent = this.makeConversational(processedContent);
        }
        
        // Extract resources if enabled
        const resources = this.userPreferences.resourceLinks ? 
            this.extractResources(processedContent) : [];
        
        return {
            content: processedContent,
            resources: resources
        };
    }

    async displayProgressiveResponse(response) {
        console.log('🎨 Displaying progressive response:', response);
        
        const messageDiv = this.createEnhancedMessageContainer();
        console.log('📝 Created message container:', messageDiv);
        
        this.chatMessages.appendChild(messageDiv);
        console.log('📝 Appended to chat messages');
        
        if (this.progressiveDisplay.enabled) {
            console.log('📝 Using progressive display');
            await this.displayProgressiveContent(response.content, messageDiv);
        } else {
            console.log('📝 Using full content display');
            this.displayFullContent(response.content, messageDiv);
        }
        
        // Add resources if available
        if (response.resources && response.resources.length > 0) {
            console.log('📝 Adding resource panel');
            this.addResourcePanel(response.resources, messageDiv);
        }
        
        // Scroll to show the new message
        this.scrollToMessage(messageDiv);
        console.log('📝 Scrolled to message');
        
        // Add to chat history
        this.addToChatHistory(response);
        console.log('✅ Response display completed');
    }

    async displayProgressiveContent(content, messageDiv) {
        console.log('📝 Starting progressive content display:', content);
        
        const contentContainer = messageDiv.querySelector('.message-content');
        console.log('📝 Content container found:', !!contentContainer);
        
        if (!contentContainer) {
            console.error('❌ Content container not found, falling back to full display');
            this.displayFullContent(content, messageDiv);
            return;
        }
        
        const words = content.split(' ');
        console.log('📝 Words to display:', words.length);
        let currentText = '';
        
        try {
            for (let i = 0; i < words.length; i++) {
                currentText += words[i] + ' ';
                
                if (i % this.progressiveDisplay.chunkSize === 0 || i === words.length - 1) {
                    const formattedContent = this.formatBotResponse(currentText.trim());
                    contentContainer.innerHTML = formattedContent;
                    this.scrollToMessage(messageDiv);
                    
                    if (i < words.length - 1) {
                        await this.delay(this.progressiveDisplay.delay);
                    }
                }
            }
            console.log('✅ Progressive content display completed');
        } catch (error) {
            console.error('❌ Progressive display failed, falling back to full display:', error);
            this.displayFullContent(content, messageDiv);
        }
    }

    displayFullContent(content, messageDiv) {
        console.log('📝 Displaying full content:', content);
        console.log('📝 Message div:', messageDiv);
        
        const contentContainer = messageDiv.querySelector('.message-content');
        console.log('📝 Content container found:', !!contentContainer);
        
        if (contentContainer) {
            const formattedContent = this.formatBotResponse(content);
            console.log('📝 Formatted content:', formattedContent);
            contentContainer.innerHTML = formattedContent;
            console.log('✅ Content displayed successfully');
        } else {
            console.error('❌ Content container not found');
        }
    }

    createEnhancedMessageContainer() {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'chat-message bot enhanced';
        messageDiv.innerHTML = `
            <div class="bot-message">
                <div class="message-header">
                    <div class="ai-indicator">
                        <i class="fas fa-robot text-primary" aria-hidden="true"></i>
                        <span class="ai-name">GuideAI Assistant</span>
                    </div>
                    <div class="message-time">${this.getCurrentTime()}</div>
                </div>
                <div class="message-content">
                    <div class="typing-indicator">
                        <span></span><span></span><span></span>
                    </div>
                </div>
                <div class="message-actions">
                    <button class="btn btn-sm btn-outline-secondary copy-btn" title="Copy message">
                        <i class="fas fa-copy" aria-hidden="true"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary read-btn" title="Read aloud">
                        <i class="fas fa-volume-up" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        `;
        
        // Add event listeners for actions
        this.setupMessageActions(messageDiv);
        
        return messageDiv;
    }



    addEnhancedMessage(content, type) {
        console.log('🔧 addEnhancedMessage called with:', { content: content.substring(0, 100) + '...', type });
        console.log('🔧 chatMessages element:', this.chatMessages);
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `chat-message ${type} enhanced`;
        console.log('🔧 Created messageDiv with class:', messageDiv.className);
        
        if (type === 'user') {
            messageDiv.innerHTML = `
                <div class="user-message">
                    <div class="message-content">
                        ${this.escapeHtml(content)}
                    </div>
                    <div class="message-time">${this.getCurrentTime()}</div>
                </div>
            `;
        } else if (type === 'bot') {
            console.log('🔧 Creating bot message HTML');
            messageDiv.innerHTML = `
                <div class="bot-message" style="display: block; margin: 10px 0; padding: 15px; background-color: #ede7f6; border-radius: 12px; border-left: 4px solid #6f42c1;">
                    <div class="message-content" style="display: block;">
                        <div style="display: flex; align-items-start; margin-bottom: 8px;">
                            <div style="flex-grow: 1;">
                                <small style="color: #6c757d; display: block; margin-bottom: 5px;">
                                    GuideAI Assistant
                                </small>
                                <div style="color: #212121; line-height: 1.5;">
                                    ${this.formatBotResponse(content)}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="message-time" style="color: #6c757d; font-size: 0.875rem; margin-top: 8px;">${this.getCurrentTime()}</div>
                </div>
            `;
            console.log('🔧 Bot message HTML created with inline styles for visibility');
        } else if (type === 'bot error') {
            messageDiv.innerHTML = `
                <div class="bot-message error">
                    <div class="message-content">
                        <strong>Error:</strong> ${this.escapeHtml(content)}
                    </div>
                    <div class="message-time">${this.getCurrentTime()}</div>
                </div>
            `;
        }

        console.log('🔧 Appending messageDiv to chatMessages');
        if (!this.chatMessages) {
            console.error('❌ chatMessages element not found!');
            return;
        }
        
        // Check chatMessages properties before appending
        console.log('🔧 chatMessages properties:', {
            offsetHeight: this.chatMessages.offsetHeight,
            scrollHeight: this.chatMessages.scrollHeight,
            clientHeight: this.chatMessages.clientHeight,
            style: this.chatMessages.style.cssText,
            className: this.chatMessages.className,
            children: this.chatMessages.children.length
        });
        
        this.chatMessages.appendChild(messageDiv);
        console.log('🔧 Message appended successfully');
        
        // Check messageDiv properties after appending
        console.log('🔧 messageDiv properties after append:', {
            offsetHeight: messageDiv.offsetHeight,
            offsetWidth: messageDiv.offsetWidth,
            style: messageDiv.style.cssText,
            className: messageDiv.className,
            innerHTML: messageDiv.innerHTML.substring(0, 200) + '...'
        });
        
        // Check chatMessages properties after appending
        console.log('🔧 chatMessages properties after append:', {
            offsetHeight: this.chatMessages.offsetHeight,
            scrollHeight: this.chatMessages.scrollHeight,
            clientHeight: this.chatMessages.clientHeight,
            children: this.chatMessages.children.length
        });
        
        this.scrollToMessage(messageDiv);
        console.log('🔧 Scrolled to message');
        
        // Add to chat history
        this.chatHistory.push({
            role: type === 'user' ? 'user' : 'assistant',
            content: content,
            timestamp: new Date().toISOString(),
            conversationId: this.currentConversationId
        });
        console.log('🔧 Added to chat history');
        
        // Force a repaint to ensure visibility
        setTimeout(() => {
            messageDiv.style.display = 'none';
            messageDiv.offsetHeight; // Force reflow
            messageDiv.style.display = '';
            console.log('🔧 Forced repaint of message');
        }, 100);
    }

    showEnhancedTypingIndicator() {
        const typingDiv = document.createElement('div');
        typingDiv.id = 'typingIndicator';
        typingDiv.className = 'chat-message bot typing enhanced';
        typingDiv.innerHTML = `
            <div class="bot-message">
                <div class="message-header">
                    <div class="ai-indicator">
                        <i class="fas fa-robot text-primary" aria-hidden="true"></i>
                        <span class="ai-name">GuideAI Assistant</span>
                    </div>
                </div>
                <div class="message-content">
                    <div class="typing-animation">
                        <span></span><span></span><span></span>
                    </div>
                    <div class="typing-text">Processing your question...</div>
                </div>
            </div>
        `;
        this.chatMessages.appendChild(typingDiv);
        this.scrollToMessage(typingDiv);
    }

    hideEnhancedTypingIndicator() {
        const typingIndicator = document.getElementById('typingIndicator');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }

    setupMessageActions(messageDiv) {
        const copyBtn = messageDiv.querySelector('.copy-btn');
        const readBtn = messageDiv.querySelector('.read-btn');
        
        if (copyBtn) {
            copyBtn.addEventListener('click', () => {
                this.copyMessageToClipboard(messageDiv);
            });
        }
        
        if (readBtn) {
            readBtn.addEventListener('click', () => {
                this.readMessageAloud(messageDiv);
            });
        }
    }

    copyMessageToClipboard(messageDiv) {
        const content = messageDiv.querySelector('.message-content').textContent;
        navigator.clipboard.writeText(content).then(() => {
            this.showToast('Message copied to clipboard', 'success');
        }).catch(() => {
            this.showToast('Failed to copy message', 'error');
        });
    }

    readMessageAloud(messageDiv) {
        const content = messageDiv.querySelector('.message-content').textContent;
        if ('speechSynthesis' in window) {
            const utterance = new SpeechSynthesisUtterance(content);
            utterance.voice = this.getPreferredVoice();
            utterance.rate = this.userPreferences.ttsSpeed;
            speechSynthesis.speak(utterance);
            this.showToast('Reading message aloud', 'info');
        } else {
            this.showToast('Text-to-speech not supported', 'error');
        }
    }

    getPreferredVoice() {
        if ('speechSynthesis' in window) {
            const voices = speechSynthesis.getVoices();
            const preferredVoice = voices.find(voice => 
                voice.name.toLowerCase().includes(this.userPreferences.ttsVoice.toLowerCase())
            );
            return preferredVoice || voices[0];
        }
        return null;
    }

    showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <div class="toast-content">
                <i class="fas fa-${this.getToastIcon(type)} me-2"></i>
                ${message}
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // Animate in
        setTimeout(() => toast.classList.add('show'), 100);
        
        // Remove after 3 seconds
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                if (document.body.contains(toast)) {
                    document.body.removeChild(toast);
                }
            }, 300);
        }, 3000);
    }

    getToastIcon(type) {
        const icons = {
            success: 'check-circle',
            error: 'exclamation-triangle',
            warning: 'exclamation-circle',
            info: 'info-circle'
        };
        return icons[type] || 'info-circle';
    }

    // Utility methods
    generateConversationId() {
        return 'conv_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }

    generateCacheKey(message, context) {
        // Use Unicode-safe encoding instead of btoa
        const data = message + JSON.stringify(context);
        return btoa(unescape(encodeURIComponent(data))).substring(0, 50);
    }

    delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    buildEnhancedContext(message, location) {
        return {
            message: message,
            location: location,
            language: this.userPreferences.language,
            conversationId: this.currentConversationId,
            chatHistory: this.chatHistory.slice(-5), // Last 5 messages
            userPreferences: this.userPreferences,
            timestamp: new Date().toISOString()
        };
    }

    getEnhancedErrorMessage(error) {
        const errorMessages = {
            '503': 'The GuideAI service is temporarily unavailable. Please try again in a few minutes.',
            'timeout': 'The request took too long. Please try again.',
            'network': 'Network connection issue. Please check your internet connection and try again.',
            'API key not configured': 'The AI service is not properly configured. Please contact support for assistance.',
            'AI services temporarily unavailable': 'All AI services are currently unavailable. Please try again later.',
            'Claude API failed': 'Claude service is temporarily unavailable. Using fallback service.',
            'Gemini API failed': 'Gemini enhancement service is temporarily unavailable.'
        };

        for (const [key, message] of Object.entries(errorMessages)) {
            if (error.message.includes(key)) {
                return message;
            }
        }

        return 'An unexpected error occurred. Please try again.';
    }

    // Content processing methods
    simplifyContent(content) {
        // Remove complex formatting, keep essential information
        return content
            .replace(/## .*?\n/g, '') // Remove headers
            .replace(/\*\*(.*?)\*\*/g, '$1') // Remove bold
            .replace(/\*(.*?)\*/g, '$1') // Remove italic
            .replace(/`(.*?)`/g, '$1') // Remove code
            .replace(/\[([^\]]+)\]\([^)]+\)/g, '$1') // Remove links
            .replace(/\n\n/g, '\n') // Reduce line breaks
            .trim();
    }

    makeFormal(content) {
        // Make content more formal and professional
        return content
            .replace(/\bI'm\b/g, 'I am')
            .replace(/\bI'll\b/g, 'I will')
            .replace(/\bI've\b/g, 'I have')
            .replace(/\bdon't\b/g, 'do not')
            .replace(/\bcan't\b/g, 'cannot')
            .replace(/\bwon't\b/g, 'will not')
            .replace(/\bIt's\b/g, 'It is')
            .replace(/\bThat's\b/g, 'That is');
    }

    makeConversational(content) {
        // Make content more conversational and friendly
        return content
            .replace(/\bIt is\b/g, "It's")
            .replace(/\bThat is\b/g, "That's")
            .replace(/\bI am\b/g, "I'm")
            .replace(/\bdo not\b/g, "don't")
            .replace(/\bcannot\b/g, "can't")
            .replace(/\bwill not\b/g, "won't");
    }

    extractResources(content) {
        const resources = [];
        const linkRegex = /\[([^\]]+)\]\(([^)]+)\)/g;
        let match;

        while ((match = linkRegex.exec(content)) !== null) {
            resources.push({
                title: match[1],
                url: match[2],
                type: 'link'
            });
        }

        // Extract phone numbers
        const phoneRegex = /(\d{3}-\d{3}-\d{4})/g;
        while ((match = phoneRegex.exec(content)) !== null) {
            resources.push({
                title: 'Phone Number',
                url: `tel:${match[1]}`,
                type: 'phone'
            });
        }

        return resources;
    }

    addResourcePanel(resources, messageDiv) {
        if (!resources || resources.length === 0) return;

        const resourcePanel = document.createElement('div');
        resourcePanel.className = 'resource-panel mt-3 p-3 bg-light rounded border';
        resourcePanel.innerHTML = `
            <h6 class="text-primary mb-2">
                <i class="fas fa-link me-2"></i>Resources
            </h6>
            <div class="resource-list">
                ${resources.map(resource => `
                    <div class="resource-item mb-2">
                        <a href="${resource.url}" target="_blank" rel="noopener noreferrer" 
                           class="text-decoration-none d-flex align-items-center">
                            <i class="fas fa-${resource.type === 'phone' ? 'phone' : 'external-link-alt'} text-primary me-2"></i>
                            <span>${resource.title}</span>
                        </a>
                    </div>
                `).join('')}
            </div>
        `;

        messageDiv.appendChild(resourcePanel);
    }

    // Enhanced accessibility methods
    initializeAccessibility() {
        // Check for screen reader
        this.accessibilityFeatures.screenReader = this.detectScreenReader();
        
        // Setup live regions
        if (this.accessibilityFeatures.liveRegions) {
            this.setupLiveRegions();
        }
        
        // Setup keyboard navigation
        if (this.accessibilityFeatures.keyboardNavigation) {
            this.setupKeyboardNavigation();
        }
        
        // Setup focus management
        if (this.accessibilityFeatures.focusManagement) {
            this.setupFocusManagement();
        }
    }

    detectScreenReader() {
        // Simple screen reader detection
        return window.matchMedia('(prefers-reduced-motion: reduce)').matches ||
               document.querySelector('[aria-live]') !== null;
    }

    setupLiveRegions() {
        // Create live regions for announcements
        const liveRegion = document.createElement('div');
        liveRegion.setAttribute('aria-live', 'polite');
        liveRegion.setAttribute('aria-atomic', 'true');
        liveRegion.className = 'sr-only';
        liveRegion.id = 'live-region';
        document.body.appendChild(liveRegion);
    }

    setupKeyboardNavigation() {
        // Enhanced keyboard navigation
        document.addEventListener('keydown', (e) => {
            // Escape key to close modals or clear input
            if (e.key === 'Escape') {
                this.handleEscapeKey();
            }
            
            // Ctrl/Cmd + Enter to submit
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                e.preventDefault();
                this.handleEnhancedSubmit();
            }
        });
    }

    setupFocusManagement() {
        // Manage focus for better accessibility
        this.chatMessages.addEventListener('focusin', (e) => {
            if (e.target.closest('.chat-message')) {
                e.target.closest('.chat-message').setAttribute('tabindex', '0');
            }
        });
    }

    handleEscapeKey() {
        // Close any open modals or clear input
        if (this.userInput && this.userInput === document.activeElement) {
            this.userInput.value = '';
            this.updateCharCount();
        }
    }

    announceToScreenReader(message) {
        if (this.accessibilityFeatures.screenReader) {
            const liveRegion = document.getElementById('live-region');
            if (liveRegion) {
                liveRegion.textContent = message;
                setTimeout(() => {
                    liveRegion.textContent = '';
                }, 1000);
            }
        }
    }

    // Enhanced voice input
    async startEnhancedVoiceInput() {
        console.log('🎤 Starting voice input...');
        
        if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
            this.showToast('Speech recognition not supported in this browser', 'error');
            return;
        }

        try {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            const recognition = new SpeechRecognition();

            recognition.continuous = false;
            recognition.interimResults = true;
            recognition.lang = this.userPreferences.language === 'es' ? 'es-ES' : 'en-US';

            // Enhanced visual feedback
            this.voiceBtn.innerHTML = '<i class="fas fa-stop" aria-hidden="true"></i>';
            this.voiceBtn.classList.add('listening');
            this.voiceBtn.classList.remove('recording');

            let interimTranscript = '';
            let finalTranscript = '';

            recognition.onstart = () => {
                console.log('🎤 Enhanced speech recognition started');
                this.announceToScreenReader('Listening for voice input');
                this.showToast('Listening... Speak now', 'info');
            };

            recognition.onresult = (event) => {
                interimTranscript = '';
                finalTranscript = '';

                for (let i = event.resultIndex; i < event.results.length; i++) {
                    const transcript = event.results[i][0].transcript;
                    if (event.results[i].isFinal) {
                        finalTranscript += transcript;
                    } else {
                        interimTranscript += transcript;
                    }
                }

                // Show interim results
                if (this.userInput) {
                    this.userInput.value = finalTranscript + interimTranscript;
                    this.updateCharCount();
                }
            };

            recognition.onend = () => {
                console.log('🎤 Enhanced speech recognition ended');
                this.voiceBtn.innerHTML = '<i class="fas fa-microphone" aria-hidden="true"></i>';
                this.voiceBtn.classList.remove('listening');
                this.voiceBtn.classList.remove('recording');
                
                if (finalTranscript) {
                    this.showToast(`Recognized: ${finalTranscript}`, 'success');
                    this.announceToScreenReader(`Recognized: ${finalTranscript}`);
                }
            };

            recognition.onerror = (event) => {
                console.error('❌ Enhanced speech recognition error:', event.error);
                this.voiceBtn.innerHTML = '<i class="fas fa-microphone" aria-hidden="true"></i>';
                this.voiceBtn.classList.remove('listening');
                this.voiceBtn.classList.remove('recording');
                
                const errorMessage = this.getVoiceErrorMessage(event.error);
                this.showToast(errorMessage, 'error');
                this.announceToScreenReader(errorMessage);
            };

            recognition.start();
            console.log('✅ Enhanced voice input started successfully');
            
        } catch (error) {
            console.error('❌ Error starting enhanced voice input:', error);
            this.voiceBtn.innerHTML = '<i class="fas fa-microphone" aria-hidden="true"></i>';
            this.voiceBtn.classList.remove('listening');
            this.voiceBtn.classList.remove('recording');
            this.showToast('Error starting voice input', 'error');
        }
    }

    getVoiceErrorMessage(error) {
        const errorMessages = {
            'no-speech': 'No speech detected. Please try again.',
            'audio-capture': 'Microphone not found or access denied.',
            'not-allowed': 'Microphone access denied. Please allow microphone access.',
            'network': 'Network error occurred. Please check your connection.',
            'aborted': 'Voice input was cancelled.',
            'service-not-allowed': 'Voice service not allowed.',
            'bad-grammar': 'Speech recognition grammar error.',
            'language-not-supported': 'Language not supported for voice input.'
        };
        return errorMessages[error] || `Speech recognition error: ${error}`;
    }

    // Enhanced chat management
    clearChatWithConfirmation() {
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Clear Conversation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to clear the entire conversation? This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmClear">Clear Conversation</button>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        
        const bootstrapModal = new bootstrap.Modal(modal);
        bootstrapModal.show();

        document.getElementById('confirmClear').addEventListener('click', () => {
            this.clearChat();
            bootstrapModal.hide();
            modal.remove();
        });

        modal.addEventListener('hidden.bs.modal', () => {
            modal.remove();
        });
    }

    clearChat() {
        // Keep only the welcome message
        const welcomeMessage = this.chatMessages.querySelector('.chat-message.bot .alert');
        this.chatMessages.innerHTML = '';
        if (welcomeMessage) {
            this.chatMessages.appendChild(welcomeMessage.parentElement);
        }
        
        this.chatHistory = [];
        this.currentConversationId = this.generateConversationId();
        this.responseCache.clear();
        
        this.announceToScreenReader('Chat conversation cleared');
        this.showToast('Conversation cleared', 'success');
    }

    printEnhancedChat() {
        console.log('🖨️ Starting print enhanced chat...');
        
        if (!this.chatMessages) {
            console.error('❌ Chat messages container not found');
            this.showToast('Cannot print: chat container not found', 'error');
            return;
        }

        const printWindow = window.open('', '_blank');
        if (!printWindow) {
            console.error('❌ Failed to open print window');
            this.showToast('Cannot open print window. Please check popup blockers.', 'error');
            return;
        }
        
        let printContent = `
            <!DOCTYPE html>
            <html lang="${document.documentElement.lang || 'en'}">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>GuideAI Chat History - ${new Date().toLocaleDateString()}</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
                    .header { border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 10px; }
                    .chat-message { margin-bottom: 15px; padding: 10px; border-radius: 8px; }
                    .chat-message.user { background-color: #e1bee7; margin-left: 20%; }
                    .chat-message.bot { background-color: #ede7f6; margin-right: 20%; }
                    .message-time { font-size: 0.8em; color: #666; margin-top: 5px; }
                    @media print { body { margin: 0; } }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>GuideAI Chat History</h1>
                    <p>Generated on: ${new Date().toLocaleString()}</p>
                </div>
                <div class="chat-content">
        `;

        // Add each message
        const messages = this.chatMessages.querySelectorAll('.chat-message');
        console.log(`🖨️ Found ${messages.length} messages to print`);
        
        messages.forEach(message => {
            const isUser = message.classList.contains('user');
            const content = message.querySelector('.message-content');
            const time = message.querySelector('.message-time');
            
            if (content) {
                printContent += `
                    <div class="chat-message ${isUser ? 'user' : 'bot'}">
                        <strong>${isUser ? 'You' : 'GuideAI'}:</strong>
                        ${content.innerHTML}
                        ${time ? `<div class="message-time">${time.textContent}</div>` : ''}
                    </div>
                `;
            }
        });

        printContent += `
                </div>
            </body>
            </html>
        `;

        printWindow.document.write(printContent);
        printWindow.document.close();
        printWindow.focus();
        
        // Trigger print dialog
        setTimeout(() => {
            printWindow.print();
        }, 500);
        
        console.log('✅ Print dialog opened');
    }

    // Enhanced scrolling and navigation
    scrollToMessage(messageDiv) {
        if (!messageDiv) return;
        
        const containerHeight = this.chatMessages.offsetHeight;
        const messageTop = messageDiv.offsetTop;
        const messageHeight = messageDiv.offsetHeight;
        
        // Scroll to show the message with some padding
        const scrollPosition = messageTop - containerHeight + messageHeight + 20;
        this.chatMessages.scrollTop = Math.max(0, scrollPosition);
        
        // Additional scroll attempts for reliability
        setTimeout(() => {
            this.chatMessages.scrollTop = Math.max(0, scrollPosition);
        }, 100);
    }

    // Enhanced prompt sidebar
    setupEnhancedPromptSidebar() {
        console.log('🔧 Setting up enhanced prompt sidebar...');
        
        // Wait a bit for DOM to be fully ready
        setTimeout(() => {
            const promptItems = document.querySelectorAll('#promptList .list-group-item-action');
            console.log(`📝 Found ${promptItems.length} prompt items`);
            
            if (promptItems.length === 0) {
                console.warn('⚠️ No prompt items found, retrying...');
                // Try alternative selectors
                const altPromptItems = document.querySelectorAll('.list-group-item-action[data-prompt]');
                console.log(`📝 Found ${altPromptItems.length} alternative prompt items`);
                
                if (altPromptItems.length > 0) {
                    this.setupPromptItemListeners(altPromptItems);
                }
            } else {
                this.setupPromptItemListeners(promptItems);
            }
        }, 100);
        
        console.log('✅ Enhanced prompt sidebar setup completed');
    }

    setupPromptItemListeners(promptItems) {
        promptItems.forEach((item, index) => {
            console.log(`📝 Setting up prompt item ${index + 1}:`, item.textContent.trim());
            
            // Remove any existing listeners
            const newItem = item.cloneNode(true);
            item.parentNode.replaceChild(newItem, item);
            
            newItem.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                // Get prompt text from data attribute or text content
                const prompt = newItem.getAttribute('data-prompt') || newItem.textContent.trim();
                console.log('🎯 Prompt selected:', prompt);
                
                // Set the prompt in the input field
                if (this.userInput) {
                    this.userInput.value = prompt;
                    this.updateCharCount();
                    this.userInput.focus();
                    this.announceToScreenReader(`Selected prompt: ${prompt}`);
                } else {
                    console.error('❌ User input not found');
                }
            });
            
            newItem.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    newItem.click();
                }
            });
            
            newItem.addEventListener('mouseenter', () => {
                newItem.style.cursor = 'pointer';
            });
        });
    }

    // Enhanced keyboard shortcuts
    setupKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + K to focus input
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                this.userInput.focus();
            }
            
            // Ctrl/Cmd + L to clear chat
            if ((e.ctrlKey || e.metaKey) && e.key === 'l') {
                e.preventDefault();
                this.clearChatWithConfirmation();
            }
            
            // Ctrl/Cmd + P to print
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                this.printEnhancedChat();
            }
        });
    }

    // Enhanced preferences management
    loadUserPreferences() {
        try {
            const saved = localStorage.getItem('guideai_preferences');
            if (saved) {
                const preferences = JSON.parse(saved);
                this.userPreferences = { ...this.userPreferences, ...preferences };
            }
        } catch (error) {
            console.warn('Failed to load user preferences:', error);
        }
    }

    saveUserPreferences() {
        try {
            localStorage.setItem('guideai_preferences', JSON.stringify(this.userPreferences));
        } catch (error) {
            console.warn('Failed to save user preferences:', error);
        }
    }

    updateUserPreferences(newPreferences) {
        this.userPreferences = { ...this.userPreferences, ...newPreferences };
        this.saveUserPreferences();
        this.applyUserPreferencesToUI();
    }

    applyUserPreferencesToUI() {
        // Apply visual preferences
        document.body.classList.toggle('high-contrast', this.userPreferences.highContrast);
        document.body.classList.toggle('large-fonts', this.userPreferences.largeFonts);
        document.body.classList.toggle('dyslexia-font', this.userPreferences.dyslexiaFont);
        
        // Apply animation preferences
        if (!this.userPreferences.animations) {
            document.body.style.setProperty('--animation-duration', '0s');
        }
    }

    // Enhanced endpoint testing
    async testAllEndpoints() {
        console.log('🔍 Testing all AI endpoints...');
        
        const testPromises = this.apiEndpoints.map(async (endpoint, index) => {
            const endpointName = `endpoint_${index + 1}`;
            try {
                const response = await this.callAPI(endpoint, { action: 'test' });
                this.endpointHealth[endpoint].healthy = response.success;
                this.endpointHealth[endpoint].lastUsed = Date.now();
                this.endpointHealth[endpoint].lastError = response.success ? null : (response.error || 'Unknown error');
                
                if (response.success) {
                    console.log(`✅ ${endpointName} endpoint healthy`);
                    return { name: endpointName, healthy: true };
                } else {
                    console.log(`❌ ${endpointName} endpoint unhealthy: ${response.error || 'Unknown error'}`);
                    return { name: endpointName, healthy: false };
                }
            } catch (error) {
                this.endpointHealth[endpoint].healthy = false;
                this.endpointHealth[endpoint].lastError = error.message;
                console.log(`❌ ${endpointName} endpoint unhealthy: ${error.message}`);
                return { name: endpointName, healthy: false };
            }
        });

        const results = await Promise.allSettled(testPromises);
        const healthyEndpoints = results.filter(r => r.status === 'fulfilled' && r.value.healthy);
        
        console.log(`📊 Endpoint health: ${healthyEndpoints.length}/${this.apiEndpoints.length} healthy`);
        
        if (healthyEndpoints.length === 0) {
            this.showToast('All AI services are currently unavailable', 'error');
        }
    }

    // Enhanced API calling with better error handling
    async callAPI(endpoint, data) {
        console.log('GuideAI callAPI - Making request to:', endpoint);
        
        try {
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 30000);
            
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data),
                signal: controller.signal
            });

            clearTimeout(timeoutId);
            console.log('GuideAI callAPI - Response status:', response.status);

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const result = await response.json();
            return result;

        } catch (error) {
            console.error('GuideAI callAPI - Error:', error);
            if (error.name === 'AbortError') {
                return {
                    success: false,
                    error: 'Request timed out. Please try again.'
                };
            }
            return {
                success: false,
                error: error.message
            };
        }
    }



    // Enhanced character count with better feedback
    updateCharCount() {
        if (!this.charCount || !this.userInput) return;
        
        const count = this.userInput.value.length;
        const maxLength = 500;
        this.charCount.textContent = `${count}/${maxLength}`;
        
        // Visual feedback for character limit
        if (count > maxLength * 0.9) {
            this.charCount.className = 'text-danger fw-bold';
        } else if (count > maxLength * 0.8) {
            this.charCount.className = 'text-warning';
        } else {
            this.charCount.className = 'text-muted';
        }
        
        // Disable submit if over limit
        const submitBtn = this.chatForm?.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = count > maxLength;
        }
    }

    // Debounced search for better performance
    debounceSearch() {
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => {
            // Implement search functionality if needed
        }, 300);
    }

    // Enhanced chat history management
    addToChatHistory(response) {
        this.chatHistory.push({
            role: 'assistant',
            content: response.content,
            resources: response.resources,
            metadata: response.metadata,
            timestamp: new Date().toISOString(),
            conversationId: this.currentConversationId
        });
        
        // Limit history size for performance
        if (this.chatHistory.length > 100) {
            this.chatHistory = this.chatHistory.slice(-50);
        }
    }

    // Enhanced welcome message visibility
    ensureWelcomeMessageVisible() {
        const welcomeMessage = this.chatMessages.querySelector('.chat-message.bot .alert');
        if (welcomeMessage) {
            const messageTop = welcomeMessage.offsetTop;
            const containerHeight = this.chatMessages.offsetHeight;
            this.chatMessages.scrollTop = Math.max(0, messageTop - 20);
            console.log('✅ Welcome message positioned for visibility');
        } else {
            this.scrollToBottom();
        }
    }

    // Legacy method compatibility
    scrollToBottom() {
        this.chatMessages.scrollTop = this.chatMessages.scrollHeight;
    }

    // Enhanced time formatting
    getCurrentTime() {
        return new Date().toLocaleTimeString([], { 
            hour: '2-digit', 
            minute: '2-digit',
            hour12: true 
        });
    }

    // Enhanced HTML escaping
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Enhanced progressive display setup
    setupProgressiveDisplay() {
        // Check user preference for reduced motion
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            this.progressiveDisplay.enabled = false;
        }
    }

    // Enhanced error handling
    getErrorMessage(error) {
        return this.getEnhancedErrorMessage(error);
    }

    // Legacy method compatibility
    handleSubmit() {
        return this.handleEnhancedSubmit();
    }

    addMessage(content, type) {
        return this.addEnhancedMessage(content, type);
    }

    showTypingIndicator() {
        return this.showEnhancedTypingIndicator();
    }

    hideTypingIndicator() {
        return this.hideEnhancedTypingIndicator();
    }

    startVoiceInput() {
        return this.startEnhancedVoiceInput();
    }

    clearChat() {
        return this.clearChatWithConfirmation();
    }

    printChat() {
        return this.printEnhancedChat();
    }

    // Enhanced location detection
    async getUserLocation() {
        return new Promise((resolve) => {
            if ('geolocation' in navigator) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        resolve({
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                            accuracy: position.coords.accuracy
                        });
                    },
                    (error) => {
                        console.warn('Location access denied:', error);
                        resolve(null);
                    },
                    { timeout: 10000, enableHighAccuracy: false }
                );
            } else {
                resolve(null);
            }
        });
    }

    // Enhanced test connection
    async testConnection() {
        return this.testAllEndpoints();
    }

    // Enhanced prompt sidebar setup
    setupPromptSidebar() {
        return this.setupEnhancedPromptSidebar();
    }

    // Enhanced accessibility integration
    setupAccessibilityIntegration() {
        return this.initializeAccessibility();
    }

    // Enhanced preferences loading
    loadPreferences() {
        return this.loadUserPreferences();
    }

    // Enhanced preferences saving
    savePreferences() {
        return this.saveUserPreferences();
    }

    // Enhanced accessibility preferences update
    updateAccessibilityPreferences(newPrefs) {
        this.userPreferences = { ...this.userPreferences, ...newPrefs };
        this.saveUserPreferences();
        this.applyUserPreferencesToUI();
    }



    // Enhanced resource links generation
    generateResourceLinks() {
        // This method is now handled by the enhanced resource panel
        return '';
    }

    // Enhanced basic response generation
    generateBasicResponse(message, language) {
        const responses = {
            en: {
                greeting: "Hello! I'm here to help you with special education questions.",
                error: "I'm sorry, I'm having trouble processing your request right now.",
                fallback: "I understand you're asking about special education. Let me help you find the information you need."
            },
            es: {
                greeting: "¡Hola! Estoy aquí para ayudarte con preguntas sobre educación especial.",
                error: "Lo siento, estoy teniendo problemas para procesar tu solicitud en este momento.",
                fallback: "Entiendo que estás preguntando sobre educación especial. Déjame ayudarte a encontrar la información que necesitas."
            }
        };

        const langResponses = responses[language] || responses.en;
        
        if (message.toLowerCase().includes('hello') || message.toLowerCase().includes('hi')) {
            return langResponses.greeting;
        } else if (message.toLowerCase().includes('error') || message.toLowerCase().includes('problem')) {
            return langResponses.error;
        } else {
            return langResponses.fallback;
        }
    }

    // ============================================
    // IMPROVED INPUT AREA FUNCTIONALITY
    // ============================================

    initializeInputArea() {
        this.initializeAutoResize();
        this.enhanceCharacterCounter();
        this.enhanceVoiceButton();
        this.enhanceFormSubmission();
        console.log('✅ Improved input area initialized');
    }

    // Auto-resize textarea functionality
    initializeAutoResize() {
        const userInput = document.getElementById('userInput');
        if (!userInput) return;

        // Auto-resize function
        const autoResize = () => {
            // Reset height to recalculate
            userInput.style.height = 'auto';
            
            // Calculate new height (max 120px)
            const newHeight = Math.min(userInput.scrollHeight, 120);
            userInput.style.height = newHeight + 'px';
        };

        // Attach event listeners
        userInput.addEventListener('input', autoResize);
        userInput.addEventListener('focus', autoResize);
        
        // Initial resize
        autoResize();
    }

    // Enhanced character counter with visual feedback
    enhanceCharacterCounter() {
        const userInput = document.getElementById('userInput');
        const charCount = document.getElementById('charCount');
        if (!userInput || !charCount) return;
        
        const updateCharacterCount = () => {
            const count = userInput.value.length;
            const maxLength = 500;
            
            // Update counter text
            charCount.textContent = `${count}/${maxLength}`;
            
            // Remove all state classes
            charCount.classList.remove('text-warning', 'text-danger');
            
            // Add appropriate class based on count
            if (count > 450) {
                charCount.classList.add('text-warning');
            } 
            if (count >= maxLength) {
                charCount.classList.add('text-danger');
            }
            
            // Update help text based on character count
            const helpText = document.querySelector('.help-text span');
            if (helpText) {
                if (count > 450 && count < maxLength) {
                    helpText.textContent = this.getTranslation('approaching_char_limit') || 'Approaching character limit';
                } else if (count >= maxLength) {
                    helpText.textContent = this.getTranslation('char_limit_reached') || 'Character limit reached';
                } else {
                    helpText.textContent = this.getTranslation('input_tip') || 'Tip: Be specific about your child\'s age, disability, or situation for better help';
                }
            }
        };

        // Attach event listener
        userInput.addEventListener('input', updateCharacterCount);
        
        // Initial update
        updateCharacterCount();
    }

    // Enhanced voice button functionality
    enhanceVoiceButton() {
        const voiceBtn = document.getElementById('voiceBtn');
        const userInput = document.getElementById('userInput');
        if (!voiceBtn || !userInput) return;

        voiceBtn.addEventListener('click', () => {
            if (voiceBtn.classList.contains('listening')) {
                // Stop listening
                if (window.recognition) {
                    window.recognition.stop();
                }
                voiceBtn.classList.remove('listening');
                userInput.disabled = false;
                userInput.placeholder = this.getTranslation('input_placeholder') || 'Ask about IEPs, accommodations, your rights, or any special education topic...';
            } else {
                // Start listening
                if (window.recognition) {
                    voiceBtn.classList.add('listening');
                    userInput.disabled = true;
                    userInput.placeholder = this.getTranslation('listening') || 'Listening...';
                    window.recognition.start();
                } else {
                    // If speech recognition not available
                    this.showToast(
                        this.getTranslation('voice_not_available') || 'Voice input is not available in your browser',
                        'warning'
                    );
                }
            }
        });
    }

    // Form submission enhancement
    enhanceFormSubmission() {
        console.log('🔧 Setting up enhanced form submission...');
        const chatForm = document.getElementById('chatForm');
        const sendBtn = document.querySelector('.send-btn');
        const userInput = document.getElementById('userInput');
        
        console.log('📝 Form elements found:', {
            chatForm: !!chatForm,
            sendBtn: !!sendBtn,
            userInput: !!userInput
        });
        
        if (!chatForm || !sendBtn || !userInput) {
            console.error('❌ Missing form elements');
            return;
        }

        // Remove existing event listeners to prevent duplicates
        const newForm = chatForm.cloneNode(true);
        chatForm.parentNode.replaceChild(newForm, chatForm);
        
        // Get new references
        const newChatForm = document.getElementById('chatForm');
        const newSendBtn = document.querySelector('.send-btn');
        const newUserInput = document.getElementById('userInput');

        console.log('📝 New form elements found:', {
            newChatForm: !!newChatForm,
            newSendBtn: !!newSendBtn,
            newUserInput: !!newUserInput
        });

        // Update the instance reference to the new input
        this.userInput = newUserInput;

        newChatForm.addEventListener('submit', (e) => {
            e.preventDefault();
            console.log('📝 Form submitted');
            
            // Validate input
            const message = newUserInput.value.trim();
            console.log('📝 Message from input:', message);
            console.log('📝 Message length:', message.length);
            
            if (!message) {
                console.warn('⚠️ Empty message detected');
                this.showToast(
                    this.getTranslation('enter_question') || 'Please enter your question',
                    'warning'
                );
                newUserInput.focus();
                return;
            }
            
            if (message.length > 500) {
                console.warn('⚠️ Message too long:', message.length);
                this.showToast(
                    this.getTranslation('question_too_long') || 'Question is too long. Please keep it under 500 characters.',
                    'warning'
                );
                return;
            }
            
            console.log('✅ Message validated, processing:', message);
            
            // Disable form during processing
            newUserInput.disabled = true;
            newSendBtn.disabled = true;
            
            // Process the message using existing enhanced submit with the message
            this.handleEnhancedSubmit(message);
            
            // Re-enable form (input clearing is handled by handleEnhancedSubmit)
            setTimeout(() => {
                newUserInput.disabled = false;
                newSendBtn.disabled = false;
                newUserInput.focus();
                console.log('✅ Form re-enabled');
            }, 100);
        });
        
        console.log('✅ Enhanced form submission setup completed');
    }

    // Helper method to get translations
    getTranslation(key) {
        // Check if language manager is available
        if (window.languageManager && typeof window.languageManager.get === 'function') {
            return window.languageManager.get(key);
        }
        
        // Fallback translations
        const translations = {
            en: {
                approaching_char_limit: 'Approaching character limit',
                char_limit_reached: 'Character limit reached',
                voice_not_available: 'Voice input is not available in your browser',
                enter_question: 'Please enter your question',
                question_too_long: 'Question is too long. Please keep it under 500 characters.',
                listening: 'Listening...',
                input_placeholder: 'IEPs, accommodations, rights...ask us anything.',
                input_tip: 'Tip: Be specific about your child\'s age, disability, or situation for better help'
            },
            es: {
                approaching_char_limit: 'Acercándose al límite de caracteres',
                char_limit_reached: 'Límite de caracteres alcanzado',
                voice_not_available: 'La entrada de voz no está disponible en tu navegador',
                enter_question: 'Por favor ingresa tu pregunta',
                question_too_long: 'La pregunta es demasiado larga. Por favor manténla bajo 500 caracteres.',
                listening: 'Escuchando...',
                input_placeholder: 'Pregunta sobre IEP, adaptaciones, tus derechos, o cualquier tema de educación especial...',
                input_tip: 'Consejo: Sé específico sobre la edad, discapacidad o situación de tu hijo para obtener mejor ayuda'
            }
        };

        const currentLang = document.documentElement.lang || 'en';
        const langTranslations = translations[currentLang] || translations.en;
        
        return langTranslations[key] || key;
    }

    // Test function to verify enhanced formatting
    testEnhancedFormatting() {
        console.log('🧪 Testing enhanced formatting...');
        
        const testContent = `## Important Information

This is a **test message** with *enhanced formatting*.

### Key Points:
- This should have checkmark icons
- Important deadlines like 12/15/2024
- Legal rights under IDEA
- Contact information: 555-123-4567

### Actions Required:
- You must complete this form
- Required documentation needed
- Critical deadline approaching

Visit [this link](https://example.com) for more information.`;
        
        console.log('🧪 Test content:', testContent);
        
        const formatted = this.formatBotResponse(testContent);
        console.log('🧪 Formatted content:', formatted);
        
        // Test the addEnhancedMessage function
        this.addEnhancedMessage(testContent, 'bot');
        
        // Add a simple test message to verify visibility
        this.addSimpleTestMessage();
        
        console.log('🧪 Enhanced formatting test completed');
    }

    // Simple test message to verify visibility
    addSimpleTestMessage() {
        console.log('🧪 Adding simple test message...');
        
        const simpleDiv = document.createElement('div');
        simpleDiv.className = 'chat-message bot test-message';
        simpleDiv.style.cssText = `
            background-color: #ffeb3b !important;
            color: #000 !important;
            padding: 10px !important;
            margin: 10px 0 !important;
            border: 2px solid #f57f17 !important;
            border-radius: 8px !important;
            font-weight: bold !important;
            z-index: 9999 !important;
            position: relative !important;
        `;
        simpleDiv.innerHTML = `
            <div style="color: #000 !important;">
                🧪 TEST MESSAGE - If you can see this, messages are being added correctly!
                <br>
                Time: ${new Date().toLocaleTimeString()}
                <br>
                Chat container children: ${this.chatMessages ? this.chatMessages.children.length : 'N/A'}
            </div>
        `;
        
        if (this.chatMessages) {
            this.chatMessages.appendChild(simpleDiv);
            console.log('🧪 Simple test message added');
            
            // Check if it's visible
            setTimeout(() => {
                const rect = simpleDiv.getBoundingClientRect();
                console.log('🧪 Test message visibility check:', {
                    offsetHeight: simpleDiv.offsetHeight,
                    offsetWidth: simpleDiv.offsetWidth,
                    getBoundingClientRect: rect,
                    isVisible: rect.height > 0 && rect.width > 0
                });
            }, 100);
        } else {
            console.error('🧪 chatMessages element not found for simple test');
        }
    }

    // Diagnostic function to check message visibility
    diagnoseMessageVisibility() {
        console.log('🔍 Diagnosing message visibility...');
        
        if (!this.chatMessages) {
            console.error('❌ chatMessages element not found');
            return;
        }
        
        // Check chat container properties
        console.log('🔍 Chat container properties:', {
            offsetHeight: this.chatMessages.offsetHeight,
            scrollHeight: this.chatMessages.scrollHeight,
            clientHeight: this.chatMessages.clientHeight,
            offsetWidth: this.chatMessages.offsetWidth,
            clientWidth: this.chatMessages.clientWidth,
            style: this.chatMessages.style.cssText,
            className: this.chatMessages.className,
            children: this.chatMessages.children.length,
            innerHTML: this.chatMessages.innerHTML.substring(0, 500) + '...'
        });
        
        // Check all child elements
        const children = this.chatMessages.children;
        console.log(`🔍 Found ${children.length} child elements:`);
        
        for (let i = 0; i < children.length; i++) {
            const child = children[i];
            const rect = child.getBoundingClientRect();
            console.log(`🔍 Child ${i + 1}:`, {
                tagName: child.tagName,
                className: child.className,
                offsetHeight: child.offsetHeight,
                offsetWidth: child.offsetWidth,
                getBoundingClientRect: rect,
                isVisible: rect.height > 0 && rect.width > 0,
                style: child.style.cssText,
                innerHTML: child.innerHTML.substring(0, 200) + '...'
            });
        }
        
        // Check if container is scrollable
        const isScrollable = this.chatMessages.scrollHeight > this.chatMessages.clientHeight;
        console.log('🔍 Container scrollable:', isScrollable);
        
        // Check scroll position
        console.log('🔍 Scroll position:', {
            scrollTop: this.chatMessages.scrollTop,
            scrollLeft: this.chatMessages.scrollLeft
        });
    }

    // Button functionality methods
    clearChatWithConfirmation() {
        if (confirm('Are you sure you want to clear the chat history? This action cannot be undone.')) {
            console.log('🧹 Clearing chat...');
            if (this.chatMessages) {
                this.chatMessages.innerHTML = '';
                this.chatHistory = [];
                console.log('✅ Chat cleared successfully');
                this.announceToScreenReader('Chat history cleared');
            }
        }
    }

    printEnhancedChat() {
        console.log('🖨️ Preparing chat for printing...');
        
        if (!this.chatMessages || this.chatMessages.children.length === 0) {
            alert('No chat messages to print.');
            return;
        }

        // Create a new window for printing
        const printWindow = window.open('', '_blank');
        if (!printWindow) {
            alert('Unable to open print window. Please check your popup blocker settings.');
            return;
        }

        // Build the print content
        let printContent = `
            <!DOCTYPE html>
            <html lang="${document.documentElement.lang || 'en'}">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>GuideAI Chat History - ${new Date().toLocaleDateString()}</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
                    .header { border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 10px; }
                    .chat-message { margin-bottom: 15px; padding: 10px; border-radius: 8px; }
                    .chat-message.user { background-color: #e1bee7; margin-left: 20%; }
                    .chat-message.bot { background-color: #ede7f6; margin-right: 20%; }
                    .message-time { font-size: 0.8em; color: #666; margin-top: 5px; }
                    @media print { body { margin: 0; } }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>GuideAI Chat History</h1>
                    <p>Generated on: ${new Date().toLocaleString()}</p>
                </div>
                <div class="chat-content">
        `;

        // Add each message
        const messages = this.chatMessages.querySelectorAll('.chat-message');
        messages.forEach(message => {
            const isUser = message.classList.contains('user');
            const content = message.querySelector('.message-content');
            const time = message.querySelector('.message-time');
            
            if (content) {
                printContent += `
                    <div class="chat-message ${isUser ? 'user' : 'bot'}">
                        <strong>${isUser ? 'You' : 'GuideAI'}:</strong>
                        ${content.innerHTML}
                        ${time ? `<div class="message-time">${time.textContent}</div>` : ''}
                    </div>
                `;
            }
        });

        printContent += `
                </div>
            </body>
            </html>
        `;

        printWindow.document.write(printContent);
        printWindow.document.close();
        printWindow.focus();
        
        // Trigger print dialog
        setTimeout(() => {
            printWindow.print();
        }, 500);
        
        console.log('✅ Print dialog opened');
    }

    startEnhancedVoiceInput() {
        console.log('🎤 Starting voice input...');
        
        if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
            alert('Voice input is not supported in your browser. Please use Chrome, Edge, or Safari.');
            return;
        }

        try {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            const recognition = new SpeechRecognition();
            
            recognition.continuous = false;
            recognition.interimResults = false;
            recognition.lang = this.userPreferences.language === 'es' ? 'es-ES' : 'en-US';

            recognition.onstart = () => {
                console.log('🎤 Voice recognition started');
                this.voiceBtn.classList.add('listening');
                this.voiceBtn.innerHTML = '<i class="fas fa-stop"></i>';
                if (this.userInput) {
                    this.userInput.placeholder = 'Listening...';
                    this.userInput.disabled = true;
                }
                this.announceToScreenReader('Voice input started. Speak now.');
            };

            recognition.onresult = (event) => {
                const transcript = event.results[0][0].transcript;
                console.log('🎤 Voice input result:', transcript);
                
                if (this.userInput) {
                    this.userInput.value = transcript;
                    this.updateCharCount();
                }
                
                this.announceToScreenReader(`Voice input captured: ${transcript}`);
            };

            recognition.onerror = (event) => {
                console.error('🎤 Voice recognition error:', event.error);
                this.resetVoiceButton();
                
                let errorMessage = 'Voice input error occurred.';
                switch (event.error) {
                    case 'no-speech':
                        errorMessage = 'No speech detected. Please try again.';
                        break;
                    case 'audio-capture':
                        errorMessage = 'Microphone access denied or unavailable.';
                        break;
                    case 'not-allowed':
                        errorMessage = 'Microphone permission denied.';
                        break;
                }
                
                alert(errorMessage);
            };

            recognition.onend = () => {
                console.log('🎤 Voice recognition ended');
                this.resetVoiceButton();
            };

            recognition.start();
            
        } catch (error) {
            console.error('🎤 Voice input initialization error:', error);
            alert('Failed to initialize voice input. Please try again.');
        }
    }

    resetVoiceButton() {
        if (this.voiceBtn) {
            this.voiceBtn.innerHTML = '<i class="fas fa-microphone" aria-hidden="true"></i>';
            this.voiceBtn.classList.remove('listening', 'recording');
            this.voiceBtn.disabled = false;
        }
    }

    // ============================================
    // LOCAL STORAGE FUNCTIONALITY
    // ============================================

    /**
     * Initialize local storage functionality
     */
    initializeLocalStorage() {
        console.log('💾 Initializing local storage...');
        
        // Load saved preferences
        this.loadUserPreferences();
        this.loadPreferences();
        
        // Load chat history if enabled
        if (this.userPreferences.saveChatHistory) {
            this.loadChatHistory();
        }
        
        console.log('✅ Local storage initialized');
    }
    
    /**
     * Save chat history to local storage
     */
    saveChatHistory() {
        if (!this.userPreferences.saveChatHistory) {
            return;
        }
        
        try {
            // Get existing history
            const existingHistory = this.getStoredChatHistory();
            
            // Find current conversation or create new one
            let currentConversation = existingHistory.find(conv => conv.conversationId === this.currentConversationId);
            
            if (!currentConversation) {
                // Create new conversation
                currentConversation = {
                    conversationId: this.currentConversationId,
                    messages: [],
                    timestamp: new Date().toISOString(),
                    version: '1.0'
                };
                existingHistory.push(currentConversation);
            }
            
            // Update current conversation with latest messages
            currentConversation.messages = this.chatHistory;
            currentConversation.lastUpdated = new Date().toISOString();
            
            // Limit history size
            if (existingHistory.length > this.userPreferences.maxHistoryItems) {
                existingHistory.splice(0, existingHistory.length - this.userPreferences.maxHistoryItems);
            }
            
            localStorage.setItem('guideai_chat_history', JSON.stringify(existingHistory));
            console.log('💾 Chat history saved:', this.chatHistory.length, 'messages in conversation', this.currentConversationId);
            
        } catch (error) {
            console.error('❌ Error saving chat history:', error);
        }
    }
    
    /**
     * Load chat history from local storage
     */
    loadChatHistory() {
        try {
            const storedHistory = this.getStoredChatHistory();
            if (storedHistory.length > 0) {
                // Load the most recent conversation
                const latestConversation = storedHistory[storedHistory.length - 1];
                this.chatHistory = latestConversation.messages || [];
                this.currentConversationId = latestConversation.conversationId || this.generateConversationId();
                
                console.log('💾 Chat history loaded:', this.chatHistory.length, 'messages');
                return true;
            }
        } catch (error) {
            console.error('❌ Error loading chat history:', error);
        }
        return false;
    }
    
    /**
     * Get stored chat history from localStorage
     */
    getStoredChatHistory() {
        try {
            const stored = localStorage.getItem('guideai_chat_history');
            return stored ? JSON.parse(stored) : [];
        } catch (error) {
            console.error('❌ Error parsing stored chat history:', error);
            return [];
        }
    }
    
    /**
     * Clear chat history from local storage
     */
    clearStoredChatHistory() {
        try {
            localStorage.removeItem('guideai_chat_history');
            this.chatHistory = [];
            this.currentConversationId = this.generateConversationId();
            console.log('💾 Chat history cleared from local storage');
        } catch (error) {
            console.error('❌ Error clearing chat history:', error);
        }
    }
    
    /**
     * Export chat history as JSON
     */
    exportChatHistory() {
        try {
            const exportData = {
                conversations: this.getStoredChatHistory(),
                exportDate: new Date().toISOString(),
                version: '1.0'
            };
            
            const blob = new Blob([JSON.stringify(exportData, null, 2)], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `guideai_chat_history_${new Date().toISOString().split('T')[0]}.json`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
            
            console.log('💾 Chat history exported');
            return true;
        } catch (error) {
            console.error('❌ Error exporting chat history:', error);
            return false;
        }
    }
    
    /**
     * Import chat history from JSON file
     */
    importChatHistory(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                try {
                    const importData = JSON.parse(e.target.result);
                    
                    if (importData.conversations && Array.isArray(importData.conversations)) {
                        localStorage.setItem('guideai_chat_history', JSON.stringify(importData.conversations));
                        this.loadChatHistory();
                        console.log('💾 Chat history imported:', importData.conversations.length, 'conversations');
                        resolve(true);
                    } else {
                        reject(new Error('Invalid import file format'));
                    }
                } catch (error) {
                    reject(error);
                }
            };
            reader.onerror = () => reject(new Error('Failed to read file'));
            reader.readAsText(file);
        });
    }
    
    /**
     * Add message to chat history and save
     */
    addToChatHistory(message) {
        const historyEntry = {
            role: message.role || 'assistant',
            content: message.content || message,
            timestamp: new Date().toISOString(),
            conversationId: this.currentConversationId
        };
        
        this.chatHistory.push(historyEntry);
        
        // Save to local storage
        if (this.userPreferences.saveChatHistory) {
            this.saveChatHistory();
        }
        
        console.log('💾 Added to chat history:', historyEntry);
    }
}

// Initialize GuideAI when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.guideAI = new GuideAI();
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = GuideAI;
}
