window.addEvent('domready', function() {
  // Close button for alert messages
  $$('.alert-error, .alert-info, .alert-success').each(function(e) {
    var button = new Element('button', {
      type: 'button',
      html: '&times;',
      events: {
        click: function() {
          var fx = new Fx.Tween(this.parentNode, {
            property: 'opacity',
            onComplete: function() {
              this.parentNode.dispose();
            }.bind(this)
          });

          fx.start(1, 0);
        }
      }
    });

    button.inject(e, 'top');
  });
});
