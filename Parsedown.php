<?php
/**
 * Simple Parsedown Implementation for GuideAI
 * Provides basic markdown to HTML conversion
 */

class Parsedown {
    private $safeMode = true;
    
    public function setSafeMode($safeMode) {
        $this->safeMode = $safeMode;
        return $this;
    }
    
    public function text($text) {
        if ($this->safeMode) {
            $text = $this->sanitizeText($text);
        }
        
        return $this->convertMarkdown($text);
    }
    
    private function sanitizeText($text) {
        // Remove potentially dangerous HTML
        $text = strip_tags($text);
        return $text;
    }
    
    private function convertMarkdown($text) {
        // Convert headers - more robust patterns
        $text = preg_replace('/^###\s+(.+)$/m', '<h3>$1</h3>', $text);
        $text = preg_replace('/^##\s+(.+)$/m', '<h2>$1</h2>', $text);
        $text = preg_replace('/^#\s+(.+)$/m', '<h1>$1</h1>', $text);
        
        // Convert bold and italic
        $text = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', $text);
        $text = preg_replace('/\*(.*?)\*/s', '<em>$1</em>', $text);
        
        // Convert code blocks
        $text = preg_replace('/```(.*?)```/s', '<pre><code>$1</code></pre>', $text);
        $text = preg_replace('/`(.*?)`/s', '<code>$1</code>', $text);
        
        // Convert lists
        $text = preg_replace('/^\* (.*$)/m', '<li>$1</li>', $text);
        $text = preg_replace('/^- (.*$)/m', '<li>$1</li>', $text);
        
        // Wrap consecutive list items in <ul> tags
        $text = preg_replace('/(<li>.*<\/li>)/s', '<ul>$1</ul>', $text);
        
        // Convert line breaks
        $text = str_replace("\n\n", '</p><p>', $text);
        $text = '<p>' . $text . '</p>';
        $text = str_replace('<p></p>', '', $text);
        
        // Convert single line breaks to <br>
        $text = str_replace("\n", '<br>', $text);
        
        return $text;
    }
}
?>
