
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
  
  (tarteaucitron.job = tarteaucitron.job || []).push('twittertimeline');
  tarteaucitron.user.analyticsUa = '{{ ga_tracking }}';
  tarteaucitron.user.analyticsMore = function () { /* add here your optionnal ga.push() */ };
  (tarteaucitron.job = tarteaucitron.job || []).push('analytics');
  (tarteaucitron.job = tarteaucitron.job || []).push('youtube');
  (tarteaucitron.job = tarteaucitron.job || []).push('facebookcomment');