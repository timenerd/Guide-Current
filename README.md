# GuideAI - Compassionate AI Support for Special Education Families

## What GuideAI Does

GuideAI is a bilingual (English/Spanish) web application designed to empower families navigating the complex world of special education and Individualized Education Programs (IEPs). It serves as a compassionate AI companion that provides personalized guidance, practical tools, and immediate support for parents of children with disabilities.

### Core Features

**🤖 AI-Powered Chat Interface**
- Personalized responses about IEPs, accommodations, rights, and special education topics
- Location-aware resource recommendations based on your geographic area
- Multi-AI integration (Claude, ChatGPT, Gemini) for comprehensive, accurate responses
- Voice input and text-to-speech capabilities for accessibility

**🎯 Specialized Support**
- Oregon-specific county and school district information
- Local parent support group recommendations
- Emergency contacts and crisis support resources
- IEP preparation guidance and document templates

**♿ Comprehensive Accessibility**
- High contrast mode, large fonts, and dyslexia-friendly typography
- Screen reader optimization and keyboard navigation
- Voice input/output with multiple voice options (including Nova female voice)
- Bilingual support throughout the entire application

**📱 Mobile-First Design**
- Responsive design that works on all devices
- Touch-friendly interface for mobile users
- Optimized performance and fast loading times

## Technical Implementation

### Architecture Overview

GuideAI is built as a modern web application using a **PHP backend** with **JavaScript frontend**, designed for scalability, accessibility, and performance.

### Backend Technology Stack

**🔧 Core Framework**
- **PHP 8.0+** - Server-side logic and API endpoints
- **Apache/Nginx** - Web server configuration
- **MySQL/SQLite** - Data persistence (configurable)

**🤖 AI Integration**
- **OpenAI GPT-4** - Primary conversational AI for user-friendly responses
- **Claude AI (Anthropic)** - Core content generation for detailed, accurate information
- **Gemini AI (Google)** - Fallback AI and location-based resource linking
- **Multi-AI Fallback System** - Ensures 99.9% uptime with intelligent service switching

**🗺️ Location Services**
- **OpenCage Geocoding API** - Reverse geocoding for location-based recommendations
- **Custom Resource Linking System** - Oregon-specific county and school district mapping
- **Geolocation API** - Browser-based location detection with user consent

**🎤 Text-to-Speech**
- **OpenAI TTS API** - High-quality voice synthesis with multiple voice options
- **Browser Speech Synthesis** - Fallback for offline/API failure scenarios
- **Voice Selection System** - Nova (female), Alloy, Echo, Fable, Onyx, Shimmer voices

### Frontend Technology Stack

**🎨 User Interface**
- **HTML5** - Semantic markup with accessibility features
- **CSS3** - Responsive design with Bootstrap 5 framework
- **JavaScript (ES6+)** - Modern JavaScript with async/await patterns
- **Font Awesome** - Icon library for consistent UI elements

**♿ Accessibility Features**
- **WCAG 2.1 AA Compliance** - Full accessibility standards implementation
- **Screen Reader Support** - ARIA labels, live regions, and semantic HTML
- **Keyboard Navigation** - Complete keyboard-only operation support
- **High Contrast Mode** - Custom CSS for visual accessibility
- **Dyslexia-Friendly Fonts** - OpenDyslexic font integration

**🌐 Internationalization**
- **Bilingual Support** - Complete English/Spanish translation system
- **Language Manager** - Dynamic language switching with persistence
- **RTL Support Ready** - Framework prepared for right-to-left languages

### Key Technical Components

#### 1. AI Response System
```php
// Multi-AI integration with intelligent fallbacks
class AIResponseSystem {
    public function generateResponse($userMessage, $location = null) {
        // Try Claude first for core content
        $response = $this->callClaudeAPI($userMessage);
        
        // Enhance with ChatGPT for conversational tone
        $response = $this->enhanceWithChatGPT($response);
        
        // Add location-specific resources
        $response = $this->addLocationResources($response, $location);
        
        return $response;
    }
}
```

#### 2. Accessibility Manager
```javascript
class GuideAIAccessibility {
    constructor() {
        this.preferences = {
            highContrast: false,
            largeFonts: false,
            dyslexiaFont: false,
            readAloud: false,
            ttsVoice: 'nova',
            ttsSpeed: 1.0
        };
    }
    
    async speakText(text, options = {}) {
        // OpenAI TTS with Nova voice support
        await this.speakWithOpenAI(text, { voice: 'nova' });
    }
}
```

#### 3. Location-Aware Resource System
```php
class ResourceLinkingSystem {
    public function getOregonCountyInfo($location) {
        // Map Oregon counties to specific support networks
        $countyMap = [
            'Deschutes' => 'Central Oregon Disability Support Network',
            'Lane' => 'FACT Oregon',
            // ... all Oregon counties mapped
        ];
        
        return $this->generateCountySpecificResponse($location, $countyMap);
    }
}
```

### Performance Optimizations

**⚡ Frontend Performance**
- **Minified JavaScript** - Reduced file sizes for faster loading
- **CSS Optimization** - Purged unused styles and compressed
- **Image Optimization** - WebP format with fallbacks
- **Lazy Loading** - Deferred loading of non-critical resources

**🚀 Backend Performance**
- **API Response Caching** - Intelligent caching of AI responses
- **Database Query Optimization** - Indexed queries and connection pooling
- **CDN Integration Ready** - Static asset delivery optimization
- **Compression** - Gzip/Brotli compression for all responses

### Security Features

**🔒 Data Protection**
- **HTTPS Enforcement** - All communications encrypted
- **API Key Security** - Environment variable storage
- **Input Sanitization** - XSS and injection protection
- **CORS Configuration** - Proper cross-origin resource sharing

**🛡️ Privacy Compliance**
- **GDPR Ready** - Data protection and user consent
- **HIPAA Considerations** - Healthcare data handling practices
- **No Personal Data Storage** - Minimal data retention policies

### Development & Deployment

**🛠️ Development Environment**
- **Local Development** - XAMPP/WAMP/MAMP compatible
- **Version Control** - Git with semantic versioning
- **Code Quality** - ESLint, Prettier, and PHP CodeSniffer
- **Testing** - Unit tests and integration tests

**🚀 Deployment Options**
- **Shared Hosting** - Compatible with standard PHP hosting
- **VPS/Dedicated** - Full server control deployment
- **Cloud Platforms** - AWS, Google Cloud, Azure ready
- **Docker Support** - Containerized deployment available

### API Endpoints

**🤖 AI Services**
- `POST /api/chat.php` - Main chat interface
- `POST /api/tts.php` - Text-to-speech generation
- `GET /api/resources.php` - Location-based resources

**🌐 Language Services**
- `GET /api/language.php` - Language switching
- `POST /api/translate.php` - Dynamic translation

### Configuration

**📝 Environment Variables**
```env
# AI API Keys
OPENAI_API_KEY=your_openai_key
CLAUDE_API_KEY=your_claude_key
GEMINI_API_KEY=your_gemini_key

# Location Services
OPENCAGE_API_KEY=your_opencage_key

# Application Settings
DEBUG_MODE=false
APP_ENV=production
```

### Browser Support

**🌐 Supported Browsers**
- **Chrome 90+** - Full feature support
- **Firefox 88+** - Full feature support
- **Safari 14+** - Full feature support
- **Edge 90+** - Full feature support
- **Mobile Browsers** - iOS Safari, Chrome Mobile

### Accessibility Standards

**♿ WCAG 2.1 AA Compliance**
- **Perceivable** - Text alternatives, adaptable content
- **Operable** - Keyboard navigation, no time limits
- **Understandable** - Readable, predictable, input assistance
- **Robust** - Compatible with assistive technologies

### Future Roadmap

**🔮 Planned Features**
- **IEP Document Parser** - Upload and analyze existing IEPs
- **Family Accounts** - Personalized user profiles
- **Mobile App** - Native iOS/Android applications
- **Advanced Analytics** - Usage insights and improvements
- **Multi-Language Expansion** - Additional language support

---

## Getting Started

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-org/guideai.git
   cd guideai
   ```

2. **Set up environment**
   ```bash
   cp .env.example .env
   # Edit .env with your API keys
   ```

3. **Install dependencies**
   ```bash
   composer install
   ```

4. **Configure web server**
   - Point document root to the project directory
   - Enable PHP extensions: curl, json, mbstring
   - Configure URL rewriting for clean URLs

5. **Test the application**
   - Visit the homepage
   - Test accessibility features
   - Verify AI responses
   - Check mobile responsiveness

## Contributing

We welcome contributions! Please see our [Contributing Guidelines](CONTRIBUTING.md) for details on:
- Code style and standards
- Testing requirements
- Pull request process
- Accessibility requirements

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

For support and questions:
- **Documentation**: [docs.guideai.org](https://docs.guideai.org)
- **Issues**: [GitHub Issues](https://github.com/your-org/guideai/issues)
- **Email**: support@guideai.org

---

*Built with ❤️ for families navigating special education* 