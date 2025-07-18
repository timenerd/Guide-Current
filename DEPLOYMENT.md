# GuideAI Production Deployment Guide

## 🚀 Production Setup Checklist

### 1. **Security Configuration**

#### API Keys Setup
```bash
# Create .env file (NEVER commit this to version control)
cp config.example.php config.php
```

Edit `config.php` with your actual API keys:
```php
// Use environment variables or direct assignment
define('CLAUDE_API_KEY', 'your_actual_claude_key');
define('GEMINI_API_KEY', 'your_actual_gemini_key');
define('OPENAI_API_KEY', 'your_actual_openai_key');
define('OPENCAGE_API_KEY', 'your_actual_opencage_key');
```

#### Alternative: Environment Variables
Create a `.env` file:
```env
CLAUDE_API_KEY=your_actual_claude_key
GEMINI_API_KEY=your_actual_gemini_key
OPENAI_API_KEY=your_actual_openai_key
OPENCAGE_API_KEY=your_actual_opencage_key
```

### 2. **Server Requirements**

#### PHP Configuration
- **PHP Version**: 8.0 or higher
- **Extensions**: mbstring, xml, ctype, iconv, intl, pdo_sqlite
- **Memory Limit**: 256MB minimum
- **Max Execution Time**: 60 seconds
- **Upload Max Filesize**: 10MB

#### Web Server Configuration
```apache
# Apache .htaccess (already included)
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Security headers
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
```

### 3. **Directory Permissions**

```bash
# Set proper permissions
chmod 755 /path/to/guideai
chmod 644 /path/to/guideai/*.php
chmod 644 /path/to/guideai/assets/css/*
chmod 644 /path/to/guideai/assets/js/*
chmod 755 /path/to/guideai/logs
chmod 755 /path/to/guideai/cache
chmod 755 /path/to/guideai/uploads
chmod 600 /path/to/guideai/config.php
```

### 4. **SSL Certificate**

Ensure HTTPS is properly configured:
- Valid SSL certificate installed
- HTTP to HTTPS redirect enabled
- HSTS headers configured

### 5. **Performance Optimization**

#### Enable Caching
- **Browser Caching**: Configure for static assets
- **PHP OPcache**: Enable for better performance
- **CDN**: Consider using a CDN for static assets

#### Database (if needed)
```sql
-- Create database if using MySQL
CREATE DATABASE guideai CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 6. **Monitoring & Logging**

#### Error Logging
- Check `/logs/error.log` for PHP errors
- Monitor `/logs/debug.log` for debug information
- Set up log rotation

#### Performance Monitoring
- Monitor API response times
- Track error rates
- Monitor server resources

### 7. **Backup Strategy**

#### Regular Backups
```bash
# Backup script example
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
tar -czf backup_guideai_$DATE.tar.gz /path/to/guideai
```

#### Backup Schedule
- **Daily**: Configuration and logs
- **Weekly**: Full application backup
- **Monthly**: Complete system backup

### 8. **Security Hardening**

#### File Protection
```apache
# Protect sensitive files
<Files "config.php">
    Order allow,deny
    Deny from all
</Files>

<Files ".env">
    Order allow,deny
    Deny from all
</Files>
```

#### Rate Limiting
- Configure web server rate limiting
- Monitor for abuse
- Set up IP blocking if needed

### 9. **Testing Checklist**

#### Pre-Deployment Tests
- [ ] API keys working correctly
- [ ] All features functional
- [ ] Mobile responsiveness
- [ ] Accessibility features
- [ ] Error handling
- [ ] Security headers
- [ ] SSL certificate valid

#### Post-Deployment Tests
- [ ] Website loads correctly
- [ ] Chat functionality works
- [ ] Voice input/output working
- [ ] Resource links functional
- [ ] Error pages display properly
- [ ] Performance acceptable

### 10. **Maintenance**

#### Regular Tasks
- **Weekly**: Check error logs
- **Monthly**: Update dependencies
- **Quarterly**: Security audit
- **Annually**: Full system review

#### Update Process
```bash
# Safe update procedure
1. Backup current installation
2. Pull latest changes from git
3. Update dependencies: composer install
4. Test in staging environment
5. Deploy to production
6. Monitor for issues
```

## 🔧 Troubleshooting

### Common Issues

#### API Errors
- Check API key validity
- Verify rate limits
- Check network connectivity

#### Performance Issues
- Enable OPcache
- Optimize images
- Use CDN for static assets

#### Security Issues
- Review error logs
- Check for suspicious activity
- Update dependencies

## 📞 Support

For deployment issues:
1. Check the error logs in `/logs/`
2. Review this deployment guide
3. Check GitHub issues
4. Contact support team

## 🎯 Production Checklist

- [ ] API keys configured securely
- [ ] SSL certificate installed
- [ ] Directory permissions set correctly
- [ ] Error logging enabled
- [ ] Security headers configured
- [ ] Rate limiting enabled
- [ ] Backup strategy implemented
- [ ] Monitoring configured
- [ ] All tests passing
- [ ] Documentation updated

---

**Remember**: Security is paramount in production. Never commit API keys or sensitive configuration to version control! 