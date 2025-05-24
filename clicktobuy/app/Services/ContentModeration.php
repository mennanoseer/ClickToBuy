<?php

namespace App\Services;

class ContentModeration
{
    /**
     * List of negative keywords to check for in reviews.
     *
     * @var array
     */
    protected static $negativeKeywords = [
        'terrible',
        'awful',
        'horrible',
        'disappointment',
        'worst',
        'waste',
        'regret',
        'rubbish',
        'useless',
        'refund',
        'scam',
        'defective',
        'broken',
        'poor quality',
        'not working',
        'false advertising',
        'misleading',
    ];
    
    /**
     * Check if content contains negative keywords.
     *
     * @param string $content
     * @return bool
     */
    public static function hasNegativeContent(string $content): bool
    {
        $content = strtolower($content);
        
        foreach (self::$negativeKeywords as $keyword) {
            if (str_contains($content, $keyword)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check if review ratings are negative (3 or below).
     *
     * @param int $rating
     * @return bool
     */
    public static function hasNegativeRating(int $rating): bool
    {
        return $rating <= 3;
    }
    
    /**
     * Calculate sentiment score of review content (basic implementation).
     * Returns a score between -1 (negative) and 1 (positive)
     *
     * @param string $content
     * @return float
     */
    public static function calculateSentimentScore(string $content): float
    {
        $content = strtolower($content);
        $wordCount = str_word_count($content);
        
        if ($wordCount === 0) {
            return 0;
        }
        
        $negativeCount = 0;
        foreach (self::$negativeKeywords as $keyword) {
            if (str_contains($content, $keyword)) {
                $negativeCount++;
            }
        }
        
        // Simple sentiment score calculation
        return 1 - (($negativeCount * 2) / $wordCount);
    }
}
