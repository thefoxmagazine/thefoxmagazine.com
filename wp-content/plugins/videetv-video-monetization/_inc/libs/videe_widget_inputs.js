
jQuery(document).ready(function() {
    
    function escapeHTML(html) {
        return html
            .replace(/&/g, '&amp;')
            .replace(/>/g, '&gt;')
            .replace(/</g, '&lt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&apos;')
    }

    function UrlExists(url)
    {
        try {
            var http = new XMLHttpRequest();
            http.open('HEAD', url, false);
            http.send();
            return http.status!=404;
        } catch(e) {
            return false;
        }
    }
 
    
    function secondsToHMS(seconds){
            var h, m, s, minutes, hours;
            function zero(d) {return "" + (d<10 ? (d<=0 ? "00" : "0"+d) : d)}
            s = seconds%60
            minutes = (seconds - s)/60
            m = minutes%60
            h = (minutes - m)/60

            if (h) return h+":"+zero(m)+":"+zero(s)
            else   return m+":"+zero(s)
    }

    function initSelect() {

        var selectContainer = jQuery("select#videe_widget_playlist_id");

        selectContainer.siblings(".select2-container").remove();

        selectContainer.select2({
            ajax: {
                url: params.apiUrl + "playlists?auth_token=" + params.videeToken,
                dataType: 'json',
                delay: 250,
                data: function (query) {
                    return {
                        query: query.term, // search term
                        limit: 10,
                        start: (query.page) * 10 || 0
                    };
                },
                processResults: function (data, params) {

                    params.page = params.page || 1;

                    if(data.items.length > 0) {
                        showSettings();
                    }
                    return {
                        results: data.items,
                        pagination: {
                            more: (params.page * 10) < data.total
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) {
                return markup;
            },
            templateResult: function (playlist) {
                if (!playlist.id) return playlist.text
                var duration = 0;


                var html = "<div class='option-playlist'>"
                html += "<h4><span class='duration'>{{duration}}</span>" + escapeHTML(playlist.name) + "</h4>"
                html += "<div class='option-playlist-videos'>"
                playlist.videos && playlist.videos.forEach(function (video) {
                    var imageUrl = (video.coverCdnPath !== null) && UrlExists(video.coverCdnPath + video.image) ?
                    video.coverCdnPath + video.image : video.image;
					imageUrl = imageUrl.replace('video1source1.videe.tv','cdn-auth.videe.tv');
                    duration += video.length
                    html += "<img src='" + imageUrl + "' onerror='this.src=\""
					+ params.videePluginUrl + "_inc/images/no-preview-available.png\";this.onerror=\"\";' \
					class='option-playlist-videos' height='25' width='25' style='margin: 0 2px 2px 0;'>";
                })
                html = html.replace("{{duration}}", secondsToHMS(duration))
                return jQuery(html)
            },
            width: "250px"
        });
    }

    function showSettings()
    {
        jQuery(".accordion").each(function (i, container) {
            jQuery(container).show();
        });
    }


    function init() {
        initSelect();
        jQuery(".accordion").each(function (i, container) {
            toggleMute(container, false);
        });
    };

    init();
    
    jQuery(document).live('widget-added widget-updated', function () {
        init();
    });

    jQuery("input#videe_widget_volume_display").live('change input', function(){

        var container = jQuery(this).parents(".accordion");
        if( jQuery("input#videe_widget_mute", container).is(':checked')) {
            jQuery("input#videe_widget_volume_display", container).val(0);
        }
        toggleMute(container, true);
    });

    jQuery('input#videe_widget_mute').live('change', function() {

        var container = jQuery(this).parents(".accordion");
        toggleMute(container, false);
    });

    function toggleMute(container, updateVolume) {
        var volume = parseInt(jQuery("input#videe_widget_volume_display", container).val());
        jQuery(this).siblings(".volume-percents").text((this.value|0) + "%");

        if( jQuery("input#videe_widget_mute", container).is(':checked') && !updateVolume) {
            jQuery("#volume-icon", container).removeClass("dashicons-controls-volumeon").addClass("dashicons-controls-volumeoff");
            jQuery(".volume-percents", container).text(0 + "%");
            jQuery("input#videe_widget_volume_display", container).val(0);
        } else {

            if(updateVolume) {
                jQuery("input#videe_widget_volume", container).val(volume);
            } else {
                volume = jQuery("input#videe_widget_volume", container).val();
                jQuery("input#videe_widget_volume_display", container).val(volume);
            }

            if( volume === 0 ) {
                jQuery("#volume-icon", container).removeClass("dashicons-controls-volumeon").addClass("dashicons-controls-volumeoff");
            } else {
                jQuery("#volume-icon", container).removeClass("dashicons-controls-volumeoff").addClass("dashicons-controls-volumeon");
            }

            jQuery("input#videe_widget_mute", container).prop("checked", false);
            jQuery(".volume-percents", container).text(volume + "%");
        }
    }



    jQuery('.toggle').live('click', (function(e) {
        e.preventDefault();

        var $this = jQuery(this);

        if ($this.next().hasClass('show')) {
            $this.next().removeClass('show');
            $this.next().slideUp(350);
        } else {
            $this.parent().parent().find('li .inner').removeClass('show');
            $this.parent().parent().find('li .inner').slideUp(350);
            $this.next().toggleClass('show');
            $this.next().slideToggle(350);
        }
    }));


});
