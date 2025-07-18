# GuideAI Minification Script
# This script creates minified versions of CSS and JS files

Write-Host "🚀 Starting GuideAI file minification..." -ForegroundColor Green

# Function to minify CSS
function Minify-CSS {
    param([string]$InputFile, [string]$OutputFile)
    
    Write-Host "📝 Minifying CSS: $InputFile -> $OutputFile" -ForegroundColor Yellow
    
    try {
        $content = Get-Content $InputFile -Raw -Encoding UTF8
        
        # Remove comments (/* ... */)
        $content = $content -replace '/\*.*?\*/', '' -replace '(?s)/\*.*?\*/', ''
        
        # Remove extra whitespace and newlines
        $content = $content -replace '\s+', ' '
        $content = $content -replace ';\s*}', '}'
        $content = $content -replace '{\s*', '{'
        $content = $content -replace ';\s*', ';'
        $content = $content -replace ':\s*', ':'
        $content = $content -replace ',\s*', ','
        
        # Remove trailing semicolons before closing braces
        $content = $content -replace ';}', '}'
        
        # Trim whitespace
        $content = $content.Trim()
        
        # Save minified content
        $content | Out-File $OutputFile -Encoding UTF8 -NoNewline
        
        $originalSize = (Get-Item $InputFile).Length
        $minifiedSize = (Get-Item $OutputFile).Length
        $savings = [math]::Round((($originalSize - $minifiedSize) / $originalSize) * 100, 2)
        
        Write-Host "✅ CSS minified: $originalSize bytes -> $minifiedSize bytes (${savings}% smaller)" -ForegroundColor Green
        
    } catch {
        Write-Host "❌ Error minifying CSS $InputFile : $_" -ForegroundColor Red
    }
}

# Function to minify JavaScript
function Minify-JS {
    param([string]$InputFile, [string]$OutputFile)
    
    Write-Host "📝 Minifying JS: $InputFile -> $OutputFile" -ForegroundColor Yellow
    
    try {
        $content = Get-Content $InputFile -Raw -Encoding UTF8
        
        # Remove single-line comments (// ...)
        $content = $content -replace '//.*$', '' -replace '(?m)^\s*//.*$', ''
        
        # Remove multi-line comments (/* ... */) but preserve /*! ... */ (license headers)
        $content = $content -replace '(?s)/\*(?!\!).*?\*/', ''
        
        # Remove extra whitespace and newlines
        $content = $content -replace '\s+', ' '
        $content = $content -replace ';\s*}', '}'
        $content = $content -replace '{\s*', '{'
        $content = $content -replace ';\s*', ';'
        $content = $content -replace ':\s*', ':'
        $content = $content -replace ',\s*', ','
        $content = $content -replace '\(\s*', '('
        $content = $content -replace '\s*\)', ')'
        $content = $content -replace '\[\s*', '['
        $content = $content -replace '\s*\]', ']'
        
        # Remove trailing semicolons before closing braces
        $content = $content -replace ';}', '}'
        
        # Trim whitespace
        $content = $content.Trim()
        
        # Save minified content
        $content | Out-File $OutputFile -Encoding UTF8 -NoNewline
        
        $originalSize = (Get-Item $InputFile).Length
        $minifiedSize = (Get-Item $OutputFile).Length
        $savings = [math]::Round((($originalSize - $minifiedSize) / $originalSize) * 100, 2)
        
        Write-Host "✅ JS minified: $originalSize bytes -> $minifiedSize bytes (${savings}% smaller)" -ForegroundColor Green
        
    } catch {
        Write-Host "❌ Error minifying JS $InputFile : $_" -ForegroundColor Red
    }
}

# Create minified CSS files
Write-Host "`n🎨 Minifying CSS files..." -ForegroundColor Cyan

Minify-CSS "assets/css/guideai.css" "assets/css/guideai.min.css"
Minify-CSS "assets/css/accessibility.css" "assets/css/accessibility.min.css"
Minify-CSS "assets/css/bilingual.css" "assets/css/bilingual.min.css"
Minify-CSS "ipt/public/css/style.css" "ipt/public/css/style.min.css"

# Create minified JS files
Write-Host "`n⚡ Minifying JS files..." -ForegroundColor Cyan

Minify-JS "assets/js/accessibility.js" "assets/js/accessibility.min.js"

# Note: guideai.min.js already exists
Write-Host "ℹ️ guideai.min.js already exists, skipping..." -ForegroundColor Blue

Write-Host "`n🎉 Minification complete!" -ForegroundColor Green
Write-Host "📊 Summary of minified files:" -ForegroundColor Cyan

# Show file sizes
$files = @(
    "assets/css/guideai.css",
    "assets/css/guideai.min.css",
    "assets/css/accessibility.css", 
    "assets/css/accessibility.min.css",
    "assets/css/bilingual.css",
    "assets/css/bilingual.min.css",
    "ipt/public/css/style.css",
    "ipt/public/css/style.min.css",
    "assets/js/accessibility.js",
    "assets/js/accessibility.min.js",
    "guideai.min.js"
)

foreach ($file in $files) {
    if (Test-Path $file) {
        $size = (Get-Item $file).Length
        $sizeKB = [math]::Round($size / 1024, 2)
        Write-Host "  $file : ${sizeKB}KB" -ForegroundColor White
    }
}

Write-Host "`n💡 To use minified files, update your HTML to reference .min.css and .min.js files" -ForegroundColor Yellow
