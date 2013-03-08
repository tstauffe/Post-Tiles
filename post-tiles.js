jQuery(document).ready(function($){
			
		  // This function repeats constantly to make sure the example post matches the current selected color 
		  // It repeats rapidly to update the color changes	
		  setInterval(function() {
		      // Reset Color
		      var color = '';
		      // Define X as the background color of the class "colorwell-selected" - Defined later on .focus
		      var x = $('.colorwell-selected').css('backgroundColor');
		     
		      // Apply the color value to the 
		      $('#example-post').css('backgroundColor', color);
		  
		  }, 100);
			
		  // Start declaring the variables for use in the colorwell function.
		  // This assigns the color farbtastic color picker to the div with the #picker	
      var f = $.farbtastic('#picker');
      // fade the picker.
      var p = $('#picker').css('opacity', 0.25);
      var selected;
      
      // Target the input fields with class .colorwell to change the value and the color
      $('.colorwell')
      	// Activate
        .each(function () { f.linkTo(this); $(this).css('opacity', 0.75); })
        // Upon .focus() start the color picking process for that input
        .focus(function() {
          if (selected) {
            $(selected).css('opacity', 0.75).removeClass('colorwell-selected');
          }
          f.linkTo(this);
          p.css('opacity', 1);
          $(selected = this).css('opacity', 1).addClass('colorwell-selected');
        
     }); 
              
});