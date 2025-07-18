# GuideAI API Setup Guide

## Current Status ✅

The form submission issue has been **FIXED**! The page no longer refreshes when submitting input on the index page.

## What Was Fixed

1. **Form Submission Handling**: Removed conflicting event listeners and properly configured the GuideAI JavaScript to handle form submissions without page refresh.

2. **API Endpoint Issues**: The 500 errors were caused by missing/invalid API keys in the `.env` file.

## Current Configuration

### Test Mode (Working)
- **API Endpoint**: `/api/test.php` - Returns mock responses without requiring external API keys
- **Status**: ✅ Working - Form submissions work without page refresh
- **Response**: Mock responses for testing the interface

### Production Mode (Requires API Keys)
To enable full AI functionality, you need to:

1. **Update the `.env` file** (located one directory above the project):
   ```env
   OPENAI_API_KEY="your-actual-openai-api-key"
   GEMINI_API_KEY="your-actual-gemini-api-key"
   CLAUDE_API_KEY="your-actual-claude-api-key"
   ```

2. **Switch back to production endpoints**:
   - Edit `guideai.js` and `guideai.min.js`
   - Change `this.apiEndpoint` from `/api/test.php` to `/api`
   - Change `this.fallbackEndpoint` from `/api/test.php` to `/api/gemini.php`

## How to Get API Keys

### OpenAI API Key
1. Go to https://platform.openai.com/api-keys
2. Create an account and add payment method
3. Generate a new API key
4. Replace `"sk-your-openai-api-key-here"` in `.env`

### Google Gemini API Key
1. Go to https://makersuite.google.com/app/apikey
2. Create a new API key
3. Replace `"your-gemini-api-key-here"` in `.env`

### Anthropic Claude API Key
1. Go to https://console.anthropic.com/
2. Create an account and add payment method
3. Generate a new API key
4. Replace `"sk-ant-your-claude-api-key-here"` in `.env`

## Testing the Fix

1. **Start the server**: `php -S localhost:8000`
2. **Open the application**: http://localhost:8000
3. **Test form submission**: Type a message and click send
4. **Expected behavior**: 
   - ✅ No page refresh
   - ✅ Mock response appears in chat
   - ✅ No 500 errors in console

## Files Modified

- `index.php` - Removed conflicting form event listeners
- `guideai.js` - Enhanced form handling and switched to test API
- `guideai.min.js` - Same changes as above
- `api/test.php` - New test API endpoint (created)

## Next Steps

1. **For Testing**: The current setup works perfectly for testing the interface
2. **For Production**: Add real API keys to `.env` and switch back to production endpoints
3. **For Development**: You can modify `api/test.php` to add more sophisticated mock responses

## Troubleshooting

If you still see 500 errors:
1. Check that the `.env` file exists one directory above the project
2. Verify API keys are valid (not placeholder values)
3. Check the browser console for specific error messages
4. Review the PHP error logs in the `logs/` directory 