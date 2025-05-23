/**
 * Text Visibility Diagnostic Script
 * This script helps to identify and fix any text that might be invisible due to color issues
 */
document.addEventListener('DOMContentLoaded', function() {
    // Function to check for problematic color combinations
    function checkTextVisibility() {
        console.log("Running text visibility check...");
        
        // Check all table cells
        const tableCells = document.querySelectorAll('table td, table th');
        tableCells.forEach(cell => {
            const cellStyle = window.getComputedStyle(cell);
            const textColor = cellStyle.color;
            const bgColor = cellStyle.backgroundColor;
            
            // Check if the text color is too close to background
            if (isColorSimilar(textColor, bgColor)) {
                console.warn("Potentially invisible text:", cell);
                cell.style.color = "#000000"; // Force black text color
                cell.style.backgroundColor = "#ffffff"; // Force white background
                
                // Add a warning border
                cell.style.border = "2px solid red";
            }
        });
        
        // Check all badges
        const badges = document.querySelectorAll('.badge');
        badges.forEach(badge => {
            const badgeStyle = window.getComputedStyle(badge);
            const textColor = badgeStyle.color;
            const bgColor = badgeStyle.backgroundColor;
            
            if (isColorSimilar(textColor, bgColor)) {
                console.warn("Potentially invisible badge:", badge);
                
                // Make sure the badge has visible text
                const badgeClasses = badge.className.split(' ');
                if (badgeClasses.includes('bg-warning')) {
                    badge.style.color = "#212529"; // Dark text for warning badges
                } else {
                    badge.style.color = "#ffffff"; // White text for other badges
                }
                
                // Add a warning border
                badge.style.border = "2px solid red";
            }
        });
    }
    
    // Helper function to determine if two colors are similar
    function isColorSimilar(color1, color2) {
        // Convert colors to RGB values
        const rgb1 = parseRGB(color1);
        const rgb2 = parseRGB(color2);
        
        if (!rgb1 || !rgb2) return false;
        
        // Calculate color difference using a simple formula
        const diff = Math.sqrt(
            Math.pow(rgb1.r - rgb2.r, 2) +
            Math.pow(rgb1.g - rgb2.g, 2) +
            Math.pow(rgb1.b - rgb2.b, 2)
        );
        
        // If the difference is small, colors are similar
        return diff < 125; // Threshold can be adjusted
    }
    
    // Helper to parse RGB string to object
    function parseRGB(color) {
        // Handle rgba format
        const rgbaMatch = color.match(/rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*[\d.]+)?\)/);
        if (rgbaMatch) {
            return {
                r: parseInt(rgbaMatch[1], 10),
                g: parseInt(rgbaMatch[2], 10),
                b: parseInt(rgbaMatch[3], 10)
            };
        }
        return null;
    }
    
    // Run the check after a short delay to ensure all styles are loaded
    setTimeout(checkTextVisibility, 1000);
});
