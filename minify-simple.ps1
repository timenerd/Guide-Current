# Simple Minification Script for GuideAI
Write-Host "🚀 Creating minified versions of CSS and JS files..." -ForegroundColor Green

# Function to minify CSS
function Minify-CSS {
    param([string]$InputFile, [string]$OutputFile)
    
    Write-Host "📝 Minifying: $InputFile" -ForegroundColor Yellow
    
    try {
        $content = Get-Content $InputFile -Raw -Encoding UTF8
        
        # Remove CSS comments
        $content = $content -replace '/\*.*?\*/', ''
        
        # Remove extra whitespace and newlines
        $content = $content -replace '\s+', ' '
        
        # Clean up CSS rules
        $content = $content -replace ';\s*}', '}'
        $content = $content -replace '{\s*', '{'
        $content = $content -replace ';\s*', ';'
        $content = $content -replace ':\s*', ':'
        $content = $content -replace ',\s*', ','
        $content = $content -replace ';}', '}'
        
        # Trim whitespace
        $content = $content.Trim()
        
        # Save minified content
        $content | Out-File $OutputFile -Encoding UTF8 -NoNewline
        
        $originalSize = (Get-Item $InputFile).Length
        $minifiedSize = (Get-Item $OutputFile).Length
        $savings = [math]::Round((($originalSize - $minifiedSize) / $originalSize) * 100, 2)
        
        Write-Host "✅ Minified: $originalSize bytes -> $minifiedSize bytes ($savings percent smaller)" -ForegroundColor Green
        
    } catch {
        Write-Host "❌ Error minifying $InputFile : $_" -ForegroundColor Red
    }
}

# Function to minify JavaScript
function Minify-JS {
    param([string]$InputFile, [string]$OutputFile)
    
    Write-Host "📝 Minifying: $InputFile" -ForegroundColor Yellow
    
    try {
        $content = Get-Content $InputFile -Raw -Encoding UTF8
        
        # Remove single-line comments (but keep console.log for debugging)
        $content = $content -replace '(?m)^\s*//.*$', ''
        
        # Remove multi-line comments (but preserve license headers)
        $content = $content -replace '(?s)/\*(?!\!).*?\*/', ''
        
        # Remove extra whitespace and newlines
        $content = $content -replace '\s+', ' '
        
        # Clean up JavaScript
        $content = $content -replace ';\s*}', '}'
        $content = $content -replace '{\s*', '{'
        $content = $content -replace ';\s*', ';'
        $content = $content -replace ':\s*', ':'
        $content = $content -replace ',\s*', ','
        $content = $content -replace '\(\s*', '('
        $content = $content -replace '\s*\)', ')'
        $content = $content -replace '\[\s*', '['
        $content = $content -replace '\s*\]', ']'
        $content = $content -replace ';}', '}'
        
        # Trim whitespace
        $content = $content.Trim()
        
        # Save minified content
        $content | Out-File $OutputFile -Encoding UTF8 -NoNewline
        
        $originalSize = (Get-Item $InputFile).Length
        $minifiedSize = (Get-Item $OutputFile).Length
        $savings = [math]::Round((($originalSize - $minifiedSize) / $originalSize) * 100, 2)
        
        Write-Host "✅ Minified: $originalSize bytes -> $minifiedSize bytes ($savings percent smaller)" -ForegroundColor Green
        
    } catch {
        Write-Host "❌ Error minifying $InputFile : $_" -ForegroundColor Red
    }
}

# Minify CSS files
Write-Host "`n🎨 Minifying CSS files..." -ForegroundColor Cyan
Minify-CSS "assets/css/accessibility.css" "assets/css/accessibility.min.css"
Minify-CSS "assets/css/bilingual.css" "assets/css/bilingual.min.css"
Minify-CSS "ipt/public/css/style.css" "ipt/public/css/style.min.css"

# Minify JS files
Write-Host "`n⚡ Minifying JS files..." -ForegroundColor Cyan
Minify-JS "assets/js/accessibility.js" "assets/js/accessibility.min.js"

Write-Host "`n📊 Final file sizes:" -ForegroundColor Cyan
Write-Host "CSS Files:" -ForegroundColor White
Get-ChildItem "assets/css/*.css" | ForEach-Object { 
    $sizeKB = [math]::Round($_.Length / 1024, 2)
    Write-Host "  $($_.Name): ${sizeKB}KB" 
}

Write-Host "`nJS Files:" -ForegroundColor White
Get-ChildItem "assets/js/*.js" | ForEach-Object { 
    $sizeKB = [math]::Round($_.Length / 1024, 2)
    Write-Host "  $($_.Name): ${sizeKB}KB" 
}

Write-Host "`n✅ Minification complete!" -ForegroundColor Green
Write-Host "💡 To use minified files, update your HTML to reference .min.css and .min.js files" -ForegroundColor Yellow 