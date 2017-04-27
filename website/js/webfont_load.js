  WebFont.load({
    custom: {
      families: ['Roboto:n3,i3,n4,n7,n9', 'Droid Serif:n4,i4,n7,i7'],
      urls: ['/scss/fonts.css']
    },
    active: function() {
      sessionStorage.font = true;
    }
  });
