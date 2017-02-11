(function($){
    "use strict";

    function initializeGoogleMap(){
        $('.tt-google-map').each(function(){
            var $gmap = $(this);
            var _id = $gmap.attr('id');
            var _lat = $gmap.data('lat');
            var _lng = $gmap.data('lng');
            var _color = $gmap.data('color');
            var _sat = $gmap.data('saturation');
            var _zoom = $gmap.data('zoom');
            var _marker = $gmap.data('marker');

            var styles = [
                {
                    stylers: [
                        { hue: _color },
                        { saturation: _sat }
                    ]
                },
                {
                    featureType: "road",
                    elementType: "geometry",
                    stylers: [
                        { lightness: 20 },
                        { visibility: "simplified" }
                    ]
                },
                {
                    featureType: "road",
                    elementType: "labels",
                    stylers: [
                        { visibility: "off" }
                    ]
                }
            ];

            var styledMap = new google.maps.StyledMapType(styles, {name: "Styled Map"});
            var curLatlng = new google.maps.LatLng(_lat, _lng);

            var mapOptions = {
                zoom: _zoom,
                center: curLatlng,
                mapTypeControlOptions: {
                    mapTypeIds: [google.maps.MapTypeId.ROADMAP, 'map_style']
                },
                scrollwheel: false,
                draggable: false,
                disableDefaultUI :true,
                streetViewControl: false
            };
            var map = new google.maps.Map(document.getElementById(_id), mapOptions);

            var marker = new google.maps.Marker({
                position: curLatlng,
                title: "Themeton: Google Map",
                map: map,
                icon: _marker
            });
            marker.setMap(map);

            map.mapTypes.set('map_style', styledMap);
            map.setMapTypeId('map_style');

        });
    }

    $(window).load(function(){
        // init google map
        setTimeout(function(){
            initializeGoogleMap();
        }, 500);
    });

})(jQuery);