<script>
    function shareOnFacebook(foodName, description, location, pickupTime) {
        // Create the share text
        var shareText = "Check out this food available for pickup:\n\n";
        shareText += "Food: " + foodName + "\n";
        shareText += "Description: " + description + "\n";
        shareText += "Location: " + location + "\n";
        shareText += "Pickup by: " + pickupTime + "\n\n";
        shareText += "Help reduce food waste by sharing surplus food!";
        
        // URL to share (replace with your actual URL if you have one)
        var shareUrl = window.location.href;
		
		// Facebook share URL
        var facebookShareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + 
                              encodeURIComponent(shareUrl) + 
                              '&quote=' + encodeURIComponent(shareText);
        
        // Open the share dialog in a new window
        window.open(facebookShareUrl, 'facebook-share-dialog', 
                   'width=626,height=436,top=100,left=100');
    }
    </script>