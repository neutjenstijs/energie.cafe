(function () {
  // Mobiel menu
  var kop = document.getElementById('kop');
  var knop = kop && kop.querySelector('.menu-knop');
  if (knop) {
    knop.addEventListener('click', function () {
      var open = kop.classList.toggle('open');
      knop.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
    kop.querySelectorAll('nav a').forEach(function (a) {
      a.addEventListener('click', function () {
        kop.classList.remove('open');
        knop.setAttribute('aria-expanded', 'false');
      });
    });
  }

  // Na een formulierfout: focus op het eerste foute veld
  var fout = document.querySelector('.veld.fout input, .veld.fout textarea, [aria-invalid="true"]');
  if (fout) {
    setTimeout(function () { fout.focus({ preventScroll: false }); }, 50);
  }

  // Dubbel versturen voorkomen
  document.querySelectorAll('form').forEach(function (f) {
    f.addEventListener('submit', function () {
      var b = f.querySelector('button[type="submit"]');
      if (b && !f.getAttribute('onsubmit')) setTimeout(function () { b.disabled = true; }, 0);
    });
  });
})();
