# Contributing to GuideAI

Thank you for your interest in contributing to GuideAI! This document provides guidelines for contributing to this project.

## 🤝 How to Contribute

### Reporting Issues
- Use the GitHub issue tracker
- Include detailed steps to reproduce the issue
- Provide system information (OS, browser, PHP version)
- Include error messages and screenshots when relevant

### Suggesting Features
- Check existing issues first
- Provide clear use cases and benefits
- Include mockups or examples when possible

### Code Contributions
1. Fork the repository
2. Create a feature branch: `git checkout -b feature/amazing-feature`
3. Make your changes
4. Test thoroughly
5. Commit with clear messages: `git commit -m "Add amazing feature"`
6. Push to your fork: `git push origin feature/amazing-feature`
7. Open a Pull Request

## 📋 Development Guidelines

### Code Style
- Follow PSR-12 coding standards for PHP
- Use meaningful variable and function names
- Add comments for complex logic
- Keep functions focused and small

### Testing
- Test all new features thoroughly
- Ensure accessibility features work
- Test on multiple browsers and devices
- Verify mobile responsiveness

### Security
- Never commit API keys or sensitive data
- Validate all user inputs
- Follow OWASP security guidelines
- Test for common vulnerabilities

### Accessibility
- Maintain WCAG 2.1 AA compliance
- Test with screen readers
- Ensure keyboard navigation works
- Verify color contrast ratios

## 🛠️ Development Setup

### Prerequisites
- PHP 8.0 or higher
- Composer
- Web server (Apache/Nginx) or PHP built-in server
- Modern web browser

### Local Development
1. Clone the repository
2. Run `composer install`
3. Copy `config.example.php` to `config.php`
4. Add your API keys to `config.php`
5. Start the development server

### Testing
- Test all AI integrations
- Verify accessibility features
- Check mobile responsiveness
- Test voice input/output functionality

## 📝 Commit Message Guidelines

Use clear, descriptive commit messages:
- Use present tense ("Add feature" not "Added feature")
- Use imperative mood ("Move cursor to..." not "Moves cursor to...")
- Limit the first line to 72 characters
- Reference issues and pull requests after the first line

Example:
```
Add voice input accessibility feature

- Implement voice recognition for hands-free operation
- Add visual feedback during recording
- Support multiple voice input languages
- Fixes #123
```

## 🔒 Security

- Never commit API keys or sensitive configuration
- Report security vulnerabilities privately
- Follow responsible disclosure practices
- Keep dependencies updated

## 📞 Getting Help

- Check existing issues and documentation
- Join our community discussions
- Contact maintainers for urgent issues

Thank you for contributing to making GuideAI better for special education families! 🌟 