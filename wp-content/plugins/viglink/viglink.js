(function($) {
  $(document).ready( function() {
    var load_timeout = 3000,
        error_count = 0;

    var $link = $('#viglink-fetch');
    var $input = $('#viglink-key');

    var original_value = $input.val();

    var disable = function() {
      $link.addClass( 'loading' );
      $input.attr( 'disabled', true );
    };

    var enable = function() {
      $link.removeClass( 'loading' );
      $input.removeAttr( 'disabled' );
    };

    var error = function() {
      error_count++;

      $('.error.fade').remove();

      var error = $("<div/>").addClass( "error fade" ).html( $("<p/>").html(
        'We had some trouble retrieving your API key.\
        Please make sure you\'re logged in at \
        <a href="http://www.viglink.com/users/login">viglink.com</a>, then try again.'
      ) );

      if( error_count > 1 ) {
        error.append( $("<p/>").html(
          'If it still won\'t work, copy the API key from your\
          <a href="http://www.viglink.com/account">VigLink account page</a>\
          and paste it below.'
        ) );
      }

      $('.instructions').before( error );
      enable();
    };

    $link.click( function() {
      if( ! $input.attr( 'disabled' ) ) {
        disable();

        var timeout = setTimeout( function() {
          timeout = null;
          error();
        }, load_timeout );

        $.get( 'http://www.viglink.com/users/getKey', {}, function( data ) {
          if( ! timeout ) { return; }
          clearTimeout( timeout );
          setTimeout( function() {
            if( data.key ) {
              $input.val( data.key );
              if( data.key != original_value ) {
                setTimeout( function() {
                  $('.submit .reminder').show();
                }, 500 );
              } else {
                $('.submit .reminder').hide();
              }
              $('.error.fade').remove();
              enable();
            } else {
              error();
            }
          }, 500 );
        }, 'jsonp' );
      }
    } );
  } );
})(jQuery);
