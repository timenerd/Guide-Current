@echo off
echo 🚀 Starting GuideAI file minification...

echo.
echo 📝 Creating minified CSS files...

REM Minify guideai.css
echo Processing guideai.css...
powershell -Command "(Get-Content 'assets/css/guideai.css' -Raw) -replace '/\*.*?\*/', '' -replace '\s+', ' ' -replace ';\s*}', '}' -replace '{\s*', '{' -replace ';\s*', ';' -replace ':\s*', ':' -replace ',\s*', ',' -replace ';}', '}' | Out-File 'assets/css/guideai.min.css' -Encoding UTF8 -NoNewline"

REM Minify accessibility.css
echo Processing accessibility.css...
powershell -Command "(Get-Content 'assets/css/accessibility.css' -Raw) -replace '/\*.*?\*/', '' -replace '\s+', ' ' -replace ';\s*}', '}' -replace '{\s*', '{' -replace ';\s*', ';' -replace ':\s*', ':' -replace ',\s*', ',' -replace ';}', '}' | Out-File 'assets/css/accessibility.min.css' -Encoding UTF8 -NoNewline"

REM Minify bilingual.css
echo Processing bilingual.css...
powershell -Command "(Get-Content 'assets/css/bilingual.css' -Raw) -replace '/\*.*?\*/', '' -replace '\s+', ' ' -replace ';\s*}', '}' -replace '{\s*', '{' -replace ';\s*', ';' -replace ':\s*', ':' -replace ',\s*', ',' -replace ';}', '}' | Out-File 'assets/css/bilingual.min.css' -Encoding UTF8 -NoNewline"

REM Minify ipt style.css
echo Processing ipt/public/css/style.css...
powershell -Command "(Get-Content 'ipt/public/css/style.css' -Raw) -replace '/\*.*?\*/', '' -replace '\s+', ' ' -replace ';\s*}', '}' -replace '{\s*', '{' -replace ';\s*', ';' -replace ':\s*', ':' -replace ',\s*', ',' -replace ';}', '}' | Out-File 'ipt/public/css/style.min.css' -Encoding UTF8 -NoNewline"

echo.
echo ⚡ Creating minified JS files...

REM Minify accessibility.js
echo Processing accessibility.js...
powershell -Command "(Get-Content 'assets/js/accessibility.js' -Raw) -replace '//.*$', '' -replace '(?m)^\s*//.*$', '' -replace '(?s)/\*(?!\!).*?\*/', '' -replace '\s+', ' ' -replace ';\s*}', '}' -replace '{\s*', '{' -replace ';\s*', ';' -replace ':\s*', ':' -replace ',\s*', ',' -replace '\(\s*', '(' -replace '\s*\)', ')' -replace '\[\s*', '[' -replace '\s*\]', ']' -replace ';}', '}' | Out-File 'assets/js/accessibility.min.js' -Encoding UTF8 -NoNewline"

echo.
echo 📊 File sizes:
echo.
for %%f in (assets/css/guideai.css assets/css/guideai.min.css assets/css/accessibility.css assets/css/accessibility.min.css assets/css/bilingual.css assets/css/bilingual.min.css ipt/public/css/style.css ipt/public/css/style.min.css assets/js/accessibility.js assets/js/accessibility.min.js guideai.min.js) do (
    if exist "%%f" (
        for %%A in ("%%f") do echo %%~zA bytes - %%f
    )
)

echo.
echo ✅ Minification complete!
echo 💡 Update your HTML files to use .min.css and .min.js files for production 