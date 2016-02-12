(function ($) {
  $(document).ready(function () {
    var $form = $('#payment_form');
    $form.SecureSubmit({
      public_key: 'pkapi_cert_P6dRqs1LzfWJ6HgGVZ',
      type: 'iframe',
      iframeTarget: '#securesubmit',
      buttonTarget: '#PaymentButton',
      useDefaultStyles: false,
      onTokenSuccess: function (response) {
        $('#token_value').val(response.token_value);
        $form.submit();
      },
      onTokenError: function (response) {
        alert(response.message);
      }
    });
  });
  $(document).on('securesubmitIframeReady', function () {
    $('#securesubmit-iframe').addClass('col-sm-10');
    hps.setStyle('heartland-body',
      'margin: 0;' +
      'font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;' +
      'color: #000;'
    );
    $.each(['heartland-card-number-container', 'heartland-expiration-date-container', 'heartland-cvv-container'],
      function (i, v) {
        hps.setStyle(v,
          'margin-left:-15px;' +
          'margin-right:-15px;' +
          'margin-bottom:15px;' +
          'box-sizing:border-box;' +
          'display:block;' +
          'clear:both;'
        );
      });
    hps.appendStyle('heartland-expiration-date-container',
      'border:0;' +
      'padding:0;' +
      'width:100%;' +
      'float:left;'
    );
    $.each(['heartland-card-number', 'heartland-expiration-month', 'heartland-expiration-year', 'heartland-cvv'],
      function (i, v) {
        hps.setStyle(v,
          'font-family:inherit;' +
          'font-size:inherit;' +
          'line-height:inherit;' +
          'text-transform:none;' +
          'margin:0;'
        );
      });
    $.each(['heartland-card-number-label', 'heartland-expiration-date-legend', 'heartland-cvv-label'],
      function (i, v) {
        hps.setStyle(v,
          'font-size:13px;' +
          'font-weight:bold;' +
          'text-align:right;' +
          'margin-top:0;' +
          'margin-bottom:0;' +
          'padding-top:7px;' +
          'width:16.666666667%;' +
          'position: relative;' +
          'min-height:1px;' +
          'padding-left:15px;' +
          'padding-right:15px;' +
          'display:inline-block;' +
          'float:left;'
        );
      });
    hps.appendStyle('heartland-expiration-date-legend',
      'display:inline;' +
      'margin:0;'
    );
    $.each(['heartland-expiration-month-label', 'heartland-expiration-year-label'],
      function (i, v) {
        hps.setStyle(v, 'display:none;');
      });
  });
}(jQuery));
