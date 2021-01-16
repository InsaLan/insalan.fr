
tarteaucitron.init({
  "privacyUrl": "/infos/cookiespolicy", /* Privacy policy url */

  "hashtag": "#tarteaucitron", /* Open the panel with this hashtag */
  "cookieName": "tarteaucitron", /* Cookie name */

  "orientation": "top", /* Banner position (top - bottom) */
  "showAlertSmall": false, /* Show the small banner on bottom right */
  "cookieslist": true, /* Show the cookie list */

  "adblocker": false, /* Show a Warning if an adblocker is detected */
  "AcceptAllCta" : true, /* Show the accept all button when highPrivacy on */
  "highPrivacy": true, /* Disable auto consent */
  "handleBrowserDNTRequest": true, /* If Do Not Track == 1, disallow all */

  "removeCredit": false, /* Remove credit link */
  "moreInfoLink": true, /* Show more info link */
  "useExternalCss": false, /* If false, the tarteaucitron.css file will be loaded */

  //"cookieDomain": ".my-multisite-domaine.fr", /* Shared cookie for multisite */
                    
  "readmoreLink": "/infos/cookiespolicy" /* Change the default readmore link */
  });
  

// ######### InsaLan custom services #########

// googledocs
tarteaucitron.services.googledocs = {
  "key": "googledocs",
  "type": "other",
  "name": "Google Docs",
  "uri": "https://policies.google.com/privacy",
  "needConsent": true,
  "cookies": [],
  "js": function () {
      "use strict";
      tarteaucitron.fallback(['googledocs'], function (x) {
          var width = x.getAttribute("width"),
              height = x.getAttribute("height"),
              url = x.getAttribute("data-url");

          return '<iframe src="' + url + '" width="' + width + '" height="' + height + '" frameborder="0"></iframe>';
          
      });
  },
  "fallback": function () {
      "use strict";
      var id = 'googledocs';
      tarteaucitron.fallback(['googledocs'], function (elem) {
          elem.style.width = elem.getAttribute('width') + 'px';
          elem.style.height = elem.getAttribute('height') + 'px';
          return tarteaucitron.engage(id);
      });
  }
};


// weezevent
tarteaucitron.services.weezevent = {
  "key": "weezevent",
  "type": "other",
  "name": "Weezevent",
  "uri": "https://weezevent.com/fr/cgv-weezticket/",
  "needConsent": true,
  "cookies": [],
  "js": function () {
      "use strict";
      tarteaucitron.fallback(['weezevent'], function (x) {
          var width = x.getAttribute("width"),
              height = x.getAttribute("height"),
              url = x.getAttribute("data-url");

          return '<iframe src="' + url + '" width="' + width + '" height="' + height + '" frameborder="0"></iframe>';
          
      });
  },
  "fallback": function () {
      "use strict";
      var id = 'weezevent';
      tarteaucitron.fallback(['weezevent'], function (elem) {
          elem.style.width = elem.getAttribute('width') + 'px';
          elem.style.height = elem.getAttribute('height') + 'px';
          return tarteaucitron.engage(id);
      });
  }
};


// twitch
tarteaucitron.services.twitchplayer = {
  "key": "twitchplayer",
  "type": "video",
  "name": "Twitch",
  "uri": "https://www.twitch.tv/p/legal/cookie-policy/",
  "needConsent": true,
  "cookies": [],
  "js": function () {
      "use strict";
      tarteaucitron.fallback(['twitchplayer'], function (x) {
          var url = x.getAttribute("data-url"),
              width = x.getAttribute("width"),
              height = x.getAttribute("height"),
              video_frame;

          video_frame = '<iframe type="text/html" width="' + width + '" height="' + height + '" src="' + url + '" frameborder="0" allowfullscreen></iframe>';
          return video_frame;
          
      });
  },
  "fallback": function () {
      "use strict";
      var id = 'twitchplayer';
      tarteaucitron.fallback(['twitchplayer'], function (elem) {
          elem.style.width = elem.getAttribute('width') + 'px';
          elem.style.height = elem.getAttribute('height') + 'px';
          return tarteaucitron.engage(id);
      });
  }
};

// youtube custom
tarteaucitron.services.youtubecustom = {
  "key": "youtubecustom",
  "type": "video",
  "name": "YouTube",
  "uri": "https://policies.google.com/privacy",
  "needConsent": true,
  "cookies": ['VISITOR_INFO1_LIVE', 'YSC', 'PREF', 'GEUP'],
  "js": function () {
      "use strict";
      tarteaucitron.fallback(['youtubecustom'], function (x) {
          var url = x.getAttribute("data-url"),
              width = x.getAttribute("width"),
              height = x.getAttribute("height"),
              video_frame;

          video_frame = '<iframe type="text/html" width="' + width + '" height="' + height + '" src="' + url + '" frameborder="0" allowfullscreen></iframe>';
          return video_frame;
      });
  },
  "fallback": function () {
      "use strict";
      var id = 'youtubecustom';
      tarteaucitron.fallback(['youtubecustom'], function (elem) {
          elem.style.width = elem.getAttribute('width') + 'px';
          elem.style.height = elem.getAttribute('height') + 'px';
          return tarteaucitron.engage(id);
      });
  }
};

// other player
tarteaucitron.services.otherplayer = {
  "key": "otherplayer",
  "type": "video",
  "name": "Autres lecteurs videos",
  "uri": "",
  "needConsent": true,
  "cookies": [],
  "js": function () {
      "use strict";
      tarteaucitron.fallback(['otherplayer'], function (x) {
          var url = x.getAttribute("data-url"),
          width = x.getAttribute("width"),
          height = x.getAttribute("height"),
          video_frame;

      video_frame = '<iframe type="text/html" width="' + width + '" height="' + height + '" src="' + url + '" frameborder="0" allowfullscreen></iframe>';
          return video_frame;
      });
  },
  "fallback": function () {
      "use strict";
      var id = 'otherplayer';
      tarteaucitron.fallback(['otherplayer'], function (elem) {
          elem.style.width = elem.getAttribute('width') + 'px';
          elem.style.height = elem.getAttribute('height') + 'px';
          return tarteaucitron.engage(id);
      });
  }
};

  (tarteaucitron.job = tarteaucitron.job || []).push('twittertimeline');
  tarteaucitron.user.analyticsUa = '{{ ga_tracking }}';
  tarteaucitron.user.analyticsMore = function () { /* add here your optionnal ga.push() */ };
  (tarteaucitron.job = tarteaucitron.job || []).push('analytics');
  (tarteaucitron.job = tarteaucitron.job || []).push('youtubecustom');
  (tarteaucitron.job = tarteaucitron.job || []).push('facebookcomment');
  (tarteaucitron.job = tarteaucitron.job || []).push('googledocs');
  (tarteaucitron.job = tarteaucitron.job || []).push('weezevent');
  (tarteaucitron.job = tarteaucitron.job || []).push('twitchplayer');
  (tarteaucitron.job = tarteaucitron.job || []).push('otherplayer');