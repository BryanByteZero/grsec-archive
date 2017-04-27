if(sessionStorage.font) {
  var l = document.createElement('link');
  l.rel = 'stylesheet';
  l.media = 'all';
  l.href = '/scss/fonts.css';
  document.head.appendChild(l);
  document.documentElement.classList.add('font-cached','wf-active');
}
