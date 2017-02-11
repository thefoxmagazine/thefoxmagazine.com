jQuery(document).ready(function($){
	
	// mgl.settings.layout = "horizontal-slider";
	
  if(mgl.settings.layout == "masonry") {
    
    $('.gallery').addClass(mgl.settings.layout);
    
    var columns = mgl.settings.masonry.columns;
    
    $('.gallery-item').addClass('columns-'+columns);
		
		$('.gallery-item').css('padding', mgl.settings.masonry.gutter+"px");

    var $grid = $('.gallery').masonry( {
      // options
      percentPosition: true,
      itemSelector: '.gallery-item',
    } );

    $grid.imagesLoaded().progress( function() {
      $grid.masonry('layout');
    } ); 
    
  }
	
	if(mgl.settings.layout == "justified") {

		$('.gallery figure .gallery-icon a').addClass('gallery-item');

		$('.gallery figure').contents().unwrap();
		$('.gallery .gallery-icon').contents().unwrap();

		$('.gallery').removeClass('gallery-columns-1 gallery-columns-2 gallery-columns-3 gallery-columns-4 gallery-columns-5 gallery-columns-6');
		$('.gallery').addClass(mgl.settings.layout);

		console.log(mgl.settings);

		var parameters = {
			gridContainer: '.gallery',
			gridItems: '.gallery-item',
			gutter: mgl.settings.justified.gutter,
			enableImagesLoaded: true
		};

		var grid = new justifiedGrid(parameters);
		grid.initGrid();

	}
	
  	if(mgl.settings.layout == "horizontal-slider") {
		$('.gallery').addClass(mgl.settings.layout);
	}
  
});